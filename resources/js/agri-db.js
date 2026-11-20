/**
 * AgriTrack Dexie.js Client Database & Offline-First Sync Layer
 * Manages local IndexedDB storage with UUIDs and sync statuses: pending, synced, failed.
 */

// Initialize Dexie instance
const createAgriDb = () => {
    if (typeof window.Dexie === 'undefined') {
        console.warn('Dexie.js is not loaded yet. Waiting for script...');
    }

    const DexieClass = window.Dexie || class DummyDexie {};
    const db = new DexieClass('AgriTrackDB_v4');

    if (typeof db.version === 'function') {
        db.version(1).stores({
            medicines: 'uuid, server_id, nama, jenis, sync_status, created_at, updated_at',
            medicine_purchases: 'uuid, server_id, medicine_id, medicine_uuid, sync_status, created_at',
            crops: 'uuid, server_id, nama_tanaman, status, sync_status, created_at, updated_at',
            crop_activities: 'uuid, server_id, crop_id, crop_uuid, status, sync_status, created_at, updated_at',
            crop_harvests: 'uuid, server_id, crop_id, crop_uuid, sync_status, created_at, updated_at',
            financial_transactions: 'uuid, server_id, tipe, kategori, crop_id, sync_status, created_at, updated_at',
            financial_categories: 'uuid, server_id, parent_id, tipe, sync_status, created_at, updated_at',
            land_preparation_steps: 'uuid, server_id, nomor, urutan, sync_status, created_at, updated_at',
            planting_seeds: 'uuid, server_id, urutan, sync_status, created_at, updated_at',
            planting_steps: 'uuid, server_id, planting_seed_id, seed_uuid, nomor, urutan, sync_status, created_at, updated_at',
            fertilizer_fermentation_steps: 'uuid, server_id, nomor, urutan, sync_status, created_at, updated_at',
            plant_catalogs: 'uuid, server_id, key, sync_status, created_at, updated_at',
            plant_catalog_hamas: 'uuid, server_id, plant_catalog_id, catalog_uuid, sync_status, created_at, updated_at',
            step_notes: 'uuid, server_id, sync_status, created_at, updated_at',
            sync_queue: '++id, uuid, table, action, sync_status, created_at'
        });
    }

    return db;
};

export const agriDb = createAgriDb();
window.agriDb = agriDb;

/**
 * Universal Form & Inline Error Helpers
 */
