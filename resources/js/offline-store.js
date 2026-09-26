/**
 * AgriTrack Offline Store & Sync Manager
 * Using browser IndexedDB for offline-first data persistence.
 */

const DB_NAME = 'AgriTrackDB';
const DB_VERSION = 4;

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

                // Object store for crop activities cache & drafts
                if (!db.objectStoreNames.contains('crop_activities')) {
                    const actStore = db.createObjectStore('crop_activities', { keyPath: 'client_id' });
                    actStore.createIndex('crop_id', 'crop_id', { unique: false });
                    actStore.createIndex('is_synced', 'is_synced', { unique: false });
                }

                // Object store for keuangan transactions
                if (!db.objectStoreNames.contains('keuangan_transactions')) {
                    const trxStore = db.createObjectStore('keuangan_transactions', { keyPath: 'client_id' });
                    trxStore.createIndex('raw_id', 'raw_id', { unique: false });
                    trxStore.createIndex('is_synced', 'is_synced', { unique: false });
                }

                // Object store for keuangan categories
                if (!db.objectStoreNames.contains('keuangan_categories')) {
                    db.createObjectStore('keuangan_categories', { keyPath: 'client_id' });
                }

                // Object store for sync queue (mutations made while offline)
                if (!db.objectStoreNames.contains('sync_queue')) {
                    db.createObjectStore('sync_queue', { keyPath: 'queue_id', autoIncrement: true });
                }

                // Object store for land preparation steps
                if (!db.objectStoreNames.contains('land_preparation_steps')) {
                    const landStore = db.createObjectStore('land_preparation_steps', { keyPath: 'client_id' });
                    landStore.createIndex('id', 'id', { unique: false });
                    landStore.createIndex('is_synced', 'is_synced', { unique: false });
                }

                // Object store for planting seeds
                if (!db.objectStoreNames.contains('planting_seeds')) {
                    const seedStore = db.createObjectStore('planting_seeds', { keyPath: 'client_id' });
                    seedStore.createIndex('id', 'id', { unique: false });
                    seedStore.createIndex('is_synced', 'is_synced', { unique: false });
                }

                // Object store for planting steps
                if (!db.objectStoreNames.contains('planting_steps')) {
                    const plantStepStore = db.createObjectStore('planting_steps', { keyPath: 'client_id' });
                    plantStepStore.createIndex('id', 'id', { unique: false });
                    plantStepStore.createIndex('planting_seed_id', 'planting_seed_id', { unique: false });
                    plantStepStore.createIndex('is_synced', 'is_synced', { unique: false });
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
                    pending.filter(q => q.action === 'create' && (!q.type || q.type === 'medicine')).map(q => q.local_id)
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
            type: 'medicine',
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

        const req = medStore.get('srv_' + id);
        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { id: id, client_id: 'srv_' + id };
                const updated = { ...current, ...data, is_synced: false };
                medStore.put(updated);
                queueStore.add({
                    type: 'medicine',
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
                type: 'medicine',
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
     * Add a crop activity while offline in Kalender HST
     */
    async addCropActivityOffline(cropId, data) {
        const tx = await this.getTransaction(['crop_activities', 'sync_queue'], 'readwrite');
        const actStore = tx.objectStore('crop_activities');
        const queueStore = tx.objectStore('sync_queue');

        const localId = 'offline_act_' + Date.now();
        const record = {
            ...data,
            id: null,
            crop_id: parseInt(cropId, 10),
            client_id: localId,
            is_synced: false,
            created_at: new Date().toISOString()
        };

        actStore.put(record);
        queueStore.add({
            type: 'crop_activity',
            action: 'create',
            local_id: localId,
            crop_id: parseInt(cropId, 10),
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
     * Get offline draft activities for a specific crop
     */
    async getOfflineCropActivities(cropId) {
        try {
            const tx = await this.getTransaction('crop_activities', 'readonly');
            const store = tx.objectStore('crop_activities');
            return new Promise((resolve) => {
                const req = store.getAll();
                req.onsuccess = () => {
                    const list = (req.result || []).filter(item => item.crop_id === parseInt(cropId, 10));
                    resolve(list);
                };
                req.onerror = () => resolve([]);
            });
        } catch (e) {
            return [];
        }
    }

    /**
     * Delete an offline crop activity draft or queue deletion
     */
    async deleteCropActivityOffline(activityId, clientId) {
        const tx = await this.getTransaction(['crop_activities', 'sync_queue'], 'readwrite');
        const actStore = tx.objectStore('crop_activities');
        const queueStore = tx.objectStore('sync_queue');

        if (clientId) {
            actStore.delete(clientId);
        }

        if (activityId) {
            queueStore.add({
                type: 'crop_activity',
                action: 'delete',
                crop_id: null,
                data: { id: activityId },
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
                // No pending mutations, fetch fresh server snapshot for medicines
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

            // Group mutations by type
            const medicineMutations = mutations.filter(m => !m.type || m.type === 'medicine');
            const hstMutations = mutations.filter(m => m.type === 'crop_activity' || m.type === 'kalender_hst');
            const keuanganMutations = mutations.filter(m => m.type === 'keuangan');
            const landMutations = mutations.filter(m => m.type === 'land_step');
            const plantingMutations = mutations.filter(m => m.type === 'planting_step' || m.type === 'planting_seed');

            let totalProcessed = 0;

            // Sync medicines if any
            if (medicineMutations.length > 0) {
                const medRes = await fetch('/api/medicines/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ mutations: medicineMutations })
                });

                if (medRes.ok) {
                    const medResult = await medRes.json();
                    if (medResult.success) {
                        totalProcessed += (medResult.processed_count || medicineMutations.length);
                        if (medResult.medicines) {
                            await this.cacheServerMedicines(medResult.medicines);
                        }
                    }
                }
            }

            // Sync Kalender HST activities if any
            if (hstMutations.length > 0) {
                const hstRes = await fetch('/api/kalender-hst/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ mutations: hstMutations })
                });

                if (hstRes.ok) {
                    const hstResult = await hstRes.json();
                    if (hstResult.success) {
                        totalProcessed += (hstResult.processed_count || hstMutations.length);
                        // Clear draft crop_activities store
                        const actTx = await this.getTransaction('crop_activities', 'readwrite');
                        actTx.objectStore('crop_activities').clear();
                        await new Promise((r) => { actTx.oncomplete = r; });
                    }
                }
            }

            // Sync Keuangan transactions & categories if any
            if (keuanganMutations.length > 0) {
                const kRes = await fetch('/api/keuangan/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ mutations: keuanganMutations })
                });

                if (kRes.ok) {
                    const kResult = await kRes.json();
                    if (kResult.success) {
                        totalProcessed += (kResult.processed_count || keuanganMutations.length);
                        const kSnap = await fetch('/api/keuangan');
                        if (kSnap.ok) {
                            const kJson = await kSnap.json();
                            if (kJson.success) {
                                await this.cacheKeuanganData(kJson.transactions, kJson.categories);
                            }
                        }
                    }
                }
            }

            // Sync Pengolahan Tanah (Land Preparation Steps) if any
            if (landMutations.length > 0) {
                const lRes = await fetch('/api/steps/pengolahan-tanah/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ mutations: landMutations })
                });

                if (lRes.ok) {
                    const lResult = await lRes.json();
                    if (lResult.success) {
                        totalProcessed += (lResult.processed_count || landMutations.length);
                        if (lResult.steps) {
                            await this.cacheLandPreparationSteps(lResult.steps);
                        }
                    }
                }
            }

            // Sync Penanaman Bibit (Planting Seeds & Steps) if any
            if (plantingMutations.length > 0) {
                const pRes = await fetch('/api/steps/penanaman-bibit/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ mutations: plantingMutations })
                });

                if (pRes.ok) {
                    const pResult = await pRes.json();
                    if (pResult.success) {
                        totalProcessed += (pResult.processed_count || plantingMutations.length);
                        if (pResult.seeds) {
                            await this.cachePlantingData(pResult.seeds);
                        }
                    }
                }
            }

            // Clear the sync queue
            const clearTx = await this.getTransaction('sync_queue', 'readwrite');
            clearTx.objectStore('sync_queue').clear();
            await new Promise((r) => { clearTx.oncomplete = r; });

            this.isSyncing = false;
            window.dispatchEvent(new CustomEvent('agri:sync-status', { detail: { status: 'online', pending: 0 } }));
            window.dispatchEvent(new CustomEvent('agri:sync-success', {
                detail: { count: totalProcessed || mutations.length }
            }));

            return { synced: true, count: totalProcessed || mutations.length };
        } catch (err) {
            console.warn('Sync failed, will retry later:', err);
            this.isSyncing = false;
            window.dispatchEvent(new CustomEvent('agri:sync-status', { detail: { status: 'error', error: err.message } }));
            return { synced: false, error: err.message };
        }
    }

    /**
     * Cache Keuangan data snapshot in IndexedDB
     */
    async cacheKeuanganData(transactions = [], categories = []) {
        try {
            const tx = await this.getTransaction(['keuangan_transactions', 'keuangan_categories', 'sync_queue'], 'readwrite');
            const trxStore = tx.objectStore('keuangan_transactions');
            const catStore = tx.objectStore('keuangan_categories');
            const queueStore = tx.objectStore('sync_queue');

            const pendingQueueReq = queueStore.getAll();
            return new Promise((resolve) => {
                pendingQueueReq.onsuccess = () => {
                    const pending = pendingQueueReq.result || [];
                    const pendingTrxLocalIds = new Set(
                        pending.filter(q => q.type === 'keuangan' && q.action === 'create_transaction').map(q => q.local_id)
                    );
                    const pendingCatLocalIds = new Set(
                        pending.filter(q => q.type === 'keuangan' && q.action === 'create_category').map(q => q.local_id)
                    );

                    // Clear synced transactions & put fresh
                    const clearTrxReq = trxStore.openCursor();
                    clearTrxReq.onsuccess = (e) => {
                        const cursor = e.target.result;
                        if (cursor) {
                            if (!pendingTrxLocalIds.has(cursor.value.client_id)) {
                                cursor.delete();
                            }
                            cursor.continue();
                        } else {
                            transactions.forEach((t) => {
                                trxStore.put({
                                    ...t,
                                    client_id: 'srv_' + (t.raw_id || t.id),
                                    is_synced: true
                                });
                            });
                        }
                    };

                    // Clear synced categories & put fresh
                    const clearCatReq = catStore.openCursor();
                    clearCatReq.onsuccess = (e) => {
                        const cursor = e.target.result;
                        if (cursor) {
                            if (!pendingCatLocalIds.has(cursor.value.client_id)) {
                                cursor.delete();
                            }
                            cursor.continue();
                        } else {
                            categories.forEach((c) => {
                                catStore.put({
                                    ...c,
                                    client_id: 'srv_' + c.id,
                                    is_synced: true
                                });
                            });
                            resolve(true);
                        }
                    };
                };
                pendingQueueReq.onerror = () => resolve(false);
            });
        } catch (e) {
            console.warn('cacheKeuanganData error:', e);
            return false;
        }
    }

    /**
     * Read Keuangan data from offline IndexedDB
     */
    async getOfflineKeuanganData(periode = 'semua') {
        try {
            const tx = await this.getTransaction(['keuangan_transactions', 'keuangan_categories'], 'readonly');
            const trxStore = tx.objectStore('keuangan_transactions');
            const catStore = tx.objectStore('keuangan_categories');

            const allTrx = await new Promise((res) => {
                const req = trxStore.getAll();
                req.onsuccess = () => res(req.result || []);
                req.onerror = () => res([]);
            });

            const allCats = await new Promise((res) => {
                const req = catStore.getAll();
                req.onsuccess = () => res(req.result || []);
                req.onerror = () => res([]);
            });

            const now = new Date();
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth();

            const filteredTrx = allTrx.filter(item => {
                if (periode === 'semua') return true;
                if (!item.tanggal && !item.tanggal_raw) return true;
                const d = new Date(item.tanggal_raw || item.tanggal);
                if (isNaN(d.getTime())) return true;

                if (periode === 'tahun_ini') {
                    return d.getFullYear() === currentYear;
                }
                if (periode === 'bulan_ini') {
                    return d.getFullYear() === currentYear && d.getMonth() === currentMonth;
                }
                return true;
            }).sort((a, b) => {
                const da = new Date(a.tanggal_raw || a.tanggal || 0);
                const db = new Date(b.tanggal_raw || b.tanggal || 0);
                return db - da;
            });

            let totalPemasukan = 0;
            let totalPengeluaran = 0;
            let jumlahPemasukan = 0;
            let jumlahPengeluaran = 0;

            filteredTrx.forEach(t => {
                const nom = parseFloat(t.nominal) || 0;
                if (t.tipe === 'pemasukan') {
                    totalPemasukan += nom;
                    jumlahPemasukan++;
                } else {
                    totalPengeluaran += nom;
                    jumlahPengeluaran++;
                }
            });

            const saldoBersih = totalPemasukan - totalPengeluaran;
            const formatRupiah = (num) => 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            return {
                success: true,
                is_offline: true,
                periode: periode,
                totalPemasukan: totalPemasukan,
                formatted_total_pemasukan: formatRupiah(totalPemasukan),
                totalPengeluaran: totalPengeluaran,
                formatted_total_pengeluaran: formatRupiah(totalPengeluaran),
                saldoBersih: saldoBersih,
                formatted_saldo_bersih: formatRupiah(saldoBersih),
                transaksiList: filteredTrx,
                totalTransaksi: filteredTrx.length,
                jumlahPemasukan: jumlahPemasukan,
                jumlahPengeluaran: jumlahPengeluaran,
                categories: allCats
            };
        } catch (e) {
            console.error('getOfflineKeuanganData error:', e);
            return null;
        }
    }

    async addKeuanganTransactionOffline(data) {
        const tx = await this.getTransaction(['keuangan_transactions', 'sync_queue'], 'readwrite');
        const trxStore = tx.objectStore('keuangan_transactions');
        const queueStore = tx.objectStore('sync_queue');

        const localId = 'offline_trx_' + Date.now();
        const nominal = parseInt((data.nominal || '0').toString().replace(/[^0-9]/g, ''), 10) || 0;
        const formatRupiah = (num) => 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        const record = {
            id: localId,
            raw_id: null,
            client_id: localId,
            source: 'manual',
            tipe: data.tipe || 'pengeluaran',
            kategori: data.kategori || 'Lainnya',
            sub_kategori: data.sub_kategori || null,
            kategori_label: data.sub_kategori ? `${data.kategori} › ${data.sub_kategori}` : (data.kategori || 'Lainnya'),
            judul: data.judul || 'Transaksi Offline',
            nominal: nominal,
            formatted_nominal: formatRupiah(nominal),
            tanggal: data.tanggal || new Date().toISOString().split('T')[0],
            tanggal_raw: data.tanggal || new Date().toISOString().split('T')[0],
            formatted_tanggal: data.tanggal || 'Hari ini',
            crop_id: data.crop_id || null,
            keterangan: data.keterangan || null,
            deskripsi: data.keterangan || 'Offline (Belum Sinkron)',
            nota_url: data.foto_nota_base64 || data.foto_base64 || null,
            is_synced: false,
            is_offline: true,
            created_at: new Date().toISOString()
        };

        trxStore.put(record);
        queueStore.add({
            type: 'keuangan',
            action: 'create_transaction',
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

    async updateKeuanganTransactionOffline(id, data) {
        const tx = await this.getTransaction(['keuangan_transactions', 'sync_queue'], 'readwrite');
        const trxStore = tx.objectStore('keuangan_transactions');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(id).startsWith('offline_trx_') ? id : ('srv_' + id);
        const req = trxStore.get(targetKey);

        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { client_id: targetKey, id: id };
                const nominal = data.nominal ? parseInt(data.nominal.toString().replace(/[^0-9]/g, ''), 10) : current.nominal;
                const formatRupiah = (num) => 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                const updated = {
                    ...current,
                    ...data,
                    nominal: nominal,
                    formatted_nominal: formatRupiah(nominal),
                    is_synced: false
                };
                trxStore.put(updated);
                queueStore.add({
                    type: 'keuangan',
                    action: 'update_transaction',
                    data: { raw_id: current.raw_id || id, ...data },
                    timestamp: Date.now()
                });
            };

            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }

    async deleteKeuanganTransactionOffline(id) {
        const tx = await this.getTransaction(['keuangan_transactions', 'sync_queue'], 'readwrite');
        const trxStore = tx.objectStore('keuangan_transactions');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(id).startsWith('offline_trx_') ? id : ('srv_' + id);
        trxStore.delete(targetKey);

        if (!String(id).startsWith('offline_trx_')) {
            queueStore.add({
                type: 'keuangan',
                action: 'delete_transaction',
                data: { raw_id: id },
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

    async addKeuanganCategoryOffline(data) {
        const tx = await this.getTransaction(['keuangan_categories', 'sync_queue'], 'readwrite');
        const catStore = tx.objectStore('keuangan_categories');
        const queueStore = tx.objectStore('sync_queue');

        const localId = 'offline_cat_' + Date.now();
        const record = {
            id: localId,
            client_id: localId,
            nama: data.nama,
            tipe: data.tipe,
            parent_id: data.parent_id || null,
            subcategories: [],
            is_synced: false
        };

        catStore.put(record);
        queueStore.add({
            type: 'keuangan',
            action: 'create_category',
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

    async deleteKeuanganCategoryOffline(id) {
        const tx = await this.getTransaction(['keuangan_categories', 'sync_queue'], 'readwrite');
        const catStore = tx.objectStore('keuangan_categories');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(id).startsWith('offline_cat_') ? id : ('srv_' + id);
        catStore.delete(targetKey);

        if (!String(id).startsWith('offline_cat_')) {
            queueStore.add({
                type: 'keuangan',
                action: 'delete_category',
                data: { id: id },
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

    // ══════════════════════════════════════════════════════════════════
    // PENGOLAHAN TANAH (LAND PREPARATION STEPS) OFFLINE METHODS
    // ══════════════════════════════════════════════════════════════════

    async cacheLandPreparationSteps(steps = []) {
        const tx = await this.getTransaction(['land_preparation_steps', 'sync_queue'], 'readwrite');
        const landStore = tx.objectStore('land_preparation_steps');
        const queueStore = tx.objectStore('sync_queue');

        const pendingQueueReq = queueStore.getAll();
        return new Promise((resolve) => {
            pendingQueueReq.onsuccess = () => {
                const pending = pendingQueueReq.result || [];
                const localIds = new Set(
                    pending.filter(q => q.type === 'land_step' && q.action === 'create').map(q => q.local_id)
                );

                const clearReq = landStore.openCursor();
                clearReq.onsuccess = (e) => {
                    const cursor = e.target.result;
                    if (cursor) {
                        if (!localIds.has(cursor.value.client_id) && !String(cursor.value.client_id).startsWith('offline_land_')) {
                            cursor.delete();
                        }
                        cursor.continue();
                    } else {
                        steps.forEach((step) => {
                            landStore.put({
                                ...step,
                                client_id: 'srv_' + step.id,
                                is_synced: true
                            });
                        });
                        resolve(true);
                    }
                };
            };
        });
    }

    async getAllLandPreparationSteps() {
        const tx = await this.getTransaction('land_preparation_steps', 'readonly');
        const store = tx.objectStore('land_preparation_steps');
        return new Promise((resolve) => {
            const req = store.getAll();
            req.onsuccess = () => {
                const results = req.result || [];
                results.sort((a, b) => {
                    const urutanA = typeof a.urutan === 'number' ? a.urutan : 999;
                    const urutanB = typeof b.urutan === 'number' ? b.urutan : 999;
                    if (urutanA !== urutanB) return urutanA - urutanB;
                    return String(a.nomor || '').localeCompare(String(b.nomor || ''), undefined, { numeric: true });
                });
                resolve(results);
            };
            req.onerror = () => resolve([]);
        });
    }

    async addLandPreparationStepOffline(data) {
        const tx = await this.getTransaction(['land_preparation_steps', 'sync_queue'], 'readwrite');
        const landStore = tx.objectStore('land_preparation_steps');
        const queueStore = tx.objectStore('sync_queue');

        const localId = 'offline_land_' + Date.now();
        const rawNomor = (data.nomor || '1').toString().trim();
        const numPart = parseInt(rawNomor.replace(/[^0-9]/g, ''), 10);
        const urutan = !isNaN(numPart) && numPart > 0 ? numPart : 999;

        const photosBase64 = data.photos_base64 || [];
        const photoItems = photosBase64.map(b64 => ({
            url: b64,
            public_id: null,
            storage_type: 'offline_base64'
        }));

        const record = {
            id: localId,
            client_id: localId,
            nomor: rawNomor,
            urutan: urutan,
            judul: data.judul || 'Langkah Baru',
            waktu: data.waktu || null,
            deskripsi: data.deskripsi || '-',
            tips: data.tips || null,
            spesifikasi: null,
            foto: photoItems,
            foto_urls: photosBase64,
            foto_count: photosBase64.length,
            is_synced: false
        };

        landStore.put(record);
        queueStore.add({
            type: 'land_step',
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

    async updateLandPreparationStepOffline(id, data) {
        const tx = await this.getTransaction(['land_preparation_steps', 'sync_queue'], 'readwrite');
        const landStore = tx.objectStore('land_preparation_steps');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(id).startsWith('offline_land_') ? id : ('srv_' + id);
        const req = landStore.get(targetKey);

        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { client_id: targetKey, id: id };
                let existingPhotos = Array.isArray(current.foto) ? current.foto : [];

                if (Array.isArray(data.deleted_photos) && data.deleted_photos.length > 0) {
                    const toDelete = new Set(data.deleted_photos);
                    existingPhotos = existingPhotos.filter(p => {
                        const url = (p && typeof p === 'object') ? (p.url || p.public_id) : p;
                        return !toDelete.has(url);
                    });
                }

                const newBase64 = data.photos_base64 || [];
                const newPhotoItems = newBase64.map(b64 => ({
                    url: b64,
                    public_id: null,
                    storage_type: 'offline_base64'
                }));

                const allPhotos = [...existingPhotos, ...newPhotoItems];
                const allUrls = allPhotos.map(p => (p && typeof p === 'object') ? (p.url || '') : String(p));

                const rawNomor = (data.nomor || current.nomor || '1').toString().trim();
                const numPart = parseInt(rawNomor.replace(/[^0-9]/g, ''), 10);
                const urutan = !isNaN(numPart) && numPart > 0 ? numPart : (current.urutan || 999);

                const updated = {
                    ...current,
                    ...data,
                    nomor: rawNomor,
                    urutan: urutan,
                    foto: allPhotos,
                    foto_urls: allUrls,
                    foto_count: allPhotos.length,
                    is_synced: false
                };

                landStore.put(updated);
                queueStore.add({
                    type: 'land_step',
                    action: 'update',
                    data: { id: current.id || id, ...data },
                    timestamp: Date.now()
                });
            };

            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }

    async deleteLandPreparationStepOffline(id) {
        const tx = await this.getTransaction(['land_preparation_steps', 'sync_queue'], 'readwrite');
        const landStore = tx.objectStore('land_preparation_steps');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(id).startsWith('offline_land_') ? id : ('srv_' + id);
        landStore.delete(targetKey);

        if (!String(id).startsWith('offline_land_')) {
            queueStore.add({
                type: 'land_step',
                action: 'delete',
                data: { id: id },
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

    async uploadLandPreparationPhotosOffline(stepId, photosBase64) {
        const tx = await this.getTransaction(['land_preparation_steps', 'sync_queue'], 'readwrite');
        const landStore = tx.objectStore('land_preparation_steps');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(stepId).startsWith('offline_land_') ? stepId : ('srv_' + stepId);
        const req = landStore.get(targetKey);

        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { client_id: targetKey, id: stepId };
                const existingPhotos = Array.isArray(current.foto) ? current.foto : [];
                const newItems = (photosBase64 || []).map(b64 => ({
                    url: b64,
                    public_id: null,
                    storage_type: 'offline_base64'
                }));
                const allPhotos = [...existingPhotos, ...newItems];
                const allUrls = allPhotos.map(p => (p && typeof p === 'object') ? (p.url || '') : String(p));

                const updated = {
                    ...current,
                    foto: allPhotos,
                    foto_urls: allUrls,
                    foto_count: allPhotos.length,
                    is_synced: false
                };
                landStore.put(updated);

                queueStore.add({
                    type: 'land_step',
                    action: 'upload_photos',
                    data: { step_id: current.id || stepId, photos_base64: photosBase64 },
                    timestamp: Date.now()
                });
            };

            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }

    async deleteLandPreparationPhotoOffline(stepId, photoUrl) {
        const tx = await this.getTransaction(['land_preparation_steps', 'sync_queue'], 'readwrite');
        const landStore = tx.objectStore('land_preparation_steps');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(stepId).startsWith('offline_land_') ? stepId : ('srv_' + stepId);
        const req = landStore.get(targetKey);

        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { client_id: targetKey, id: stepId };
                const existingPhotos = Array.isArray(current.foto) ? current.foto : [];
                const remaining = existingPhotos.filter(p => {
                    const u = (p && typeof p === 'object') ? (p.url || p.public_id) : p;
                    return u !== photoUrl;
                });
                const allUrls = remaining.map(p => (p && typeof p === 'object') ? (p.url || '') : String(p));

                const updated = {
                    ...current,
                    foto: remaining,
                    foto_urls: allUrls,
                    foto_count: remaining.length,
                    is_synced: false
                };
                landStore.put(updated);

                if (!String(stepId).startsWith('offline_land_')) {
                    queueStore.add({
                        type: 'land_step',
                        action: 'delete_photo',
                        data: { step_id: stepId, photo_url: photoUrl },
                        timestamp: Date.now()
                    });
                }
            };

            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }

    // ══════════════════════════════════════════════════════════════════
    // PENANAMAN BIBIT (SEEDS & PLANTING STEPS) OFFLINE METHODS
    // ══════════════════════════════════════════════════════════════════

    async cachePlantingData(seeds = []) {
        const tx = await this.getTransaction(['planting_seeds', 'planting_steps', 'sync_queue'], 'readwrite');
        const seedStore = tx.objectStore('planting_seeds');
        const stepStore = tx.objectStore('planting_steps');
        const queueStore = tx.objectStore('sync_queue');

        const pendingQueueReq = queueStore.getAll();
        return new Promise((resolve) => {
            pendingQueueReq.onsuccess = () => {
                const pending = pendingQueueReq.result || [];
                const pendingSeedLocalIds = new Set(
                    pending.filter(q => q.type === 'planting_seed' && q.action === 'create_seed').map(q => q.local_id)
                );
                const pendingStepLocalIds = new Set(
                    pending.filter(q => q.type === 'planting_step' && q.action === 'create_step').map(q => q.local_id)
                );

                // Clean synced seeds
                const clearSeedReq = seedStore.openCursor();
                clearSeedReq.onsuccess = (e) => {
                    const cursor = e.target.result;
                    if (cursor) {
                        if (!pendingSeedLocalIds.has(cursor.value.client_id) && !String(cursor.value.client_id).startsWith('offline_seed_')) {
                            cursor.delete();
                        }
                        cursor.continue();
                    } else {
                        // Clean synced steps
                        const clearStepReq = stepStore.openCursor();
                        clearStepReq.onsuccess = (e2) => {
                            const cursor2 = e2.target.result;
                            if (cursor2) {
                                if (!pendingStepLocalIds.has(cursor2.value.client_id) && !String(cursor2.value.client_id).startsWith('offline_pstep_')) {
                                    cursor2.delete();
                                }
                                cursor2.continue();
                            } else {
                                // Put fresh seeds & their steps
                                seeds.forEach(seed => {
                                    seedStore.put({
                                        id: seed.id,
                                        client_id: 'srv_' + seed.id,
                                        nama_bibit: seed.nama_bibit,
                                        varietas: seed.varietas,
                                        deskripsi: seed.deskripsi,
                                        urutan: seed.urutan,
                                        is_synced: true
                                    });

                                    const steps = seed.steps || [];
                                    steps.forEach(st => {
                                        stepStore.put({
                                            ...st,
                                            client_id: 'srv_' + st.id,
                                            planting_seed_id: seed.id,
                                            is_synced: true
                                        });
                                    });
                                });
                                resolve(true);
                            }
                        };
                    }
                };
            };
        });
    }

    async getAllPlantingSeedsWithSteps() {
        const tx = await this.getTransaction(['planting_seeds', 'planting_steps'], 'readonly');
        const seedStore = tx.objectStore('planting_seeds');
        const stepStore = tx.objectStore('planting_steps');

        const [seeds, steps] = await Promise.all([
            new Promise((res) => {
                const req = seedStore.getAll();
                req.onsuccess = () => res(req.result || []);
                req.onerror = () => res([]);
            }),
            new Promise((res) => {
                const req = stepStore.getAll();
                req.onsuccess = () => res(req.result || []);
                req.onerror = () => res([]);
            })
        ]);

        // Group steps by seed
        const stepsBySeed = {};
        steps.forEach(st => {
            const sid = String(st.planting_seed_id);
            if (!stepsBySeed[sid]) stepsBySeed[sid] = [];
            stepsBySeed[sid].push(st);
        });

        // Sort steps for each seed
        Object.keys(stepsBySeed).forEach(sid => {
            stepsBySeed[sid].sort((a, b) => {
                const urutanA = typeof a.urutan === 'number' ? a.urutan : 999;
                const urutanB = typeof b.urutan === 'number' ? b.urutan : 999;
                if (urutanA !== urutanB) return urutanA - urutanB;
                return String(a.nomor || '').localeCompare(String(b.nomor || ''), undefined, { numeric: true });
            });
        });

        // Attach steps to seed objects
        const results = seeds.map(seed => {
            const seedKey = String(seed.id);
            const rawKey = String(seed.client_id).replace(/^srv_/, '');
            const seedSteps = stepsBySeed[seedKey] || stepsBySeed[rawKey] || [];
            return {
                ...seed,
                steps: seedSteps
            };
        });

        // Sort seeds
        results.sort((a, b) => {
            const urutanA = typeof a.urutan === 'number' ? a.urutan : 999;
            const urutanB = typeof b.urutan === 'number' ? b.urutan : 999;
            if (urutanA !== urutanB) return urutanA - urutanB;
            return String(a.nama_bibit || '').localeCompare(String(b.nama_bibit || ''));
        });

        return results;
    }

    async addPlantingSeedOffline(data) {
        const tx = await this.getTransaction(['planting_seeds', 'sync_queue'], 'readwrite');
        const seedStore = tx.objectStore('planting_seeds');
        const queueStore = tx.objectStore('sync_queue');

        const localId = 'offline_seed_' + Date.now();
        const record = {
            id: localId,
            client_id: localId,
            nama_bibit: data.nama_bibit || 'Bibit Baru',
            varietas: data.varietas || '',
            deskripsi: data.deskripsi || '',
            urutan: 999,
            steps: [],
            is_synced: false
        };

        seedStore.put(record);
        queueStore.add({
            type: 'planting_seed',
            action: 'create_seed',
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

    async updatePlantingSeedOffline(id, data) {
        const tx = await this.getTransaction(['planting_seeds', 'sync_queue'], 'readwrite');
        const seedStore = tx.objectStore('planting_seeds');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(id).startsWith('offline_seed_') ? id : ('srv_' + id);
        const req = seedStore.get(targetKey);

        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { client_id: targetKey, id: id };
                const updated = {
                    ...current,
                    ...data,
                    is_synced: false
                };
                seedStore.put(updated);
                queueStore.add({
                    type: 'planting_seed',
                    action: 'update_seed',
                    data: { id: current.id || id, ...data },
                    timestamp: Date.now()
                });
            };

            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }

    async deletePlantingSeedOffline(id) {
        const tx = await this.getTransaction(['planting_seeds', 'planting_steps', 'sync_queue'], 'readwrite');
        const seedStore = tx.objectStore('planting_seeds');
        const stepStore = tx.objectStore('planting_steps');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(id).startsWith('offline_seed_') ? id : ('srv_' + id);
        seedStore.delete(targetKey);

        // Delete associated steps
        const stepCursorReq = stepStore.openCursor();
        stepCursorReq.onsuccess = (e) => {
            const cursor = e.target.result;
            if (cursor) {
                if (String(cursor.value.planting_seed_id) === String(id) || String(cursor.value.planting_seed_id) === String(targetKey)) {
                    cursor.delete();
                }
                cursor.continue();
            }
        };

        if (!String(id).startsWith('offline_seed_')) {
            queueStore.add({
                type: 'planting_seed',
                action: 'delete_seed',
                data: { id: id },
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

    async addPlantingStepOffline(seedId, data) {
        const tx = await this.getTransaction(['planting_steps', 'sync_queue'], 'readwrite');
        const stepStore = tx.objectStore('planting_steps');
        const queueStore = tx.objectStore('sync_queue');

        const localId = 'offline_pstep_' + Date.now();
        const rawNomor = (data.nomor || '1').toString().trim();
        const numPart = parseInt(rawNomor.replace(/[^0-9]/g, ''), 10);
        const urutan = !isNaN(numPart) && numPart > 0 ? numPart : 999;

        const photosBase64 = data.photos_base64 || [];
        const photoItems = photosBase64.map(b64 => ({
            url: b64,
            public_id: null,
            storage_type: 'offline_base64'
        }));

        const record = {
            id: localId,
            client_id: localId,
            planting_seed_id: seedId,
            nomor: rawNomor,
            urutan: urutan,
            judul: data.judul || 'Langkah Baru',
            waktu: data.waktu || null,
            deskripsi: data.deskripsi || '-',
            tips: data.tips || null,
            foto: photoItems,
            foto_urls: photosBase64,
            foto_count: photosBase64.length,
            is_synced: false
        };

        stepStore.put(record);
        queueStore.add({
            type: 'planting_step',
            action: 'create_step',
            local_id: localId,
            data: { planting_seed_id: seedId, ...data },
            timestamp: Date.now()
        });

        return new Promise((resolve) => {
            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(record);
            };
        });
    }

    async updatePlantingStepOffline(stepId, data) {
        const tx = await this.getTransaction(['planting_steps', 'sync_queue'], 'readwrite');
        const stepStore = tx.objectStore('planting_steps');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(stepId).startsWith('offline_pstep_') ? stepId : ('srv_' + stepId);
        const req = stepStore.get(targetKey);

        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { client_id: targetKey, id: stepId };
                let existingPhotos = Array.isArray(current.foto) ? current.foto : [];

                if (Array.isArray(data.deleted_photos) && data.deleted_photos.length > 0) {
                    const toDelete = new Set(data.deleted_photos);
                    existingPhotos = existingPhotos.filter(p => {
                        const url = (p && typeof p === 'object') ? (p.url || p.public_id) : p;
                        return !toDelete.has(url);
                    });
                }

                const newBase64 = data.photos_base64 || [];
                const newPhotoItems = newBase64.map(b64 => ({
                    url: b64,
                    public_id: null,
                    storage_type: 'offline_base64'
                }));

                const allPhotos = [...existingPhotos, ...newPhotoItems];
                const allUrls = allPhotos.map(p => (p && typeof p === 'object') ? (p.url || '') : String(p));

                const rawNomor = (data.nomor || current.nomor || '1').toString().trim();
                const numPart = parseInt(rawNomor.replace(/[^0-9]/g, ''), 10);
                const urutan = !isNaN(numPart) && numPart > 0 ? numPart : (current.urutan || 999);

                const updated = {
                    ...current,
                    ...data,
                    nomor: rawNomor,
                    urutan: urutan,
                    foto: allPhotos,
                    foto_urls: allUrls,
                    foto_count: allPhotos.length,
                    is_synced: false
                };

                stepStore.put(updated);
                queueStore.add({
                    type: 'planting_step',
                    action: 'update_step',
                    data: { id: current.id || stepId, ...data },
                    timestamp: Date.now()
                });
            };

            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }

    async deletePlantingStepOffline(stepId) {
        const tx = await this.getTransaction(['planting_steps', 'sync_queue'], 'readwrite');
        const stepStore = tx.objectStore('planting_steps');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(stepId).startsWith('offline_pstep_') ? stepId : ('srv_' + stepId);
        stepStore.delete(targetKey);

        if (!String(stepId).startsWith('offline_pstep_')) {
            queueStore.add({
                type: 'planting_step',
                action: 'delete_step',
                data: { id: stepId },
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

    async uploadPlantingStepPhotosOffline(stepId, photosBase64) {
        const tx = await this.getTransaction(['planting_steps', 'sync_queue'], 'readwrite');
        const stepStore = tx.objectStore('planting_steps');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(stepId).startsWith('offline_pstep_') ? stepId : ('srv_' + stepId);
        const req = stepStore.get(targetKey);

        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { client_id: targetKey, id: stepId };
                const existingPhotos = Array.isArray(current.foto) ? current.foto : [];
                const newItems = (photosBase64 || []).map(b64 => ({
                    url: b64,
                    public_id: null,
                    storage_type: 'offline_base64'
                }));
                const allPhotos = [...existingPhotos, ...newItems];
                const allUrls = allPhotos.map(p => (p && typeof p === 'object') ? (p.url || '') : String(p));

                const updated = {
                    ...current,
                    foto: allPhotos,
                    foto_urls: allUrls,
                    foto_count: allPhotos.length,
                    is_synced: false
                };
                stepStore.put(updated);

                queueStore.add({
                    type: 'planting_step',
                    action: 'upload_step_photos',
                    data: { step_id: current.id || stepId, photos_base64: photosBase64 },
                    timestamp: Date.now()
                });
            };

            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }

    async deletePlantingStepPhotoOffline(stepId, photoUrl) {
        const tx = await this.getTransaction(['planting_steps', 'sync_queue'], 'readwrite');
        const stepStore = tx.objectStore('planting_steps');
        const queueStore = tx.objectStore('sync_queue');

        const targetKey = String(stepId).startsWith('offline_pstep_') ? stepId : ('srv_' + stepId);
        const req = stepStore.get(targetKey);

        return new Promise((resolve) => {
            req.onsuccess = () => {
                const current = req.result || { client_id: targetKey, id: stepId };
                const existingPhotos = Array.isArray(current.foto) ? current.foto : [];
                const remaining = existingPhotos.filter(p => {
                    const u = (p && typeof p === 'object') ? (p.url || p.public_id) : p;
                    return u !== photoUrl;
                });
                const allUrls = remaining.map(p => (p && typeof p === 'object') ? (p.url || '') : String(p));

                const updated = {
                    ...current,
                    foto: remaining,
                    foto_urls: allUrls,
                    foto_count: remaining.length,
                    is_synced: false
                };
                stepStore.put(updated);

                if (!String(stepId).startsWith('offline_pstep_')) {
                    queueStore.add({
                        type: 'planting_step',
                        action: 'delete_step_photo',
                        data: { step_id: stepId, photo_url: photoUrl },
                        timestamp: Date.now()
                    });
                }
            };

            tx.oncomplete = () => {
                window.dispatchEvent(new CustomEvent('agri:data-changed'));
                resolve(true);
            };
        });
    }
}

export const offlineStore = new AgriOfflineStore();
window.AgriOfflineStore = offlineStore;
