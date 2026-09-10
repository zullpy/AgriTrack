# Design Guide — FarmDash UI

Referensi visual: dashboard "FarmVista" (lihat gambar terlampir) — tema hijau-putih, bersih, banyak kartu (card-based), rounded corners besar.

## 1. Prinsip Desain
- **Clean & airy**: banyak whitespace, kartu dengan padding lega.
- **Card-based layout**: setiap blok informasi dibungkus card dengan sudut membulat (radius besar) dan bayangan tipis.
- **Data-forward**: grafik dan angka besar jadi fokus utama, teks pendukung berukuran kecil abu-abu.
- **Konsisten**: sidebar tetap statis di kiri (desktop), warna aksi utama selalu hijau.

## 2. Palet Warna
| Nama | Hex (perkiraan) | Penggunaan |
|---|---|---|
| Hijau Utama (Primary) | `#2FB344` / `#22A85C` | Tombol utama, ikon aktif, aksen grafik |
| Hijau Gelap | `#1F7A3D` | Hover state, teks aksen |
| Hijau Muda / Mint | `#D6F5E3` | Background badge/ikon lembut |
| Ungu Aksen | `#8B7CF6` | Variasi warna grafik (kategori kedua) |
| Kuning/Oranye Aksen | `#F5A623` | Variasi warna grafik (kategori ketiga) |
| Putih | `#FFFFFF` | Background card |
| Abu Latar | `#F5F7F6` | Background halaman |
| Abu Teks Sekunder | `#8A9A94` / `#6B7B76` | Label, subteks |
| Teks Utama | `#1E2A24` / `#222` | Judul, angka besar |
| Merah Peringatan | `#E4574C` | Status peringatan / hapus |

## 3. Tipografi
- **Font**: sans-serif modern (mis. Inter, Poppins, atau Manrope).
- **Judul halaman/H1**: 24–28px, semi-bold.
- **Judul kartu/H2**: 16–18px, semi-bold.
- **Angka besar (metric)**: 22–26px, bold.
- **Body/label**: 13–14px, regular, warna abu.
- **Caption/kecil**: 11–12px untuk tanggal, status, keterangan tambahan.

## 4. Layout Struktur

### Desktop
```
┌───────────┬──────────────────────────────────────────┐
│           │  Header: search | notif | filter | export │
│  Sidebar  ├──────────────────────────────────────────┤
│  (fixed,  │  Baris kartu ringkasan (cuaca, produksi,  │
│  ~220px)  │  luas lahan, pendapatan)                  │
│           ├──────────────────────────────────────────┤
│  Logo     │  Grafik analisis (line chart) | highlight │
│  Menu     │  lahan (foto + info)                      │
│  items    ├──────────────────────────────────────────┤
│  Logout   │  Tabel tugas             | ringkasan panen │
└───────────┴──────────────────────────────────────────┘
```

### Mobile
- Sidebar disembunyikan menjadi bottom navigation bar (ikon: Dashboard, Data Obat, Kalender HST) — mengikuti pola versi mobile pada referensi (bottom nav dengan 3–4 ikon).
- Kartu disusun 1 kolom, full width, stack vertikal.
- Header disederhanakan: sapaan + avatar + tombol export ikon saja.

## 5. Komponen UI

### 5.1 Sidebar
- Lebar tetap ~220–240px, background putih.
- Logo di atas (ikon daun + nama app).
- List menu dengan ikon di kiri teks; item aktif memiliki background hijau muda + teks/ikon hijau tua, sudut membulat.
- Tombol "Log Out" di bagian paling bawah (opsional — bisa dihilangkan karena tanpa login) atau diganti "Bantuan".

### 5.2 Card
- Background putih, border-radius ~16–20px, shadow lembut (`0 2px 8px rgba(0,0,0,0.05)`).
- Padding internal ~16–20px.
- Judul card di kiri atas, kadang disertai ikon lingkaran kecil berwarna hijau muda.

### 5.3 Tombol
- **Primary**: background hijau solid, teks putih, radius penuh (pill) atau 10–12px, contoh "+ Tambah Tugas", "Export".
- **Secondary/Outline**: border tipis abu, teks gelap, background transparan, contoh "Lihat Semua".

### 5.4 Grafik
- **Radial/Gauge chart** (Overview Produksi): busur setengah lingkaran multi-warna (hijau, ungu, kuning) menunjukkan proporsi per kategori tanaman.
- **Line chart** (Analisis Hasil/HST progress): garis hijau halus dengan area gradient tipis di bawahnya, titik data dengan tooltip saat hover (contoh: "300t" pada bulan tertentu).

