# PRD — Aplikasi Manajemen Pertanian (FarmDash)

## 1. Latar Belakang
Aplikasi ini dibuat untuk membantu petani/pengelola kebun memantau kondisi tanaman, mencatat penggunaan obat-obatan tanaman, dan mengelola jadwal perawatan berdasarkan Hari Setelah Tanam (HST). Desain UI mengacu pada referensi dashboard "FarmVista" (sidebar hijau-putih, kartu ringkasan, grafik lengkung, tabel tugas).

**Catatan khusus:** Tidak ada halaman login. Aplikasi langsung membuka halaman Dashboard saat dibuka (single-user / tanpa autentikasi di versi ini).

## 2. Tujuan Produk
- Memberi ringkasan cepat kondisi lahan, cuaca, dan produksi dalam satu layar.
- Memudahkan pencatatan & pelacakan pemakaian obat/pupuk/pestisida per tanaman.
- Menyediakan kalender HST agar jadwal perawatan (pemupukan, penyemprotan, panen) tidak terlewat.

## 3. Target Pengguna
- Petani/pemilik lahan skala kecil-menengah.
- Mandor/pengelola kebun yang mencatat aktivitas harian tim.

## 4. Ruang Lingkup Fitur

### 4.1 Navigasi (Sidebar)
Menu utama hanya 3 item (menyederhanakan dari referensi):
1. **Dashboard**
2. **Data Obat-obatan Tanaman**
3. **Kalender HST**

Tidak ada halaman login/auth — aplikasi langsung terbuka di Dashboard.

### 4.2 Dashboard
Mengikuti layout referensi:
- **Header**: sapaan "Selamat Pagi!", search bar, notifikasi, filter periode ("Bulan Ini"), tombol Export, profil pengguna.
- **Kartu Cuaca Hari Ini**: tanggal, suhu, kondisi cuaca (ikon), suhu tertinggi/terendah, "terasa seperti".
- **Kartu Overview Produksi**: grafik radial/gauge multi-warna menampilkan total produksi (ton) per jenis tanaman.
- **Grafik Analisis Hasil Panen Bulanan**: line chart tren produksi per bulan, dengan tooltip nilai per titik.
- **Ringkasan Panen Sayuran**: daftar komoditas dengan ikon, jumlah panen (ton).

### 4.3 Data Obat-obatan Tanaman
Modul pencatatan penggunaan pestisida/pupuk/obat tanaman.
- **Daftar Obat**: tabel berisi Nama Obat, Jenis (Pestisida/Fungisida/Pupuk/Herbisida), Tanaman Sasaran.
- **Tambah/Edit Obat**: form input nama, jenis, dosis anjuran, interval aplikasi, catatan keamanan.
- **Filter & Pencarian**: berdasarkan jenis obat dan nama tanaman.

### 4.4 Kalender HST (Hari Setelah Tanam)
Modul penjadwalan berbasis umur tanaman.
- **Tampilan Kalender Bulanan**: setiap tanggal menampilkan tanaman yang sedang berjalan beserta HST-nya (mis. "Jagung — HST 45").
- **Tambah Tanaman Baru**: input nama tanaman + tanggal tanam. Setelah disimpan, sistem **otomatis menghitung HST dan tanggal berjalan** setiap hari berdasarkan selisih tanggal hari ini dengan tanggal tanam (HST = tanggal hari ini − tanggal tanam). Pengguna tidak perlu input HST atau tanggal secara manual — keduanya selalu ter-update sendiri.
- **Kegiatan/Timeline HST — input manual**: jadwal kegiatan (misal pemupukan, penyemprotan, penyiangan, panen) **tidak digenerate otomatis oleh sistem**. Pengguna menambahkan sendiri kegiatan untuk tiap tanaman dengan memilih/menentukan pada HST ke berapa kegiatan itu dilakukan (mis. "HST 10 — Pemupukan", "HST 20 — Penyemprotan").
  - Form tambah kegiatan: pilih tanaman, nama kegiatan, HST target (angka), catatan (opsional).
  - Kegiatan yang HST targetnya sudah terlewati/sesuai HST berjalan saat ini akan ditandai (badge) di kalender/timeline sebagai reminder, tapi isi kegiatannya tetap murni dari input pengguna.
  - Pengguna bisa edit/hapus kegiatan kapan saja sebelum atau sesudah HST-nya tercapai.
