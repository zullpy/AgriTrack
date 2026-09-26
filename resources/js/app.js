import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from 'sweetalert2';
import { offlineStore } from './offline-store.js';

window.Swal = Swal;
window.AgriOfflineStore = offlineStore;

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

    // 4. Global Delete confirmation with offline support
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('form[data-confirm-delete]');
        if (!form) return;

        if (form.dataset.confirmed === 'true') {
            return;
        }

        e.preventDefault();
        const itemName = form.dataset.confirmDelete || 'data ini';
        const title = form.dataset.confirmTitle || 'Hapus Data Obat?';

        AgriSwal.confirmDelete(title, itemName, async () => {
            if (!navigator.onLine) {
                // Offline deletion
                const actionUrl = form.getAttribute('action') || '';
                const parts = actionUrl.split('/');
                const id = parseInt(parts[parts.length - 1]);

                await offlineStore.deleteMedicineOffline(id);
                AgriSwal.toastSuccess(`"${itemName}" dihapus secara offline.`);

                // Visually remove row or card
                const row = form.closest('tr, .divide-y > div');
                if (row) {
                    row.style.opacity = '0.3';
                    row.style.pointerEvents = 'none';
                    setTimeout(() => row.remove(), 400);
                }
                const p = await offlineStore.getPendingCount();
                updateConnectionBadges('offline', p);
            } else {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });
    });

    // 5. Intercept medicine create/edit forms when offline
    const medicineForm = document.querySelector('form#form-medicine');
    if (medicineForm) {
        medicineForm.addEventListener('submit', async (e) => {
            if (!navigator.onLine) {
                e.preventDefault();

                const formData = new FormData(medicineForm);
                const rawHarga = (formData.get('harga') || '').toString().replace(/\D/g, '');

                // Kumpulkan foto dalam format base64 agar tersimpan di IndexedDB saat offline
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

                const data = {
                    nama: formData.get('nama') || '',
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

                const methodField = medicineForm.querySelector('input[name="_method"]');
                const isEdit = methodField && methodField.value.toUpperCase() === 'PUT';
                const actionUrl = medicineForm.getAttribute('action') || '';

                if (isEdit) {
                    const parts = actionUrl.split('/');
                    const id = parseInt(parts[parts.length - 1]);
                    await offlineStore.updateMedicineOffline(id, data);
                    sessionStorage.setItem('agri_offline_flash', `Perubahan "${data.nama}" tersimpan di perangkat (Mode Offline).`);
                } else {
                    await offlineStore.addMedicineOffline(data);
                    sessionStorage.setItem('agri_offline_flash', `Obat "${data.nama}" tersimpan di perangkat (Mode Offline).`);
                }

                window.location.href = '/data-obat';
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