### 5.5 Badge Status
- Pill kecil dengan background lembut & teks warna sesuai status:
  - Hijau muda + teks hijau tua → "Aman" / "Selesai"
  - Kuning muda + teks oranye → "Menipis" / "In Progress"
  - Merah muda + teks merah → "Habis" / "Kadaluarsa" / "Pending"

### 5.6 Tabel
- Header abu terang, teks kecil kapital tipis.
- Baris dengan pembatas tipis (bukan garis tebal), padding vertikal cukup untuk keterbacaan.
- Kolom status berisi badge (lihat 5.5), kolom aksi ikon titik tiga (⋮) di kanan.

### 5.7 Kalender HST (khusus)
- Grid bulanan standar (7 kolom hari), tanggal aktif hari ini ditandai lingkaran hijau solid.
- Tanggal dengan kegiatan yang diinput manual ditandai titik/dot kecil berwarna di bawah angka tanggal.
- Panel samping/bawah: daftar tanaman berjalan dengan progress bar horizontal menunjukkan posisi HST saat ini terhadap total siklus (mis. HST 45/90).

#### 5.7.1 Kartu Tanaman Aktif
- Card putih rounded, berisi: nama tanaman, tanggal tanam, **badge HST besar** (mis. "HST 45") berwarna hijau tebal sebagai angka paling menonjol di kartu — nilai ini read-only, terupdate otomatis tiap hari, tidak bisa diedit manual.
- Progress bar tipis di bawah nama menunjukkan posisi HST terhadap estimasi siklus.
- Baris ikon kecil: jumlah kegiatan yang sudah dijadwalkan & jumlah yang jatuh tempo (badge kuning bila ada).
- Dua tombol aksi di bagian bawah kartu, sejajar:
  - **"+ Tambah Kegiatan"** — tombol outline hijau, ikon "+".
  - **"Sudah Dipanen"** — tombol solid hijau tua (atau outline merah muda untuk menegaskan aksi akhir), ikon centang/keranjang panen. Diposisikan di kanan agar tidak tertekan tidak sengaja.

#### 5.7.2 Modal/Form "Tambah Kegiatan"
Dibuka dari tombol "+ Tambah Kegiatan" pada kartu tanaman. Field:
- **Tanaman** — dropdown, terisi otomatis jika dibuka dari kartu tanaman tertentu.
- **Nama Kegiatan** — text input (mis. "Pemupukan", "Penyemprotan Pestisida").
- **HST Target** — input angka (stepper +/-), dengan teks bantu kecil di bawah menampilkan tanggal kalender hasil konversi otomatis (mis. "≈ 14 Sep 2026").
- **Catatan** (opsional) — textarea kecil.
- Tombol bawah: **"Simpan"** (solid hijau) dan **"Batal"** (teks abu, tanpa border).
- Setelah disimpan, kegiatan muncul sebagai baris di timeline kartu tanaman dan sebagai dot di kalender bulanan pada tanggal yang sesuai.

#### 5.7.3 Modal Konfirmasi "Sudah Dipanen"
- Modal kecil di tengah layar, ikon keranjang/centang besar berwarna hijau di atas.
- Judul: "Tandai sudah dipanen?" — teks singkat menjelaskan bahwa HST akan berhenti bertambah dan tanaman pindah ke Riwayat Tanam.
- Field opsional: **Tanggal Panen Aktual** (date picker, default hari ini).
- Tombol: **"Ya, Sudah Dipanen"** (solid hijau, aksi utama) dan **"Batal"** (outline abu).

#### 5.7.4 Riwayat Tanam
- List/card dengan tampilan sedikit lebih pudar (opacity ringan) dibanding kartu aktif untuk membedakan status non-aktif.
- Badge "Sudah Dipanen" abu-hijau di pojok kartu, menampilkan HST final (mis. "Dipanen — HST 92").
- Bisa expand untuk melihat riwayat kegiatan yang pernah diinput selama siklus tanam tersebut.

## 6. Ikonografi
- Ikon garis (outline/line-icon) minimalis, konsisten dengan gaya referensi (ikon daun, tetes air, matahari, kalender, tugas).
- Ukuran ikon konsisten 18–20px di sidebar, 20–24px di kartu.

## 7. Spacing & Grid
- Grid dasar 8px (padding/margin kelipatan 8: 8, 16, 24, 32).
- Jarak antar kartu: 16–20px.
- Border-radius standar: 12px (elemen kecil), 16–20px (card besar).

## 8. Responsivitas
- Breakpoint utama: Desktop ≥1024px (sidebar penuh), Tablet 768–1023px (sidebar mengecil ke ikon saja), Mobile <768px (sidebar → bottom nav, kartu 1 kolom).