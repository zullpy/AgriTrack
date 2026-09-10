/**
 * AgriTrack Offline Store & Sync Manager
 * Using browser IndexedDB for offline-first data persistence.
 */

const DB_NAME = 'AgriTrackDB';
const DB_VERSION = 1;

class AgriOfflineStore {
    constructor() {
        this.db = null;
        this.isSyncing = false;
    }

    async init() {
        if (this.db) return this.db;

        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = (e) => {
                const db = e.target.result;

                // Object store for medicines cache
                if (!db.objectStoreNames.contains('medicines')) {
                    const medicineStore = db.createObjectStore('medicines', { keyPath: 'client_id' });
                    medicineStore.createIndex('id', 'id', { unique: false });
                    medicineStore.createIndex('is_synced', 'is_synced', { unique: false });
                }

                // Object store for sync queue (mutations made while offline)
                if (!db.objectStoreNames.contains('sync_queue')) {
                    db.createObjectStore('sync_queue', { keyPath: 'queue_id', autoIncrement: true });
                }
            };

            request.onsuccess = (e) => {
                this.db = e.target.result;
                resolve(this.db);
            };

            request.onerror = (e) => {
                console.error('IndexedDB open error:', e);
                reject(e);
            };
        });
    }

    async getTransaction(storeName, mode = 'readonly') {
        await this.init();
        return this.db.transaction(storeName, mode);
    }

    /**
     * Cache a list of medicines from server into local IndexedDB
     */
    async cacheServerMedicines(medicines) {
        const tx = await this.getTransaction(['medicines', 'sync_queue'], 'readwrite');
        const medStore = tx.objectStore('medicines');
        const queueStore = tx.objectStore('sync_queue');

        // Check if we have pending local creations to preserve
        const pendingQueueReq = queueStore.getAll();

        return new Promise((resolve) => {
            pendingQueueReq.onsuccess = () => {
                const pending = pendingQueueReq.result || [];
                const localIds = new Set(
                    pending.filter(q => q.action === 'create').map(q => q.local_id)
                );

                // Clear synced medicines and rewrite with fresh server data
                const clearReq = medStore.openCursor();
                clearReq.onsuccess = (e) => {
                    const cursor = e.target.result;
                    if (cursor) {
                        if (!localIds.has(cursor.value.client_id)) {
                            cursor.delete();
                        }
                        cursor.continue();
                    } else {
                        // Put new medicines
                        medicines.forEach((m) => {
                            medStore.put({
                                ...m,
                                client_id: 'srv_' + m.id,
                                is_synced: true
                            });
                        });
                        resolve(true);
                    }
                };
            };
        });
    }

    /**
     * Get all medicines (both synced and offline pending)
     */
    async getAllMedicines() {
        const tx = await this.getTransaction('medicines', 'readonly');
        const store = tx.objectStore('medicines');
        return new Promise((resolve) => {
            const req = store.getAll();
            req.onsuccess = () => {
                // Sort latest first
                const list = (req.result || []).sort((a, b) => {
                    return (b.id || 0) - (a.id || 0);
                });
                resolve(list);
            };
            req.onerror = () => resolve([]);
        });
    }

    /**
     * Add a new medicine while offline
     */
    async addMedicineOffline(data) {
        const tx = await this.getTransaction(['medicines', 'sync_queue'], 'readwrite');
        const medStore = tx.objectStore('medicines');
        const queueStore = tx.objectStore('sync_queue');

        const localId = 'offline_' + Date.now();
        const record = {
            ...data,
            id: null,
            client_id: localId,
            is_synced: false,
            created_at: new Date().toISOString()
        };

        medStore.put(record);
        queueStore.add({
            action: 'create',
            local_id: localId,
            data: data,
            timestamp: Date.now()
        });

        return new Promise((resolve) => {
            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(record);
            };
        });
    }

    /**
     * Update medicine while offline
     */
    async updateMedicineOffline(id, data) {
        const tx = await this.getTransaction(['medicines', 'sync_queue'], 'readwrite');
        const medStore = tx.objectStore('medicines');
        const queueStore = tx.objectStore('sync_queue');

        // Look up item
        const req = medStore.get('srv_' + id);
        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { id: id, client_id: 'srv_' + id };
                const updated = { ...current, ...data, is_synced: false };
                medStore.put(updated);
                queueStore.add({
                    action: 'update',
                    data: { id, ...data },
                    timestamp: Date.now()
                });
            };

            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }

    /**
     * Delete medicine while offline
     */
    async deleteMedicineOffline(id, clientId) {
        const tx = await this.getTransaction(['medicines', 'sync_queue'], 'readwrite');
        const medStore = tx.objectStore('medicines');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = clientId || ('srv_' + id);
        medStore.delete(targetKey);

        if (id) {
            queueStore.add({
                action: 'delete',
                data: { id },
                timestamp: Date.now()
            });
        }

        return new Promise((resolve) => {
            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }

    /**
     * Get pending sync items count
     */
    async getPendingCount() {
        const tx = await this.getTransaction('sync_queue', 'readonly');
        const store = tx.objectStore('sync_queue');
        return new Promise((resolve) => {
            const req = store.count();
            req.onsuccess = () => resolve(req.result || 0);
            req.onerror = () => resolve(0);
        });
    }

    /**
     * Synchronize pending offline mutations to the Laravel server
     */
    async syncAll() {
        if (!navigator.onLine || this.isSyncing) {
            return { synced: false, reason: 'offline_or_busy' };
        }

        this.isSyncing = true;
        window.dispatchEvent(new CustomEvent('agri:sync-status', { detail: { status: 'syncing' } }));

        try {
            const tx = await this.getTransaction('sync_queue', 'readonly');
            const store = tx.objectStore('sync_queue');
            const queueReq = store.getAll();

            const mutations = await new Promise((resolve) => {
                queueReq.onsuccess = () => resolve(queueReq.result || []);
                queueReq.onerror = () => resolve([]);
            });

            if (mutations.length === 0) {
                // No pending mutations, fetch fresh server snapshot
                const res = await fetch('/api/medicines');
                if (res.ok) {
                    const json = await res.json();
                    if (json.success && json.data) {
                        await this.cacheServerMedicines(json.data);
                    }
                }
                this.isSyncing = false;
                window.dispatchEvent(new CustomEvent('agri:sync-status', { detail: { status: 'online', pending: 0 } }));
                return { synced: true, count: 0 };
            }

            // Send mutations to server sync endpoint
            const response = await fetch('/api/medicines/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ mutations })
            });

            if (!response.ok) {
                throw new Error('Sync endpoint responded with status ' + response.status);
            }

            const result = await response.json();

            if (result.success) {
                // Clear the sync queue and update local cache with fresh server medicines
                const clearTx = await this.getTransaction('sync_queue', 'readwrite');
                clearTx.objectStore('sync_queue').clear();
                await new Promise((r) => { clearTx.oncomplete = r; });

                if (result.medicines) {
                    await this.cacheServerMedicines(result.medicines);
                }

                this.isSyncing = false;
                window.dispatchEvent(new CustomEvent('agri:sync-status', { detail: { status: 'online', pending: 0 } }));
                window.dispatchEvent(new CustomEvent('agri:sync-success', {
                    detail: { count: result.processed_count || mutations.length }
                }));

                return { synced: true, count: result.processed_count || mutations.length };
            } else {
                throw new Error(result.message || 'Gagal sinkronisasi');
            }
        } catch (err) {
            console.warn('Sync failed, will retry later:', err);
            this.isSyncing = false;
            window.dispatchEvent(new CustomEvent('agri:sync-status', { detail: { status: 'error', error: err.message } }));
            return { synced: false, error: err.message };
        }
    }
}

export const offlineStore = new AgriOfflineStore();
window.AgriOfflineStore = offlineStore;
