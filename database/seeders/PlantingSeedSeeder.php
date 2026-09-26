<?php

namespace Database\Seeders;

use App\Models\PlantingSeed;
use App\Models\PlantingStep;
use Illuminate\Database\Seeder;

class PlantingSeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedsData = [
            [
                'nama_bibit' => 'Bibit Wortel',
                'varietas' => 'Kuroda / Chantenay',
                'deskripsi' => 'Wortel ditabur langsung (direct seeding) di bedengan tanah gembur tanpa proses semai tray agar akar tunggang tidak bercabang atau patah.',
                'urutan' => 1,
                'steps' => [
                    [
                        'nomor' => '1',
                        'urutan' => 1,
                        'judul' => 'Seleksi & Uji Daya Kecambah Benih Wortel',
                        'waktu' => 'H-3 Sebelum Tanam',
                        'deskripsi' => 'Pilih benih wortel bersertifikat resmi dengan persentase daya tumbuh minimal 80%. Lakukan perendaman benih selama 12-24 jam dalam air hangat kuku untuk melunakkan kulit benih wortel yang keras agar perkecambahan seragam.',
                        'tips' => 'Setelah direndam, tiriskan dan angin-anginkan benih hingga agak kering (tidak menggumpal) agar mudah ditabur secara merata di garitan.',
                    ],
                    [
                        'nomor' => '2',
                        'urutan' => 2,
                        'judul' => 'Pembuatan Garitan Dangkal di Atas Bedengan',
                        'waktu' => 'H-1 Sebelum Tanam',
                        'deskripsi' => 'Buat alur/garitan lurus dangkal sedalam 1–1.5 cm di permukaan bedengan yang telah gembur halus. Jarak antar alur garitan diatur 15–20 cm untuk memudahkan sirkulasi udara dan sanitasi gulma.',
                        'tips' => 'Gunakan bilah kayu lurus yang ditekan ringan agar kedalaman garitan rata. Hindari garitan terlalu dalam (> 2 cm) karena benih wortel berukuran sangat kecil.',
                    ],
                    [
                        'nomor' => '3',
                        'urutan' => 3,
                        'judul' => 'Penaburan Benih & Penutupan Tipis Media',
                        'waktu' => 'Hari H Penanaman',
                        'deskripsi' => 'Campurkan benih wortel dengan pasir halus kering atau abu sekam (perbandingan 1:5) agar penaburan tidak terlalu rapat dan boros. Taburkan campuran benih secara tipis di sepanjang alur garitan, lalu tutup kembali dengan tanah remah atau kompos halus setebal 0.5 cm.',
                        'tips' => 'Siram perlahan menggunakan gembor bermata halus (spray lembut) agar benih tidak hanyut atau terkubur terlalu dalam.',
                    ],
                    [
                        'nomor' => '4',
                        'urutan' => 4,
                        'judul' => 'Penjarangan Tanaman (Thinning) & Penyiangan Gulma',
                        'waktu' => '15 – 20 HST (Hari Setelah Tanam)',
                        'deskripsi' => 'Saat bibit wortel sudah berdaun 3–4 helai dan tinggi 5–7 cm, lakukan penjarangan dengan mencabut bibit yang kerdil atau terlalu rapat sehingga tersisa jarak ideal 5–8 cm per tanaman.',
                        'tips' => 'Lakukan penjarangan saat tanah dalam kondisi lembap (setelah disiram) agar akar bibit yang disisakan tidak ikut terganggu atau stres.',
                    ],
                ],
            ],
            [
                'nama_bibit' => 'Bibit Cabai Rawit / Merah',
                'varietas' => 'Ori 212 / Imola F1',
                'deskripsi' => 'Cabai dibibitkan melalui tray semai khusus selama 21–28 hari hingga bibit kokoh dan berakar kuat sebelum dipindahkan ke lubang mulsa.',
                'urutan' => 2,
                'steps' => [
                    [
                        'nomor' => '1',
                        'urutan' => 1,
                        'judul' => 'Perlakuan Benih (Seed Treatment) & Perendaman ZPT',
                        'waktu' => 'H-28 Sebelum Tanam (Hari Semai)',
                        'deskripsi' => 'Rendam benih cabai dalam air hangat bersuhu 45–50°C selama 4–6 jam. Tambahkan larutan ZPT alami (ekstrak bawang merah) dan fungisida perlakuan benih untuk merangsang pembelahan sel serta mencegah penyakit rebah semai (damping-off).',
                        'tips' => 'Tiriskan benih di atas kain/tisu basah di tempat gelap hangat selama 24–48 jam hingga titik radikula (calon akar) mulai memutih.',
                    ],
                    [
                        'nomor' => '2',
                        'urutan' => 2,
                        'judul' => 'Penyemaian di Media Tray Semai Steril',
                        'waktu' => 'H-26 Sebelum Tanam',
                        'deskripsi' => 'Isi tray semai (lubang 98 atau 128) dengan media campuran tanah gembur, arang sekam, dan kompos matang terfermentasi (rasio 1:1:1). Masukkan 1 benih cabai per lubang sedalam 0.5 cm, lalu tutup tipis dengan media ayakan halus.',
                        'tips' => 'Tutup permukaan tray dengan karung goni basah selama 3 hari pertama sampai benih mulai berkecambah (muncul loop hijau).',
                    ],
                    [
                        'nomor' => '3',
                        'urutan' => 3,
                        'judul' => 'Pemeliharaan & Aklimatisasi (Hardening Off)',
                        'waktu' => 'H-10 s/d H-3 Sebelum Tanam',
                        'deskripsi' => 'Siram bibit semai setiap pagi dengan semprotan kabut halus. Tiga hari menjelang pindah tanam, kenalkan bibit ke sinar matahari penuh secara bertahap dan kurangi intensitas penyiraman agar batang mengeras (lignifikasi) dan kuat saat ditanam di lapangan terbuka.',
                        'tips' => 'Semprotkan pupuk daun berkalsium dan silika rendah dosis pada H-4 untuk menebalkan dinding sel bibit.',
                    ],
                    [
                        'nomor' => '4',
                        'urutan' => 4,
                        'judul' => 'Pindah Tanam (Transplanting) ke Bedengan Mulsa',
                        'waktu' => 'Hari H Penanaman (Sore Hari)',
                        'deskripsi' => 'Pindahkan bibit yang telah memiliki 4–6 helai daun sejati (umur 21–28 hari). Buat lubang tanam di bedengan MPHP sesuai ukuran perakaran tray semai. Tanam bibit tegak lurus dan tekan tanah di sekitarnya secara hati-hati.',
                        'tips' => 'Lakukan penanaman pada sore hari (pukul 15.30 ke atas) saat terik matahari berkurang drastis guna meminimalkan stres transpirasi.',
                    ],
                ],
            ],
            [
                'nama_bibit' => 'Bibit Tomat Hibrida',
                'varietas' => 'Servo F1 / Timoty',
                'deskripsi' => 'Tomat membutuhkan bibit sehat dengan daun 4-5 helai sebelum transplanting untuk ketahanan optimal terhadap layu bakteri dan virus.',
                'urutan' => 3,
                'steps' => [
                    [
                        'nomor' => '1',
                        'urutan' => 1,
                        'judul' => 'Pemilihan Benih Hibrida Tahan Virus & Layu',
                        'waktu' => 'H-25 Sebelum Tanam',
                        'deskripsi' => 'Gunakan benih hibrida unggul yang memiliki resistensi terhadap Geminivirus (bule) dan layu bakteri (Ralstonia solanacearum). Rendam benih dalam larutan air hangat selama 2–3 jam.',
                        'tips' => 'Periksa kemasan benih dan pastikan segel aluminium foil pabrik masih kedap udara.',
                    ],
                    [
                        'nomor' => '2',
                        'urutan' => 2,
                        'judul' => 'Penyemaian di Polybag Kecil atau Tray Semai',
                        'waktu' => 'H-23 Sebelum Tanam',
                        'deskripsi' => 'Semaikan benih di tray lubang besar atau bumbunan daun pisang mini. Berikan sungkup plastik UV atau paranet 50% untuk melindungi semai muda dari terpaan air hujan deras dan terik langsung.',
                        'tips' => 'Inokulasi agens hayati Trichoderma harzianum pada media semai untuk melindungi perakaran bibit sedini mungkin.',
                    ],
                    [
                        'nomor' => '3',
                        'urutan' => 3,
                        'judul' => 'Pindah Tanam & Kocor Larutan Pencegah Rebah',
                        'waktu' => 'Hari H Penanaman',
                        'deskripsi' => 'Pilih bibit berbatang kekar, daun hijau segar (bukan kurus jangkung). Masukkan ke dalam lubang tanam sedalam pangkal batang dan segera kocor dengan 200–250 ml air per lubang yang dicampur pupuk starter fosfat tinggi.',
                        'tips' => 'Segera pasang ajir/turus bambu di dekat tanaman 1–2 hari setelah tanam sebelum akar tomat menyebar lebar.',
                    ],
                ],
            ],
        ];

        foreach ($seedsData as $seedItem) {
            $steps = $seedItem['steps'] ?? [];
            unset($seedItem['steps']);

            $seed = PlantingSeed::updateOrCreate(
                ['nama_bibit' => $seedItem['nama_bibit']],
                $seedItem
            );

            foreach ($steps as $stepData) {
                PlantingStep::updateOrCreate(
                    [
                        'planting_seed_id' => $seed->id,
                        'nomor' => $stepData['nomor'],
                    ],
                    $stepData
                );
            }
        }
    }
}
