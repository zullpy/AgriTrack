<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\FinancialTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinancialTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan transaksi lama agar tidak dobel
        FinancialTransaction::query()->delete();

        $crop = Crop::first();
        $cropId = $crop ? $crop->id : null;

        $startDate = Carbon::now()->subDays(60);

        $data = [
            // ── 1. Persiapan / Pra Olah Lahan (Tanpa Sub) ──
            [
                'kategori' => 'Persiapan / Pra Olah Lahan',
                'sub_kategori' => null,
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Pembersihan Gulma & Semak Lahan Blok A', 'nominal' => 250000, 'ket' => 'Pembersihan sisa rumput liar sebelum traktor'],
                    ['judul' => 'Pengukuran & Pemetaan Kontur Bedengan', 'nominal' => 180000, 'ket' => 'Jasa ukur kemiringan lahan untuk drainase'],
                    ['judul' => 'Sewa Traktor Singkal Pra-Olah', 'nominal' => 450000, 'ket' => 'Bajak lahan tahap awal sedalam 30cm'],
                    ['judul' => 'Pembongkaran Tunggul Sisa Panen Sebelumnya', 'nominal' => 300000, 'ket' => 'Pencabutan akar lama agar tidak berjamur'],
                    ['judul' => 'Pembuatan Saluran Drainase Keliling Lahan', 'nominal' => 350000, 'ket' => 'Parit keliling mencegah genangan air hujan'],
                ],
            ],

            // ── 2. Olah Lahan Garapan ──
            // a) Pupuk Dasar - Organik › HOK Pemberian Pupuk
            [
                'kategori' => 'Olah Lahan Garapan',
                'sub_kategori' => 'Pupuk Dasar - Organik › HOK Pemberian Pupuk',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'HOK Tebar Kohe Ayam Matang 50 Karung', 'nominal' => 200000, 'ket' => 'Upah 2 pekerja tabur kohe fermentasi'],
                    ['judul' => 'HOK Pengadukan Kompos Kasgot ke Bedengan', 'nominal' => 180000, 'ket' => 'Aplikasi kompos maggot secara merata'],
                    ['judul' => 'HOK Penaburan Pupuk Kandang Sapi Blok Barat', 'nominal' => 220000, 'ket' => 'Upah sebar pupuk kandang sapi terfermentasi'],
                    ['judul' => 'HOK Aplikasi Pupuk Hayati Mikoriza Dasar', 'nominal' => 160000, 'ket' => 'Inokulasi jamur mikoriza dasar bedengan'],
                    ['judul' => 'HOK Penyebaran Bokashi Halus Bedengan 1-10', 'nominal' => 190000, 'ket' => 'Penaburan bokashi buatan sendiri'],
                ],
            ],
            // b) Pupuk Dasar - Organik › Mamin
            [
                'kategori' => 'Olah Lahan Garapan',
                'sub_kategori' => 'Pupuk Dasar - Organik › Mamin',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Konsumsi & Air Mineral Pekerja Tebar Kohe', 'nominal' => 65000, 'ket' => 'Snack pagi dan air galon tim pupuk organik'],
                    ['judul' => 'Kopi & Gorengan Istirahat Tabur Kompos', 'nominal' => 45000, 'ket' => 'Camilan sore pekerja'],
                    ['judul' => 'Makan Siang 4 Orang Pekerja Pupuk Kandang', 'nominal' => 100000, 'ket' => 'Nasi bungkus siang pekerja pupuk kandang sapi'],
                    ['judul' => 'Snack & Es Teh Pekerja Bokashi Pagi', 'nominal' => 50000, 'ket' => 'Pelepas dahaga regu sebar bokashi'],
                    ['judul' => 'Makan Siang & Mamin Lembur Tebar Pupuk Organik', 'nominal' => 90000, 'ket' => 'Konsumsi lembur sebar pupuk dasar'],
                ],
            ],
            // c) Pupuk Dasar - Kimia › HOK Pemberian Pupuk
            [
                'kategori' => 'Olah Lahan Garapan',
                'sub_kategori' => 'Pupuk Dasar - Kimia › HOK Pemberian Pupuk',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'HOK Tabur SP-36 dan NPK Dasar Bedengan Utama', 'nominal' => 180000, 'ket' => 'Aplikasi pupuk fosfat dan NPK dasar'],
                    ['judul' => 'HOK Aplikasi Dolomit/Kapur Pertanian', 'nominal' => 160000, 'ket' => 'Penaburan kapur dolomit netralisir pH tanah'],
                    ['judul' => 'HOK Tebar ZA & KCl Dasar Sebelum Pasang Mulsa', 'nominal' => 190000, 'ket' => 'Upah tabur ZA dan Kalium dasar'],
                    ['judul' => 'HOK Campur & Tabur Insektisida Karbofuran Dasar', 'nominal' => 150000, 'ket' => 'Pencegahan hama uret dan nematoda akar'],
                    ['judul' => 'HOK Penaburan Boron & Magnesium Sulfat Dasar', 'nominal' => 175000, 'ket' => 'Aplikasi unsur mikro dasar bedengan'],
                ],
            ],
            // d) Pupuk Dasar - Kimia › Mamin
            [
                'kategori' => 'Olah Lahan Garapan',
                'sub_kategori' => 'Pupuk Dasar - Kimia › Mamin',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Makan Siang Pekerja Tabur SP-36 & NPK', 'nominal' => 80000, 'ket' => 'Konsumsi makan siang tim pupuk kimia'],
                    ['judul' => 'Kopi & Roti Istirahat Tebar Dolomit', 'nominal' => 40000, 'ket' => 'Snack pagi pekerja tabur dolomit'],
                    ['judul' => 'Mamin Pekerja Tabur Pupuk Kimia Dasar Blok B', 'nominal' => 75000, 'ket' => 'Makan siang dan es teh manis'],
                    ['judul' => 'Snack Pagi & Air Galon Tabur Karbofuran', 'nominal' => 45000, 'ket' => 'Air minum dan kue basah'],
                    ['judul' => 'Makan Siang 3 Pekerja Pupuk Dasar Kimia', 'nominal' => 75000, 'ket' => 'Makan siang 3 orang tukang tabur'],
                ],
            ],
            // e) Finishing Olah Lahan › HOK Pekerja
            [
                'kategori' => 'Olah Lahan Garapan',
                'sub_kategori' => 'Finishing Olah Lahan › HOK Pekerja',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'HOK Perapihan & Pembentukan Guludan Bedengan', 'nominal' => 250000, 'ket' => 'Merapikan bentuk bedengan sebelum mulsa'],
                    ['judul' => 'HOK Pemasangan Mulsa Plastik Hitam Perak', 'nominal' => 280000, 'ket' => 'Penarikan dan perapian mulsa plastik'],
                    ['judul' => 'HOK Pembuatan Lubang Tanam Mulsa', 'nominal' => 160000, 'ket' => 'Melubangi mulsa jarak 50x60cm dengan pelubang bara'],
                    ['judul' => 'HOK Penjepitan Mulsa dengan Bambu Pasak', 'nominal' => 150000, 'ket' => 'Memasang pasak bambu di pinggir mulsa'],
                    ['judul' => 'HOK Penggenangan Parit / Labur Saluran Air', 'nominal' => 200000, 'ket' => 'Pembersihan got antar bedengan agar air lancar'],
                ],
            ],
            // f) Finishing Olah Lahan › Mamin
            [
                'kategori' => 'Olah Lahan Garapan',
                'sub_kategori' => 'Finishing Olah Lahan › Mamin',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Makan Siang 5 Tukang Pasang Mulsa Plastik', 'nominal' => 125000, 'ket' => 'Konsumsi makan siang regu mulsa'],
                    ['judul' => 'Kopi & Makanan Ringan Finishing Bedengan', 'nominal' => 55000, 'ket' => 'Kopi panas dan gorengan sore'],
                    ['judul' => 'Mamin & Minuman Isotonik Pekerja Lubang Tanam', 'nominal' => 60000, 'ket' => 'Minuman penambah stamina siang terik'],
                    ['judul' => 'Konsumsi Sore Pasang Pasak Bambu Mulsa', 'nominal' => 45000, 'ket' => 'Teh hangat dan roti sore'],
                    ['judul' => 'Makan Siang Pekerja Pemadatan Parit Drainase', 'nominal' => 80000, 'ket' => 'Nasi bungkus tim parit bedengan'],
                ],
            ],

            // ── 3. Penanaman ──
            // a) Sub: Bibit
            [
                'kategori' => 'Penanaman',
                'sub_kategori' => 'Bibit',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Pembelian Benih Unggul Varietas F1 (5 Sachet)', 'nominal' => 325000, 'ket' => 'Benih bersertifikat tahan virus Gemini'],
                    ['judul' => 'Beli Tray Semai Plastik 128 Lubang (10 Pcs)', 'nominal' => 120000, 'ket' => 'Wadah semai bibit siap pakai'],
                    ['judul' => 'Media Semai Siap Pakai Premium (4 Karung)', 'nominal' => 160000, 'ket' => 'Campuran cocopeat, sekam bakar, dan kompos halus'],
                    ['judul' => 'Beli Bibit Siap Tanam 1.000 Batang', 'nominal' => 400000, 'ket' => 'Bibit umur 25 HSS siap pindah tanam'],
                    ['judul' => 'Bibit Semai Sulaman / Cadangan Tanam', 'nominal' => 150000, 'ket' => 'Cadangan bibit jika ada yang layu di lahan'],
                ],
            ],
            // b) Sub: Gok Penanaman
            [
                'kategori' => 'Penanaman',
                'sub_kategori' => 'Gok Penanaman',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'HOK Tanam Pindah Bibit Blok Timur 4 Orang', 'nominal' => 320000, 'ket' => 'Pindah tanam pagi hari ke bedengan utama'],
                    ['judul' => 'HOK Pindah Tanam Bibit Sore Hari Blok Barat', 'nominal' => 300000, 'ket' => 'Menanam bibit saat teduh menghindari stres'],
                    ['judul' => 'HOK Penyulaman Bibit yang Mati/Layu HST 3', 'nominal' => 150000, 'ket' => 'Mengganti bibit rusak dengan sulaman baru'],
                    ['judul' => 'HOK Pembuatan Naungan Daun Pisang Bibit Baru', 'nominal' => 260000, 'ket' => 'Pemberian pelindung terik matahari untuk bibit muda'],
                    ['judul' => 'HOK Penyiraman Awal Kocor Basah Bibit', 'nominal' => 180000, 'ket' => 'Pengocoran air setelah tanam agar tanah menyatu'],
                ],
            ],
            // c) Sub: Mamin
            [
                'kategori' => 'Penanaman',
                'sub_kategori' => 'Mamin',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Konsumsi Sarapan & Kopi Pekerja Tanam Pagi', 'nominal' => 70000, 'ket' => 'Sarapan sebelum tanam jam 6 pagi'],
                    ['judul' => 'Makan Siang 4 Orang Tim Penanaman Bibit', 'nominal' => 100000, 'ket' => 'Nasi padang makan siang tukang tanam'],
                    ['judul' => 'Es Kelapa Muda & Gorengan Istirahat Tanam', 'nominal' => 50000, 'ket' => 'Pelepas dahaga istirahat tengah hari'],
                    ['judul' => 'Mamin Pekerja Penyulaman Bibit Sore', 'nominal' => 40000, 'ket' => 'Snack sore pekerja penyulaman'],
                    ['judul' => 'Makan Siang Tim Tanam & Kocor Awal', 'nominal' => 95000, 'ket' => 'Konsumsi tim penanaman selesai blok utara'],
                ],
            ],

            // ── 4. Pemeliharaan ──
            // a) Nyemprot › Obat
            [
                'kategori' => 'Pemeliharaan',
                'sub_kategori' => 'Nyemprot › Obat',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Beli Insektisida Abamektin Pencegah Thrips', 'nominal' => 175000, 'ket' => 'Kemasan 250ml pencegah keriting daun'],
                    ['judul' => 'Beli Fungisida Mankozeb Kuning 1 Kg', 'nominal' => 145000, 'ket' => 'Fungisida kontak pencegah busuk batang dan daun'],
                    ['judul' => 'Beli Perekat & Perata Pestisida (Agristik)', 'nominal' => 65000, 'ket' => 'Menjaga semprotan tidak luntur saat hujan'],
                    ['judul' => 'Beli Bakterisida Kasugamisin Layu Bakteri', 'nominal' => 135000, 'ket' => 'Proteksi tanaman dari penyakit layu lendir'],
                    ['judul' => 'Beli Akarisida Pembasmi Tungau Merah', 'nominal' => 160000, 'ket' => 'Kemasan 100ml pembasmi tungau daun bawah'],
                ],
            ],
            // b) Nyemprot › HOK Penyemprotan
            [
                'kategori' => 'Pemeliharaan',
                'sub_kategori' => 'Nyemprot › HOK Penyemprotan',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'HOK Penyemprotan Insektisida Rutin HST 14', 'nominal' => 120000, 'ket' => 'Semprot pagi hari 4 tangki sprayer'],
                    ['judul' => 'HOK Spray Fungisida & ZPT Pemacu Tunas HST 21', 'nominal' => 130000, 'ket' => 'Penyemprotan pemacu cabang produktif'],
                    ['judul' => 'HOK Penyemprotan Daun Pengendali Kutu Kebul', 'nominal' => 120000, 'ket' => 'Pencegahan penular virus kuning pada daun'],
                    ['judul' => 'HOK Spray Nutrisi Kalsium-Boron Buah HST 35', 'nominal' => 125000, 'ket' => 'Nutrisi pelindung pantat buah tidak busuk'],
                    ['judul' => 'HOK Penyemprotan Massal Pencegahan Antraknosa', 'nominal' => 140000, 'ket' => 'Pencegahan patek jelang musim hujan'],
                ],
            ],
            // c) Pemupukan › Pupuk
            [
                'kategori' => 'Pemeliharaan',
                'sub_kategori' => 'Pemupukan › Pupuk',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Beli NPK Mutiara 16-16-16 (1 Sak 50kg)', 'nominal' => 850000, 'ket' => 'Pupuk utama fase vegetatif dan generatif'],
                    ['judul' => 'Beli Pupuk KNO3 Merah Fase Vegetatif (5kg)', 'nominal' => 210000, 'ket' => 'Merangsang pertumbuhan akar dan anakan'],
                    ['judul' => 'Beli Pupuk KNO3 Putih Pembungaan (5kg)', 'nominal' => 235000, 'ket' => 'Mencegah kerontokan bunga dan bobot buah'],
                    ['judul' => 'Beli Kalsium Nitrat (CNG) Anti Busuk Pantat', 'nominal' => 185000, 'ket' => 'Kalsium larut air untuk kuatkan dinding sel buah'],
                    ['judul' => 'Beli Pupuk Organik Cair Asam Humat 5 Liter', 'nominal' => 165000, 'ket' => 'Kondisioner tanah dan pengikat hara'],
                ],
            ],
            // d) Pemupukan › HOK Pemupukan
            [
                'kategori' => 'Pemeliharaan',
                'sub_kategori' => 'Pemupukan › HOK Pemupukan',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'HOK Kocor NPK + Asam Humat HST 7', 'nominal' => 150000, 'ket' => 'Kocor pertama dosis 5 gram/liter'],
                    ['judul' => 'HOK Pemupukan Kocor Susulan Ke-2 HST 14', 'nominal' => 150000, 'ket' => 'Kocor susulan mempercepat pembesaran batang'],
                    ['judul' => 'HOK Tabur NPK di Lubang Tugal HST 28', 'nominal' => 170000, 'ket' => 'Tugal pupuk kering di sela tanaman'],
                    ['judul' => 'HOK Kocor Pupuk Penguat Buah HST 42', 'nominal' => 160000, 'ket' => 'Aplikasi KNO3 putih dan kalsium kocor'],
                    ['judul' => 'HOK Aplikasi Kocor Booster Buah HST 56', 'nominal' => 160000, 'ket' => 'Pemberian pupuk kalium tinggi menjelang panen'],
                ],
            ],

            // ── 5. Panen ──
            // a) BOP Panen › HOK Panen (Pengeluaran)
            [
                'kategori' => 'Panen',
                'sub_kategori' => 'BOP Panen › HOK Panen',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'HOK Petik Buah Panen Perdana HST 65', 'nominal' => 360000, 'ket' => 'Upah 4 orang tenaga petik panen awal'],
                    ['judul' => 'HOK Petik Buah Panen Ke-2 Blok A', 'nominal' => 450000, 'ket' => 'Petik buah merah 5 orang tenaga kerja'],
                    ['judul' => 'HOK Petik Buah Panen Ke-3 Puncak Panen', 'nominal' => 500000, 'ket' => 'Panen raya buah pilihan kualitas super'],
                    ['judul' => 'HOK Panen Ke-4 & Pengumpulan Keranjang', 'nominal' => 420000, 'ket' => 'Petik dan angkut ke gudang transit'],
                    ['judul' => 'HOK Petik Buah Panen Ke-5 Akhir Periode', 'nominal' => 380000, 'ket' => 'Pembersihan sisa panen petikan terakhir'],
                ],
            ],
            // b) BOP Panen › Mamin (Pengeluaran)
            [
                'kategori' => 'Panen',
                'sub_kategori' => 'BOP Panen › Mamin',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Makan Siang & Mamin Tenaga Petik Panen 1', 'nominal' => 130000, 'ket' => 'Nasi bungkus dan es teh tim panen pertama'],
                    ['judul' => 'Kopi, Es Teh & Gorengan Saat Panen Raya Ke-2', 'nominal' => 75000, 'ket' => 'Konsumsi istirahat siang hari terik'],
                    ['judul' => 'Makan Siang & Snack Sore Regu Petik Panen 3', 'nominal' => 140000, 'ket' => 'Makan siang 5 orang pemetik buah'],
                    ['judul' => 'Konsumsi Makan Siang Tenaga Petik Panen 4', 'nominal' => 120000, 'ket' => 'Nasi dan lauk pauk tim petik'],
                    ['judul' => 'Mamin & Minuman Dingin Petik Panen 5', 'nominal' => 70000, 'ket' => 'Es sirup dan roti bakar penutup panen'],
                ],
            ],
            // c) BOP Panen › Hasil Panen (Pengeluaran Operasional / Distribusi)
            [
                'kategori' => 'Panen',
                'sub_kategori' => 'BOP Panen › Hasil Panen',
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Sewa Pikap Angkut Hasil Panen Ke Pasar', 'nominal' => 350000, 'ket' => 'Ongkos kirim 1 armada pikap ke pasar induk'],
                    ['judul' => 'Upah Sortasi & Grading Kualitas Super', 'nominal' => 200000, 'ket' => 'Pemisahan kualitas grade A, B, dan afkir'],
                    ['judul' => 'Beli Keranjang Panen Plastik & Karung Jaring', 'nominal' => 220000, 'ket' => 'Wadah buah agar sirkulasi udara terjaga'],
                    ['judul' => 'Beli Kardus Packing & Lakban Hasil Panen', 'nominal' => 180000, 'ket' => 'Pengemasan pesanan supermarket'],
                    ['judul' => 'Upah Kuli Bongkar Muat di Pengepul', 'nominal' => 150000, 'ket' => 'Bongkar muat keranjang buah ke lapak juragan'],
                ],
            ],
            // d) Hasil Panen (Pemasukan)
            [
                'kategori' => 'Panen',
                'sub_kategori' => 'Hasil Panen',
                'tipe' => 'pemasukan',
                'items' => [
                    ['judul' => 'Penjualan Panen Perdana 180 kg ke Tengkulak', 'nominal' => 2700000, 'ket' => 'Harga Rp 15.000/kg tunai di timbangan'],
                    ['judul' => 'Penjualan Panen Ke-2 350 kg Grade A Pasar Induk', 'nominal' => 5250000, 'ket' => 'Harga Rp 15.000/kg kualitas super'],
                    ['judul' => 'Penjualan Panen Ke-3 Raya 480 kg Grosir', 'nominal' => 7200000, 'ket' => 'Puncak panen raya 480kg harga Rp 15.000/kg'],
                    ['judul' => 'Penjualan Panen Ke-4 280 kg ke Lapak Sayur', 'nominal' => 4480000, 'ket' => 'Harga Rp 16.000/kg pesanan lapak langganan'],
                    ['judul' => 'Penjualan Panen Ke-5 190 kg Partai Terakhir', 'nominal' => 2850000, 'ket' => 'Penutupan panen 190kg harga Rp 15.000/kg'],
                ],
            ],

            // ── 6. Perlengkapan (Tanpa Sub) ──
            [
                'kategori' => 'Perlengkapan',
                'sub_kategori' => null,
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Beli Ajir / Lanjaran Bambu 500 Batang', 'nominal' => 350000, 'ket' => 'Lanjaran penopang tanaman agar tidak rebah'],
                    ['judul' => 'Beli Tali Salaran Tali Pertanian 5 Gulung', 'nominal' => 125000, 'ket' => 'Tali pengikat batang ke ajir bambu'],
                    ['judul' => 'Beli Selang Spiral & Pipa Irigasi Tetes 50M', 'nominal' => 280000, 'ket' => 'Pemasangan jalur irigasi air otomatis'],
                    ['judul' => 'Beli Waring Jaring Pagar Keliling Anti Hama', 'nominal' => 320000, 'ket' => 'Pagar keliling pengaman kebun dari unggas/hewan'],
                    ['judul' => 'Beli Terpal Plastik Jemur / Penutup Pupuk 4x6M', 'nominal' => 160000, 'ket' => 'Penutup tumpukan kohe saat fermentasi'],
                ],
            ],

            // ── 7. Peralatan (Tanpa Sub) ──
            [
                'kategori' => 'Peralatan',
                'sub_kategori' => null,
                'tipe' => 'pengeluaran',
                'items' => [
                    ['judul' => 'Beli Tangki Sprayer Elektrik 16 Liter CBA', 'nominal' => 480000, 'ket' => 'Sprayer otomatis untuk efisiensi semprot'],
                    ['judul' => 'Beli Cangkul Baja & Gagang Kayu Jati (2 Unit)', 'nominal' => 190000, 'ket' => 'Alat utama pengolahan tanah bedengan'],
                    ['judul' => 'Beli Gunting Pangkas / Pruning Buah (2 Pcs)', 'nominal' => 95000, 'ket' => 'Gunting panen tajam anti karat'],
                    ['judul' => 'Beli Gembor Penyiraman Seng 10 Liter', 'nominal' => 85000, 'ket' => 'Penyiram manual bibit di tray semai'],
                    ['judul' => 'Beli Timbangan Duduk Digital 100 kg Panen', 'nominal' => 420000, 'ket' => 'Timbangan akurat untuk hasil petikan kebun'],
                ],
            ],
        ];

        $dayOffset = 0;
        foreach ($data as $group) {
            foreach ($group['items'] as $item) {
                $txDate = (clone $startDate)->addDays($dayOffset % 55);
                $dayOffset += 2;

                FinancialTransaction::create([
                    'crop_id' => $cropId,
                    'tipe' => $group['tipe'],
                    'kategori' => $group['kategori'],
                    'sub_kategori' => $group['sub_kategori'],
                    'judul' => $item['judul'],
                    'nominal' => $item['nominal'],
                    'tanggal' => $txDate,
                    'keterangan' => $item['ket'],
                    'foto_nota' => null,
                ]);
            }
        }
    }
}
