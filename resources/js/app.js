import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from 'sweetalert2';
import { offlineStore } from './offline-store.js';
import { agriDb, AgriFormHelper, AgriSyncManager } from './agri-db.js';

window.Swal = Swal;
window.AgriOfflineStore = offlineStore;
window.agriDb = agriDb;
window.AgriFormHelper = AgriFormHelper;
window.AgriSyncManager = AgriSyncManager;

// AgriTrack SweetAlert Custom Theme Preset
window.AgriSwal = {
    confirmDelete: function(title, itemName, onConfirm) {
        return Swal.fire({
            title: title || 'Hapus Data?',
            html: `Apakah Anda yakin ingin menghapus <strong>"${itemName}"</strong>?<br><span class="agri-swal-subtitle">Data yang dihapus tidak dapat dipulihkan kembali.</span>`,
            icon: 'warning',
            iconColor: '#E4574C',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'agri-swal-popup',
                title: 'agri-swal-title',
                htmlContainer: 'agri-swal-html',
                confirmButton: 'agri-swal-btn-danger',
                cancelButton: 'agri-swal-btn-cancel',
                actions: 'agri-swal-actions',
                icon: 'agri-swal-icon-warning'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
            return result;
        });
    },

    toastSuccess: function(message) {
        return Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            iconColor: '#2FB344',
            title: message,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            customClass: {
                popup: 'agri-swal-toast',
                title: 'agri-swal-toast-title',
                timerProgressBar: 'agri-swal-progress'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    },

    toastInfo: function(message) {
        return Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            iconColor: '#F5A623',
            title: message,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: {
                popup: 'agri-swal-toast',
                title: 'agri-swal-toast-title'
            }
        });
    },

    toastError: function(message) {
        return Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            iconColor: '#E4574C',
            title: message,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: {
                popup: 'agri-swal-toast agri-swal-toast-error',
                title: 'agri-swal-toast-title'
            }
        });
    },

    alert: function(title, message, icon = 'info') {
        const isError = icon === 'error';
        const isSuccess = icon === 'success';
        return Swal.fire({
            title: title || (isError ? 'Terjadi Kesalahan' : (isSuccess ? 'Berhasil' : 'Pemberitahuan')),
            html: message,
            icon: icon,
            iconColor: isError ? '#E4574C' : (isSuccess ? '#2FB344' : '#059669'),
            confirmButtonText: 'Mengerti',
            buttonsStyling: false,
            customClass: {
                popup: 'agri-swal-popup',
                title: 'agri-swal-title',
                htmlContainer: 'agri-swal-html',
                confirmButton: isError ? 'agri-swal-btn-danger' : 'agri-swal-btn-primary',
                actions: 'agri-swal-actions'
            }
        });
    },

    alertSuccess: function(title, message) {
        return this.alert(title, message, 'success');
    },

    alertError: function(title, message) {
        return this.alert(title, message, 'error');
    },

    confirm: function(title, message, onConfirm, confirmText = 'Lanjutkan', isDanger = false) {
        return Swal.fire({
            title: title || 'Konfirmasi Tindakan',
            html: message,
            icon: isDanger ? 'warning' : 'question',
            iconColor: isDanger ? '#E4574C' : '#059669',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
            buttonsStyling: false,
            customClass: {
                popup: 'agri-swal-popup',
                title: 'agri-swal-title',
                htmlContainer: 'agri-swal-html',
                confirmButton: isDanger ? 'agri-swal-btn-danger' : 'agri-swal-btn-primary',
                cancelButton: 'agri-swal-btn-cancel',
                actions: 'agri-swal-actions',
                icon: isDanger ? 'agri-swal-icon-warning' : 'agri-swal-icon-question'
            }
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
            return result;
        });
    }
};

// Global native alert override to guarantee consistent SweetAlert theme across all scripts
window.alert = function(message) {
    if (window.AgriSwal && typeof window.AgriSwal.toastInfo === 'function') {
        window.AgriSwal.toastInfo(String(message));
    }
};

// ── PWA Service Worker Registration ──
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((reg) => {
                console.log('AgriTrack Service Worker registered:', reg.scope);
                reg.update();
            })
            .catch((err) => {
                console.warn('Service Worker registration failed:', err);
            });
    });
}

// ── Connection Status Indicator Management ──
function updateConnectionBadges(status, pendingCount = 0) {
    const badges = document.querySelectorAll('.connection-badge');
    badges.forEach((el) => {
        if (status === 'syncing') {
            el.className = 'connection-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 transition-all';
            el.innerHTML = '<span class="w-2 h-2 rounded-full bg-blue-500 animate-spin"></span> <span>Menyinkronkan...</span>';
        } else if (!navigator.onLine || status === 'offline') {
            el.className = 'connection-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 transition-all';
            const pendingText = pendingCount > 0 ? ` (${pendingCount} antrean)` : '';
            el.innerHTML = `<span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> <span>Mode Offline${pendingText}</span>`;
        } else {
            el.className = 'connection-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 transition-all';
            el.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span> <span>Online</span>';
        }
    });
}
window.updateConnectionBadges = updateConnectionBadges;

