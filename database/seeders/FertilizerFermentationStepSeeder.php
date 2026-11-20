<?php

namespace Database\Seeders;

use App\Models\FertilizerFermentationStep;
use Illuminate\Database\Seeder;

class FertilizerFermentationStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $steps = [
            [
                'nomor' => '1',
                'urutan' => 1,
                'judul' => 'Persiapan & Sanitasi Bahan Baku Kohe & Arang Sekam',
                'waktu' => 'Hari ke-1 (H-30 Sebelum Tebar Lahan)',
                'deskripsi' => 'Siapkan kotoran hewan (kohe kambing, domba, sapi, atau ayam) yang sudah diangin-anginkan (kondisi lembap tiris, tidak basah kuyup). Campurkan dengan bahan peremah berongga seperti arang sekam, serbuk gergaji lapuk, atau dedak halus dengan rasio 4 bagian kohe : 1 bagian arang sekam/dedak. Arang sekam berfungsi menjaga kegemburan dan sirkulasi oksigen di dalam tumpukan.',
                'tips' => 'Hindari langsung menggunakan kohe segar ke tanaman karena gas amonia dan panasnya dapat membakar akar bibit muda serta memicu serangan jamur layu tular tanah.',
                'spesifikasi' => [
                    ['label' => 'Bahan Baku Utama', 'nilai' => 'Kohe Kambing / Ayam / Sapi (80%)'],
                    ['label' => 'Bahan Campuran', 'nilai' => 'Arang Sekam & Dedak Padi Halus (20%)'],
                    ['label' => 'Lokasi Penumpukan', 'nilai' => 'Teduh, beratap / terlindung dari hujan deras'],
                ],
                'foto' => null,
            ],
            [
                'nomor' => '2',
                'urutan' => 2,
                'judul' => 'Pembuatan Larutan Aktivator (EM4 / M21 & Molase)',
                'waktu' => 'Hari ke-1 (Sebelum Pencampuran)',
                'deskripsi' => 'Larutkan 100–200 ml bakteri pengurai (seperti EM4 Pertanian atau M21 Decomposer) bersama 100–200 ml tetes tebu (molase) atau larutan gula merah ke dalam 10–20 liter air bersih. Diamkan larutan selama 15–30 menit di tempat teduh agar mikroba fotosintetik, bakteri asam laktat, dan ragi bangun dari dormansi dan aktif berkembang biak.',
                'tips' => 'Gunakan air sumur atau air hujan yang bebas kaporit. Klorin/kaporit pada air PDAM dapat membunuh koloni mikroba pengurai yang berguna.',
                'spesifikasi' => [
                    ['label' => 'Dosis Decomposer', 'nilai' => '10 – 20 ml / Liter air (1:100)'],
                    ['label' => 'Dosis Molase / Gula', 'nilai' => '10 – 20 ml / Liter air (Perbandingan 1:1)'],
                    ['label' => 'Waktu Aktivasi Awal', 'nilai' => '15 – 30 Menit sebelum disiramkan'],
                ],
                'foto' => null,
            ],
            [
                'nomor' => '3',
                'urutan' => 3,
                'judul' => 'Pencampuran & Pengaturan Kadar Air (40–50%)',
                'waktu' => 'Hari ke-1 s/d Hari ke-2',
                'deskripsi' => 'Gelar bahan kohe lapis demi lapis setinggi 15–20 cm, lalu siramkan larutan aktivator secara merata menggunakan gembor. Aduk merata memakai cangkul atau garpu. Lakukan "Uji Kepal Tangan": ambil segenggam pupuk lalu remas kuat; jika air tidak menetes keluar di sela jari namun saat kepalan dibuka bahan tetap menggumpal utuh (tidak buyar), maka kadar air telah ideal (40–50%).',
                'tips' => 'Jika terlalu basah (>60%), fermentasi berubah menjadi anaerob pembusukan dan berbau busuk tajam. Jika terlalu kering (<30%), aktivitas bakteri dekomposer terhenti.',
                'spesifikasi' => [
                    ['label' => 'Kadar Air Ideal', 'nilai' => '40% – 50% (Lembap Tiris)'],
                    ['label' => 'Indikator Uji Kepal', 'nilai' => 'Menggumpal, tidak meneteskan air'],
                    ['label' => 'Alat Kerja', 'nilai' => 'Gembor siram, cangkul & garpu tanah'],
                ],
                'foto' => null,
            ],
            [
                'nomor' => '4',
                'urutan' => 4,
                'judul' => 'Penumpukan & Penutupan Terpal (Fase Termofilik)',
                'waktu' => 'Hari ke-2 s/d Hari ke-7',
                'deskripsi' => 'Tumpuk bahan pupuk membentuk gundukan trapesium dengan tinggi minimal 80–120 cm agar panas alami fermentasi dapat terperangkap stabil. Tutup rapat seluruh permukaan tumpukan dengan terpal plastik gelap atau karung goni. Dalam kurun waktu 48–72 jam, suhu di dalam tumpukan akan melonjak naik mencapai 50–65°C.',
                'tips' => 'Suhu tinggi termofilik (50–65°C) ini sangat penting untuk mensterilkan pupuk dari biji gulma liar yang terbawa dari pakan ternak serta membasmi bakteri patogen usus berbahaya seperti Salmonella dan E. coli.',
                'spesifikasi' => [
                    ['label' => 'Tinggi Gundukan', 'nilai' => '80 – 120 cm'],
                    ['label' => 'Suhu Termofilik', 'nilai' => '50°C – 65°C'],
                    ['label' => 'Material Penutup', 'nilai' => 'Terpal plastik tebal kedap cahaya'],
                ],
                'foto' => null,
            ],
            [
                'nomor' => '5',
                'urutan' => 5,
                'judul' => 'Pembalikan & Aerasi Rutin (Kontrol Suhu & Oksigen)',
                'waktu' => 'Hari ke-5, 10, dan 15',
                'deskripsi' => 'Buka terpal dan balikkan seluruh tumpukan pupuk (bagian luar dipindahkan ke bagian dalam, dan bagian tengah dibawa ke luar) secara berkala setiap 5 hari sekali. Hal ini bertujuan menyuplai oksigen segar bagi bakteri aerob dan mencegah suhu melonjak melebihi 70°C yang dapat membakar hangus bahan organik.',
                'tips' => 'Lakukan pembalikan di pagi hari saat udara masih sejuk. Jika tumpukan tampak mengering saat dibalik, semprotkan sedikit air bersih berdecomposer tipis-tipis.',
                'spesifikasi' => [
                    ['label' => 'Jadwal Pembalikan', 'nilai' => 'Tiap 5 hari sekali (minimal 3 kali)'],
                    ['label' => 'Batas Suhu Maksimal', 'nilai' => '< 70°C (Jangan sampai hangus)'],
                    ['label' => 'Ciri Proses Baik', 'nilai' => 'Terbentuk hifa jamur putih di lapisan'],
                ],
                'foto' => null,
            ],
            [
                'nomor' => '6',
                'urutan' => 6,
                'judul' => 'Inokulasi Trichoderma sp., Uji Kematangan & Penyimpanan',
                'waktu' => 'Hari ke-21 s/d Hari ke-30 (Siap Tebar)',
                'deskripsi' => 'Fermentasi selesai jika suhu tumpukan sudah turun dingin (suhu ruang < 35°C), warna cokelat tua kehitaman mirip tanah subur, tekstur sangat remah mudah dihancurkan, dan beraroma wangi humus hutan (tidak ada lagi aroma kotoran). Pada saat pupuk dingin ini, taburkan dan campur agens hayati Trichoderma sp. (1–2 kg per ton pupuk) sebagai pelindung anti jamur fusarium di lahan.',
                'tips' => 'Jangan pernah mencampurkan agens hayati Trichoderma saat pupuk masih panas di fase awal karena spora Trichoderma akan mati terbakar panas.',
                'spesifikasi' => [
                    ['label' => 'Suhu Akhir', 'nilai' => 'Dingin (< 35°C / Suhu Ruang)'],
                    ['label' => 'Warna & Aroma', 'nilai' => 'Hitam kecokelatan, wangi tanah remah'],
                    ['label' => 'Agens Hayati Tambahan', 'nilai' => 'Trichoderma harzianum (1–2 kg / Ton)'],
                    ['label' => 'Masa Simpan', 'nilai' => '6 – 12 Bulan di karung kering'],
                ],
                'foto' => null,
            ],
        ];

        foreach ($steps as $step) {
            FertilizerFermentationStep::updateOrCreate(
                ['nomor' => $step['nomor']],
                $step
            );
        }
    }
}
