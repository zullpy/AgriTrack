<?php

namespace Database\Seeders;

use App\Models\PlantCatalog;
use App\Models\PlantCatalogGuide;
use Illuminate\Database\Seeder;

class PlantCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $plants = [
            [
                'catalog' => [
                    'key' => 'timun',
                    'name' => 'Timun',
                    'emoji' => '🥒',
                    'cycle' => '35 – 45 HST',
                    'theme' => 'emerald',
                    'keywords' => 'timun, mentimun, cucumber',
                    'urutan' => 1,
                    'aktif' => true,
                ],
                'guides' => [
                    [
                        'phase' => 'Pemupukan Dasar',
                        'hst' => '0 HST (Sebelum Tanam)',
                        'focus' => 'Membangun struktur tanah & stok hara awal',
                        'nutrients' => ['Pupuk Kandang / Kompos', 'NPK 16-16-16', 'SP-36', 'Dolomit'],
                        'dosis' => 'Pupuk kandang 1 kg/lubang + NPK 10 gr/lubang + SP-36 5 gr/lubang.',
                        'metode' => 'Campur rata dalam lubang tanam, tutup tipis dengan tanah sebelum benih/bibit dimasukkan.',
                        'tips' => 'Pastikan pH tanah 6.0–6.8. Tanah masam hambat penyerapan P dan Ca secara signifikan.',
                        'urutan' => 1,
                    ],
                    [
                        'phase' => 'Vegetatif Awal',
                        'hst' => '7 – 14 HST',
                        'focus' => 'Memacu pertumbuhan akar & sulur merambat',
                        'nutrients' => ['NPK 16-16-16', 'Asam Humat', 'ZA (Nitrogen)'],
                        'dosis' => '5 gram NPK per liter air, kocor 200 ml per tanaman.',
                        'metode' => 'Kocor di sekitar perakaran, hindari terkena batang langsung.',
                        'tips' => 'Pasang ajir/tali rambatan lebih awal agar sulur tidak tumbuh ke tanah.',
                        'urutan' => 2,
                    ],
                    [
                        'phase' => 'Pembungaan & Buah Muda',
                        'hst' => '18 – 28 HST',
                        'focus' => 'Memperbanyak bunga betina & mencegah kerontokan buah',
                        'nutrients' => ['MKP (Monokalium Fosfat)', 'Kalsium Nitrat', 'Boron'],
                        'dosis' => 'MKP 2 gr + Kalsium Nitrat 3 gr per liter, semprot foliar pagi hari.',
                        'metode' => 'Semprot bagian bawah daun setiap 5–7 hari sekali.',
                        'tips' => 'Bunga jantan muncul lebih dulu, tunggu bunga betina (bertangkai buah kecil) sebelum kalkulasi set buah.',
                        'urutan' => 3,
                    ],
                    [
                        'phase' => 'Pembesaran Buah & Panen',
                        'hst' => '30 – 45 HST (Panen Bergulir)',
                        'focus' => 'Mempertahankan produktivitas panen bergulir',
                        'nutrients' => ['KNO3 (Kalium Nitrat)', 'NPK Buah', 'Magnesium Sulfat'],
                        'dosis' => '5–10 gram per liter air dikocorkan berkala setiap 5 hari sekali.',
                        'metode' => 'Kocorkan rutin setiap selesai putaran petik buah.',
                        'tips' => 'Petik buah 2 hari sekali di pagi hari agar tanaman terus terpacu memproduksi buah baru.',
                        'urutan' => 4,
                    ],
                ],
            ],

            [
                'catalog' => [
                    'key' => 'cabe',
                    'name' => 'Cabe',
                    'emoji' => '🌶️',
                    'cycle' => '75 – 120 HST',
                    'theme' => 'rose',
                    'keywords' => 'cabe, cabai, chili, lombok',
                    'urutan' => 2,
                    'aktif' => true,
                ],
                'guides' => [
                    [
                        'phase' => 'Pemupukan Dasar',
                        'hst' => '0 HST (Sebelum Tanam)',
                        'focus' => 'Penyesuaian pH tanah (netral) & pondasi unsur makro',
                        'nutrients' => ['Kapur Pertanian (Dolomit)', 'Pupuk Kandang', 'SP-36', 'Za'],
                        'dosis' => 'Dolomit 100 gr/meter bedengan + pupuk kandang 1.5 kg/meter + SP-36 15 gr/lubang.',
                        'metode' => 'Tebar di bedengan, biarkan terkena hujan/disiram sebelum ditutup mulsa perak hitam.',
                        'tips' => 'Kadar pH 6.0 – 6.8 sangat penting agar penyerapan pupuk susulan tidak terikat racun tanah.',
                        'urutan' => 1,
                    ],
                    [
                        'phase' => 'Vegetatif Percabangan',
                        'hst' => '10 – 35 HST',
                        'focus' => 'Pembentukan tunas aktif, cabang "Y", & batang kokoh',
                        'nutrients' => ['NPK 16-16-16', 'Asam Humat', 'Kalsium Nitrat'],
                        'dosis' => '5 gram NPK + 1 gram Asam Humat per liter air, kocor 200–250 ml per tanaman.',
                        'metode' => 'Kocor rutin setiap 7 hari sekali dengan jarak 10 cm dari pangkal batang.',
                        'tips' => 'Rempel tunas air di ketiak daun bawah sebelum cabang Y pertama agar sirkulasi udara baik.',
                        'urutan' => 2,
                    ],
                    [
                        'phase' => 'Inisiasi Bunga & Buah Muda',
                        'hst' => '40 – 65 HST',
                        'focus' => 'Kekuatan tangkai bunga, daya tahan hama, & buah lebat',
                        'nutrients' => ['MKP (Monokalium Fosfat)', 'Boron', 'Kalsium', 'Magnesium'],
                        'dosis' => 'MKP 2–3 gr/liter (foliar) semprot pagi + Kalsium 5 gr/liter kocor.',
                        'metode' => 'Semprot foliar interval 5–7 hari sekali, utamakan bagian bawah daun.',
                        'tips' => 'Hindari pupuk Urea berlebih saat bunga mulai muncul agar bunga tidak rontok.',
                        'urutan' => 3,
                    ],
                    [
                        'phase' => 'Pematangan & Panen',
                        'hst' => '70+ HST – Menjelang Panen',
                        'focus' => 'Warna merah mengkilap, kulit tebal, bobot berat, & pedas',
                        'nutrients' => ['KNO3 Putih', 'KCL', 'Kalium Sulfat tinggi'],
                        'dosis' => '10 gram per liter air untuk kocor per pokok, atau semprot pupuk buah.',
                        'metode' => 'Kocor teratur setiap setelah pemetikan buah panen.',
                        'tips' => 'Kandungan kalium dan kalsium tinggi membuat buah cabai tahan simpan dan tidak mudah busuk saat distribusi.',
                        'urutan' => 4,
                    ],
                ],
            ],

            [
                'catalog' => [
                    'key' => 'jagung',
                    'name' => 'Jagung',
                    'emoji' => '🌽',
                    'cycle' => '70 – 95 HST',
                    'theme' => 'amber',
                    'keywords' => 'jagung, corn, maize',
                    'urutan' => 3,
                    'aktif' => true,
                ],
                'guides' => [
                    [
                        'phase' => 'Pemupukan Dasar',
                        'hst' => '0 HST (Saat Tanam)',
                        'focus' => 'Memacu kecambah cepat & perakaran tunggang yang kuat',
                        'nutrients' => ['NPK 15-15-15 atau SP-36', 'Kompos Organik'],
                        'dosis' => '5–7 gram per lubang yang ditugal 7 cm di samping lubang benih.',
                        'metode' => 'Tugal di samping biji dan tutup dengan tanah gembur tipis.',
                        'tips' => 'Jangan letakkan pupuk anorganik menempel langsung pada biji agar benih tidak terbakar.',
                        'urutan' => 1,
                    ],
                    [
                        'phase' => 'Susulan 1 (Vegetatif Cepat)',
                        'hst' => '15 – 20 HST',
                        'focus' => 'Pertumbuhan batang tinggi, jumlah daun, & warna hijau pekat',
                        'nutrients' => ['Urea (Nitrogen Tinggi)', 'NPK Mutiara'],
                        'dosis' => 'Urea 7 gram + NPK 5 gram per tanaman ditugal.',
                        'metode' => 'Tugal 10 cm dari batang lalu lakukan pembumbunan tanah pada pokok tanaman.',
                        'tips' => 'Pembumbunan tanah sekaligus menutup pupuk agar tidak menguap dan menopang akar jangkar jagung.',
                        'urutan' => 2,
                    ],
                    [
                        'phase' => 'Susulan 2 (Bunga & Tongkol)',
                        'hst' => '35 – 45 HST',
                        'focus' => 'Persiapan malai bunga jantan & inisiasi bakal tongkol jagung',
                        'nutrients' => ['Urea', 'NPK Seimbang', 'KCL (Kalium Klorida)'],
                        'dosis' => 'Urea 5 gram + NPK 10 gram + KCL 5 gram per tanaman.',
                        'metode' => 'Tugal melingkar 15 cm dari batang jagung saat kondisi tanah lembap.',
                        'tips' => 'Ketersediaan air tanah sangat penting saat fase serbuk sari keluar agar tongkol tidak ompong.',
                        'urutan' => 3,
                    ],
                    [
                        'phase' => 'Pengisian Biji Tongkol',
                        'hst' => '55 – 75 HST',
                        'focus' => 'Pengisian biji penuh hingga ujung tongkol & rasa manis',
                        'nutrients' => ['Kalium Larut Air', 'Pupuk Daun Kalium Tinggi'],
                        'dosis' => 'Semprot pupuk daun kalium atau kocor bila diperlukan.',
                        'metode' => 'Aplikasi saat pagi hari sebelum terik matahari.',
                        'tips' => 'Untuk jagung manis, panen tepat saat rambut tongkol berwarna cokelat tua mengering (sekitar 70–75 HST).',
                        'urutan' => 4,
                    ],
                ],
            ],
        ];

        foreach ($plants as $data) {
            $catalog = PlantCatalog::updateOrCreate(
                ['key' => $data['catalog']['key']],
                $data['catalog'],
            );

            foreach ($data['guides'] as $guide) {
                PlantCatalogGuide::updateOrCreate(
                    [
                        'plant_catalog_id' => $catalog->id,
                        'phase' => $guide['phase'],
                    ],
                    array_merge($guide, ['plant_catalog_id' => $catalog->id]),
                );
            }
        }
    }
}