// ── Offline Form Interception & Auto Sync ──
document.addEventListener('DOMContentLoaded', async () => {
    // 1. Initialize IndexedDB
    await offlineStore.init();

    // 2. Initial check & sync if online
    const pending = await offlineStore.getPendingCount();
    updateConnectionBadges(navigator.onLine ? 'online' : 'offline', pending);

    if (navigator.onLine) {
        await offlineStore.syncAll();
    }

    // 3. Network listeners
    window.addEventListener('online', async () => {
        updateConnectionBadges('syncing');
        AgriSwal.toastInfo('Terhubung kembali! Menyinkronkan data...');
        await offlineStore.syncAll();
    });

    window.addEventListener('offline', async () => {
        const p = await offlineStore.getPendingCount();
        updateConnectionBadges('offline', p);
        AgriSwal.toastInfo('Anda sedang offline. Data dan foto tetap dapat dicatat & disimpan di HP.');
    });

    window.addEventListener('agri:sync-status', (e) => {
        updateConnectionBadges(e.detail.status, e.detail.pending || 0);
    });

    window.addEventListener('agri:sync-success', (e) => {
        const count = e.detail.count || 0;
        if (count > 0) {
            AgriSwal.toastSuccess(`Sinkronisasi selesai! ${count} data berhasil disimpan ke server.`);
            // If on data-obat index or kalender-hst tanaman show, reload to reflect fresh IDs from server
            if (window.location.pathname.startsWith('/data-obat') && !window.location.pathname.includes('/tambah') && !window.location.pathname.includes('/edit')) {
                setTimeout(() => window.location.reload(), 1200);
            } else if (window.location.pathname.startsWith('/kalender-hst/tanaman/')) {
                setTimeout(() => window.location.reload(), 1200);
            } else if (window.location.pathname.startsWith('/keuangan')) {
                if (typeof window.loadKeuanganData === 'function') {
                    window.loadKeuanganData();
                }
            } else if (window.location.pathname.startsWith('/steps')) {
                setTimeout(() => window.location.reload(), 1200);
            }
        }
    });

    // 4. Global Delete confirmation with AJAX & offline support (Zero Full Page Refresh)
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('form[data-confirm-delete]');
        if (!form) return;

        if (form.dataset.confirmed === 'true') {
            return;
        }

        e.preventDefault();
        const itemName = form.dataset.confirmDelete || 'data ini';
        const title = form.dataset.confirmTitle || 'Hapus Data?';

        AgriSwal.confirmDelete(title, itemName, async () => {
            const actionUrl = form.getAttribute('action') || '';
            const parts = actionUrl.split('/');
            const id = parseInt(parts[parts.length - 1], 10);
            const row = form.closest('tr, .divide-y > div, .grid > div, [data-row-id], .card-item');

            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0.4';
                row.style.pointerEvents = 'none';
            }

            if (!navigator.onLine) {
                // Offline deletion in Dexie
                await offlineStore.deleteMedicineOffline(id);
                AgriSwal.toastSuccess(`"${itemName}" dihapus secara offline.`);

                if (row) {
                    row.style.transform = 'scale(0.95)';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                }
                const p = await offlineStore.getPendingCount();
                updateConnectionBadges('offline', p);
            } else {
                // Online deletion via AJAX (No page reload)
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    });

                    const result = await response.json().catch(() => null);

                    if (response.ok && result && result.success !== false) {
                        AgriSwal.toastSuccess(result.message || `"${itemName}" berhasil dihapus.`);
                        if (row) {
                            row.style.transform = 'scale(0.95)';
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 300);
                        }
                        if (id) {
                            await agriDb.medicines.where('server_id').equals(id).delete().catch(() => {});
                            await agriDb.crops.where('server_id').equals(id).delete().catch(() => {});
                        }
                    } else {
                        if (row) {
                            row.style.opacity = '1';
                            row.style.pointerEvents = 'auto';
                        }
                        AgriSwal.toastError(result?.message || 'Gagal menghapus data.');
                    }
                } catch (err) {
                    console.warn('Network error during online delete, fallback to offline:', err);
                    await offlineStore.deleteMedicineOffline(id);
                    AgriSwal.toastSuccess(`"${itemName}" dihapus secara offline.`);
                    if (row) {
                        row.style.transform = 'scale(0.95)';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    }
                    const p = await offlineStore.getPendingCount();
                    updateConnectionBadges('offline', p);
                }
            }
        });
    });

    // 5. Intercept medicine create/edit forms with AJAX (Zero Full Page Refresh, Inline Errors, Loading State)
    const medicineForm = document.querySelector('form#form-medicine');
    if (medicineForm) {
        medicineForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            AgriFormHelper.clearErrors(medicineForm);

            const submitBtn = medicineForm.querySelector('button[type="submit"]') || document.querySelector('button[form="form-medicine"]');
            AgriFormHelper.setLoading(submitBtn, true, 'Menyimpan Obat...');

            const formData = new FormData(medicineForm);
            const nama = (formData.get('nama') || '').toString().trim();
            if (!nama) {
                AgriFormHelper.showErrors(medicineForm, { nama: ['Nama obat wajib diisi.'] });
                AgriFormHelper.setLoading(submitBtn, false);
                return;
            }

            const rawHarga = (formData.get('harga') || '').toString().replace(/\D/g, '');
            const actionUrl = medicineForm.getAttribute('action') || '';
            const methodField = medicineForm.querySelector('input[name="_method"]');
            const isEdit = methodField && methodField.value.toUpperCase() === 'PUT';

            // Kumpulkan foto dalam format base64
            const photosBase64 = [];
            if (window.selectedCompressedFiles && window.selectedCompressedFiles.length > 0) {
                for (const item of window.selectedCompressedFiles) {
                    if (item.dataUrl) {
                        photosBase64.push(item.dataUrl);
                    } else if (item.file) {
                        const b64 = await new Promise((res) => {
                            const r = new FileReader();
                            r.onload = () => res(r.result);
                            r.onerror = () => res(null);
                            r.readAsDataURL(item.file);
                        });
                        if (b64) photosBase64.push(b64);
                    }
                }
            } else {
                const fileInput = document.getElementById('foto_nota');
                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        const f = fileInput.files[i];
                        const b64 = await new Promise((res) => {
                            const r = new FileReader();
                            r.onload = () => res(r.result);
                            r.onerror = () => res(null);
                            r.readAsDataURL(f);
                        });
                        if (b64) photosBase64.push(b64);
                    }
                }
            }

            const dataObj = {
                nama,
                jenis: formData.get('jenis') || 'Fungisida',
                cara_kerja: formData.get('cara_kerja') || null,
                sasaran_obat: formData.get('sasaran_obat') || '',
                tanaman_sasaran: formData.get('tanaman_sasaran') || '',
                dosis_anjuran: formData.get('dosis_anjuran') || '',
                harga: rawHarga !== '' ? parseInt(rawHarga, 10) : null,
                unsur_bahan: formData.get('unsur_bahan') || '',
                fase: formData.get('fase') || null,
                keterangan: formData.get('keterangan') || '',
                tanggal_beli: formData.get('tanggal_beli') || null,
                toko_obat: formData.get('toko_obat') || '',
                photos_base64: photosBase64
            };

            if (!navigator.onLine) {
                // Offline save directly to Dexie
                if (isEdit) {
                    const parts = actionUrl.split('/');
                    const id = parseInt(parts[parts.length - 1], 10);
                    await offlineStore.updateMedicineOffline(id, dataObj);
                    AgriSwal.toastSuccess(`Perubahan "${nama}" tersimpan di perangkat (Mode Offline).`);
                } else {
                    await offlineStore.addMedicineOffline(dataObj);
                    AgriSwal.toastSuccess(`Obat "${nama}" berhasil disimpan di perangkat (Mode Offline).`);
                }
                AgriFormHelper.setLoading(submitBtn, false);
                const p = await offlineStore.getPendingCount();
                updateConnectionBadges('offline', p);
                return;
            }

            // Online submit via AJAX JSON
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const result = await response.json().catch(() => null);

                if (response.ok && result && result.success !== false) {
                    AgriSwal.toastSuccess(result.message || 'Obat berhasil disimpan!');
                    // Cache to Dexie with synced status
                    const med = result.medicine || result.data || {};
                    if (med.id) {
                        await AgriSyncManager.save('medicines', med, { sync_status: 'synced' }).catch(() => {});
                    }
                    if (!isEdit) {
                        medicineForm.reset();
                        if (window.selectedCompressedFiles) window.selectedCompressedFiles = [];
                        const preview = document.getElementById('preview-container');
                        if (preview) preview.innerHTML = '';
                    }
                } else if (response.status === 422 && result && result.errors) {
                    AgriFormHelper.showErrors(medicineForm, result.errors);
                    AgriSwal.toastError('Mohon periksa input yang ditandai merah.');
                } else {
                    AgriSwal.toastError(result?.message || 'Gagal menyimpan obat.');
                }
            } catch (err) {
                console.warn('Network error during online submit, fallback to offline:', err);
                if (isEdit) {
                    const parts = actionUrl.split('/');
                    const id = parseInt(parts[parts.length - 1], 10);
                    await offlineStore.updateMedicineOffline(id, dataObj);
                } else {
                    await offlineStore.addMedicineOffline(dataObj);
                }
                AgriSwal.toastInfo(`Koneksi terganggu. Data "${nama}" tersimpan di perangkat secara offline.`);
                const p = await offlineStore.getPendingCount();
                updateConnectionBadges('offline', p);
            } finally {
                AgriFormHelper.setLoading(submitBtn, false);
            }
        });
    }

    // 6. Check for offline flash message after redirection
    const offlineFlash = sessionStorage.getItem('agri_offline_flash');
    if (offlineFlash) {
        sessionStorage.removeItem('agri_offline_flash');
        AgriSwal.toastSuccess(offlineFlash);
    }
});