- **Notifikasi/Reminder**: penanda visual (badge warna) untuk kegiatan (yang sudah diinput manual) yang jatuh tempo hari ini/minggu ini sesuai HST berjalan.
- **Status Tanaman**: setiap tanaman punya status `Sedang Ditanam` (aktif, HST & tanggal terus berjalan/update otomatis tiap hari) atau `Sudah Dipanen` (berhenti, HST dikunci di angka terakhir).
- **Tombol "Sudah Dipanen"**: tersedia di kartu/detail setiap tanaman yang berstatus aktif.
  - Saat ditekan, sistem meminta konfirmasi dan (opsional) tanggal panen aktual.
  - Setelah dikonfirmasi: status tanaman berubah jadi `Sudah Dipanen`, penghitungan HST **berhenti** (tidak bertambah lagi setiap hari), dan tanaman otomatis pindah dari tampilan "Tanaman Aktif" di kalender/list ke **Riwayat Tanam**.
  - HST final (misal "Dipanen pada HST 92") tetap ditampilkan sebagai catatan riwayat, tidak dihapus. Kegiatan-kegiatan yang sudah diinput sebelumnya tetap tersimpan di riwayat tanaman tersebut.
- **Riwayat Tanam**: daftar tanaman yang sudah ditandai "Sudah Dipanen", menampilkan tanggal tanam, tanggal panen, total HST saat panen, daftar kegiatan yang pernah diinput, dan hasil akhir (jika dicatat).

## 5. Alur Pengguna (User Flow)
1. Buka aplikasi → langsung masuk **Dashboard** (tanpa login).
2. Dari sidebar, pilih **Data Obat-obatan Tanaman** untuk tambah catatan obat baru.
3. Dari sidebar, pilih **Kalender HST**:
   - Tambah tanaman baru → HST & tanggal berjalan otomatis mulai dihitung.
   - Tambah kegiatan perawatan secara manual (tentukan sendiri di HST keberapa).
   - Tekan "Sudah Dipanen" saat tanaman selesai dipanen agar HST berhenti bertambah.
4. Kembali ke Dashboard kapan saja untuk ringkasan umum.

## 6. Kebutuhan Non-Fungsional
- Responsif: layout desktop (sidebar penuh) dan mobile (sidebar collapse jadi bottom nav / hamburger), sesuai referensi yang punya versi desktop & mobile.
- Konsisten dengan gaya visual referensi (lihat `design.md`).
- Data tersimpan lokal/persisten antar sesi (tanpa akun pengguna).

## 7. Metrik Keberhasilan
- Waktu untuk menemukan info obat < 5 detik.
- HST & tanggal semua tanaman aktif selalu akurat tanpa input manual.
- Pengguna bisa menambah/mengubah kegiatan perawatan kapan saja tanpa dibatasi jadwal bawaan sistem.
- Tidak ada friksi tambahan dari proses login (karena dihilangkan).

## 8. Di Luar Cakupan (Out of Scope)
- Sistem login/multi-user & role management.
- Integrasi pembayaran/e-commerce.
- Generate otomatis jadwal kegiatan/milestone perawatan oleh sistem (semua kegiatan diinput manual oleh pengguna).
- Menu Crop Management, Soil & Water, Weather, Equipment, Labor Management, Reports & Analytics, Settings, My Account dari referensi asli (tidak dipakai, kecuali dashboard tetap menampilkan ringkasan cuaca sebagai kartu, bukan menu terpisah).