export const AgriFormHelper = {
    /**
     * Generate RFC4122 v4 UUID
     */
    generateUUID() {
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
            const r = (Math.random() * 16) | 0;
            const v = c === 'x' ? r : (r & 0x3) | 0x8;
            return v.toString(16);
        });
    },

    /**
     * Clear all inline validation error messages from a form
     */
    clearErrors(form) {
        if (!form) return;
        const errorElements = form.querySelectorAll('.field-error-msg');
        errorElements.forEach((el) => el.remove());

        const erroredInputs = form.querySelectorAll('.is-invalid-input');
        erroredInputs.forEach((input) => {
            input.classList.remove('is-invalid-input', 'border-rose-500', 'bg-rose-50/50', 'focus:border-rose-500', 'focus:ring-rose-500/20');
        });
    },

    /**
     * Display validation error messages directly underneath input fields
     * @param {HTMLFormElement} form
     * @param {Object} errors - e.g. { field_name: ['Pesan error 1', ...] }
     */
    showErrors(form, errors) {
        if (!form || !errors || typeof errors !== 'object') return;

        this.clearErrors(form);

        let firstInvalidField = null;

        Object.keys(errors).forEach((fieldKey) => {
            const messages = Array.isArray(errors[fieldKey]) ? errors[fieldKey] : [errors[fieldKey]];
            if (messages.length === 0) return;

            // Resolve target input by name or id (handling dot notation e.g. purchases.0.harga -> purchases[0][harga])
            let input = form.querySelector(`[name="${fieldKey}"]`) || form.querySelector(`#${fieldKey}`);

            if (!input && fieldKey.includes('.')) {
                const parts = fieldKey.split('.');
                const bracketName = parts[0] + parts.slice(1).map(p => `[${p}]`).join('');
                input = form.querySelector(`[name="${bracketName}"]`);
            }

            if (!input) {
                // Try fuzzy match
                const bareName = fieldKey.split('.').pop();
                input = form.querySelector(`[name$="[${bareName}]"]`) || form.querySelector(`[name="${bareName}"]`);
            }

            if (input) {
                if (!firstInvalidField) {
                    firstInvalidField = input;
                }

                // Add error styling to input
                input.classList.add('is-invalid-input', 'border-rose-500', 'bg-rose-50/50', 'focus:border-rose-500', 'focus:ring-rose-500/20');

                // Create error text element
                const errorP = document.createElement('p');
                errorP.className = 'field-error-msg text-xs font-semibold text-rose-600 mt-1 flex items-center gap-1 animate-slide-up';
                errorP.innerHTML = `
                    <svg class="w-3.5 h-3.5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>${messages[0]}</span>
                `;

                // Insert error right after the input or its container wrapper
                const parentGroup = input.closest('.input-group') || input.parentElement;
                if (parentGroup) {
                    parentGroup.appendChild(errorP);
                } else {
                    input.insertAdjacentElement('afterend', errorP);
                }
            } else {
                console.warn(`Input element for error field "${fieldKey}" not found in form.`);
            }
        });

        // Scroll to first invalid input
        if (firstInvalidField) {
            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalidField.focus();
        }
    },

    /**
     * Manage button loading states during AJAX execution
     * @param {HTMLButtonElement|HTMLElement} btn
     * @param {boolean} isLoading
     * @param {string} loadingText
     */
    setLoading(btn, isLoading, loadingText = 'Menyimpan...') {
        if (!btn) return;

        if (isLoading) {
            btn.dataset.origHtml = btn.innerHTML;
            btn.dataset.origDisabled = btn.disabled;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>${loadingText}</span>
            `;
        } else {
            if (btn.dataset.origHtml) {
                btn.innerHTML = btn.dataset.origHtml;
            }
            btn.disabled = btn.dataset.origDisabled === 'true';
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    },

    /**
     * Universal AJAX Form Submission with Dexie Offline-First Support
     */
    async handleAjaxSubmit(form, options = {}) {
        if (!form) return;
        this.clearErrors(form);

        const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button:not([type="button"])');
        this.setLoading(submitBtn, true, options.loadingText || 'Menyimpan...');

        const formData = new FormData(form);
        const actionUrl = form.getAttribute('action') || window.location.href;
        let method = (form.getAttribute('method') || 'POST').toUpperCase();
        const methodField = form.querySelector('input[name="_method"]');
        if (methodField && methodField.value) {
            method = methodField.value.toUpperCase();
        }

        // Offline handling
        if (!navigator.onLine) {
            try {
                let dataObj = {};
                for (const [key, val] of formData.entries()) {
                    if (key !== '_token' && key !== '_method') {
                        dataObj[key] = val;
                    }
                }
                if (typeof options.transformData === 'function') {
                    dataObj = await options.transformData(dataObj, formData);
                }

                if (options.table) {
                    const saved = await AgriSyncManager.save(options.table, dataObj, { action: method === 'POST' ? 'create' : 'update' });
                    if (typeof options.onSuccess === 'function') {
                        options.onSuccess({ success: true, message: 'Data tersimpan di perangkat (Mode Offline)', data: saved }, false);
                    }
                    if (window.AgriSwal) {
                        window.AgriSwal.toastInfo('Tersimpan di perangkat (Mode Offline). Akan disinkronkan otomatis saat online.');
                    }
                }
            } catch (err) {
                console.error('Offline save error:', err);
                if (window.AgriSwal) window.AgriSwal.toastError('Gagal menyimpan data offline.');
            } finally {
                this.setLoading(submitBtn, false);
            }
            return;
        }

        // Online AJAX request
        try {
            const response = await fetch(actionUrl, {
                method: method === 'GET' ? 'GET' : 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: method === 'GET' ? null : formData
            });

            const result = await response.json().catch(() => null);

            if (response.ok && result && result.success !== false) {
                if (options.table && result) {
                    const item = result.data || result[options.table.replace(/s$/, '')] || result.crop || result.activity || result.medicine || result.note || result.step || result.transaction || {};
                    if (item && (item.id || item.uuid)) {
                        await AgriSyncManager.save(options.table, item, { sync_status: 'synced' }).catch(() => {});
                    }
                }

                if (window.AgriSwal) {
                    window.AgriSwal.toastSuccess(result?.message || 'Data berhasil disimpan!');
                }

                if (typeof options.onSuccess === 'function') {
                    options.onSuccess(result, true);
                }
            } else if (response.status === 422 && result && result.errors) {
                this.showErrors(form, result.errors);
                if (window.AgriSwal) {
                    window.AgriSwal.toastError('Mohon periksa kolom yang ditandai merah.');
                }
                if (typeof options.onError === 'function') {
                    options.onError(result.errors);
                }
            } else {
                const errorMsg = result?.message || `Terjadi kesalahan (Kode: ${response.status})`;
                if (window.AgriSwal) {
                    window.AgriSwal.toastError(errorMsg);
                }
                if (typeof options.onError === 'function') {
                    options.onError(result);
                }
            }
        } catch (fetchErr) {
            console.warn('Network error during form submission, fallback to offline:', fetchErr);
            try {
                let dataObj = {};
                for (const [key, val] of formData.entries()) {
                    if (key !== '_token' && key !== '_method') {
                        dataObj[key] = val;
                    }
                }
                if (typeof options.transformData === 'function') {
                    dataObj = await options.transformData(dataObj, formData);
                }
                if (options.table) {
                    const saved = await AgriSyncManager.save(options.table, dataObj, { action: method === 'POST' ? 'create' : 'update' });
                    if (typeof options.onSuccess === 'function') {
                        options.onSuccess({ success: true, message: 'Koneksi terputus. Data disimpan di perangkat (Mode Offline)', data: saved }, false);
                    }
                    if (window.AgriSwal) {
                        window.AgriSwal.toastInfo('Koneksi terputus. Data tersimpan di perangkat dan akan disinkronkan nanti.');
                    }
                }
            } catch (err) {
                if (window.AgriSwal) window.AgriSwal.toastError('Gagal menyimpan data.');
            }
        } finally {
            this.setLoading(submitBtn, false);
        }
    },

    /**
     * Universal AJAX Delete Helper with Dexie Offline-First Support
     */
    async handleAjaxDelete(options = {}) {
        const { url, table, id, uuid, itemName, title, onSuccess, rowElement } = options;

        const executeDelete = async () => {
            if (!navigator.onLine) {
                if (table) {
                    await AgriSyncManager.remove(table, uuid || id, id);
                }
                if (rowElement) {
                    rowElement.style.transition = 'all 0.3s ease';
                    rowElement.style.opacity = '0';
                    rowElement.style.transform = 'scale(0.95)';
                    setTimeout(() => rowElement.remove(), 300);
                }
                if (typeof onSuccess === 'function') onSuccess({ success: true, offline: true });
                if (window.AgriSwal) window.AgriSwal.toastSuccess(`"${itemName || 'Data'}" dihapus di perangkat (Mode Offline).`);
                return;
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                const result = await response.json().catch(() => null);
                if (response.ok && result && result.success !== false) {
                    if (table && (uuid || id)) {
                        await AgriSyncManager.db[table]?.delete(uuid || id).catch(() => {});
                    }
                    if (rowElement) {
                        rowElement.style.transition = 'all 0.3s ease';
                        rowElement.style.opacity = '0';
                        rowElement.style.transform = 'scale(0.95)';
                        setTimeout(() => rowElement.remove(), 300);
                    }
                    if (typeof onSuccess === 'function') onSuccess(result);
                    if (window.AgriSwal) window.AgriSwal.toastSuccess(result?.message || `"${itemName || 'Data'}" berhasil dihapus.`);
                } else {
                    const msg = result?.message || 'Gagal menghapus data.';
                    if (window.AgriSwal) window.AgriSwal.toastError(msg);
                }
            } catch (err) {
                console.warn('Network error during delete, falling back to offline:', err);
                if (table) {
                    await AgriSyncManager.remove(table, uuid || id, id);
                }
                if (rowElement) {
                    rowElement.style.transition = 'all 0.3s ease';
                    rowElement.style.opacity = '0';
                    rowElement.style.transform = 'scale(0.95)';
                    setTimeout(() => rowElement.remove(), 300);
                }
                if (typeof onSuccess === 'function') onSuccess({ success: true, offline: true });
                if (window.AgriSwal) window.AgriSwal.toastInfo(`Koneksi bermasalah. "${itemName || 'Data'}" dihapus offline.`);
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(title || 'Hapus Data?', itemName || 'data ini', executeDelete);
        } else {
            await executeDelete();
        }
    }
};
window.AgriFormHelper = AgriFormHelper;

/**
 * AgriSyncManager: Dexie-backed Offline-First Repository & Network Synchronization
 */
export const AgriSyncManager = {
    db: agriDb,

    /**
     * Save an entity offline-first into Dexie.js
     * Automatically assigns a UUID and status: 'pending' (or updates existing)
     */
    async save(table, data, options = {}) {
        const action = options.action || (data.uuid || data.id ? 'update' : 'create');
        const uuid = data.uuid || (action === 'create' ? AgriFormHelper.generateUUID() : null) || AgriFormHelper.generateUUID();
        const now = new Date().toISOString();

        const entity = {
            ...data,
            uuid,
            sync_status: options.sync_status || 'pending',
            updated_at: now,
            created_at: data.created_at || now
        };

        if (data.id && !entity.server_id) {
            entity.server_id = parseInt(data.id, 10);
        }

        try {
            await this.db[table].put(entity);

            // Add to background sync queue
            await this.db.sync_queue.add({
                uuid,
                table,
                action,
                payload: entity,
                sync_status: 'pending',
                created_at: now
            });

            this.notifySyncStatus();
        } catch (e) {
            console.error(`Error saving ${table} to Dexie:`, e);
        }

        return entity;
    },

    /**
     * Mark an entity as deleted offline-first
     */
    async remove(table, uuid, serverId = null) {
        const now = new Date().toISOString();

        try {
            await this.db[table].delete(uuid);

            await this.db.sync_queue.add({
                uuid,
                server_id: serverId,
                table,
                action: 'delete',
                sync_status: 'pending',
                created_at: now
            });

            this.notifySyncStatus();
        } catch (e) {
            console.error(`Error deleting ${table} from Dexie:`, e);
        }
    },

    /**
     * Get pending sync items count
     */
    async getPendingCount() {
        try {
            return await this.db.sync_queue.where('sync_status').equals('pending').count();
        } catch (e) {
            return 0;
        }
    },

    /**
     * Broadcast sync status to UI badges
     */
    async notifySyncStatus() {
        const pendingCount = await this.getPendingCount();
        const status = navigator.onLine ? (pendingCount > 0 ? 'syncing' : 'online') : 'offline';

        if (typeof window.updateConnectionBadges === 'function') {
            window.updateConnectionBadges(status, pendingCount);
        }

        window.dispatchEvent(new CustomEvent('agri:sync-status', {
            detail: { status, pending: pendingCount }
        }));
    }
};
window.AgriSyncManager = AgriSyncManager;
