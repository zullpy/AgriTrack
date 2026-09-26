<?php

namespace Database\Seeders;

use App\Models\LandPreparationStep;
use Illuminate\Database\Seeder;

class LandPreparationStepSeeder extends Seeder
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
                'judul' => 'Sanitasi & Pembersihan Lahan',
                'waktu' => 'H-30 s/d H-21 Sebelum Tanam',
                'deskripsi' => 'Bersihkan lahan dari sisa-sisa tanaman musim tanam sebelumnya, tunggul kayu, semak, dan gulma berakar keras. Pembersihan ini bertujuan memutus siklus hidup hama (seperti ulat grayak, penggerek batang) dan inang penyakit jamur atau nematoda tanah.',
                'tips' => 'Gulma yang dibabat sebaiknya dikumpulkan dan dibuat kompos terpisah atau dibakar jika terindikasi terserang virus/bakteri tular tanah.',
                'spesifikasi' => null,
                'foto' => null,
            ],
            [
                'nomor' => '2',
                'urutan' => 2,
                'judul' => 'Pembalikan & Penggemburan Tanah (Bajak I)',
                'waktu' => 'H-21 s/d H-15 Sebelum Tanam',
                'deskripsi' => 'Cangkul atau bajak tanah dengan kedalaman 25–30 cm. Balikkan bongkahan tanah agar lapisan bawah terangkat ke permukaan dan terpapar sinar matahari secara merata selama 5–7 hari (proses solarisasi alami) guna membunuh telur serangga hama dan spora patogen tanah.',
                'tips' => 'Lakukan pembajakan saat kondisi tanah tidak terlalu basah/becek (macak-macak ringan) agar struktur agregat tanah tidak rusak memadat.',
                'spesifikasi' => null,
                'foto' => null,
            ],
            [
                'nomor' => '3',
                'urutan' => 3,
                'judul' => 'Uji pH Tanah & Aplikasi Kapur Dolomit',
                'waktu' => 'H-15 s/d H-10 Sebelum Tanam',
                'deskripsi' => 'Ukur pH tanah menggunakan pH meter tanah (pH ideal tanaman hortikultura adalah 6.0 – 6.8). Jika pH masam (< 5.5), taburkan kapur pertanian (Dolomit CaMg(CO3)2) sebanyak 1.5 – 2 ton/ha atau 150–200 gram per meter panjang bedengan. Dolomit juga menyuplai unsur mikro Kalsium (Ca) dan Magnesium (Mg).',
                'tips' => 'Beri jeda minimal 5–7 hari antara pemberian kapur dolomit dengan pupuk nitrogen/kandang agar tidak terjadi pelepasan amonia yang mengurangi efisiensi pupuk.',
                'spesifikasi' => null,
                'foto' => null,
            ],
            [
                'nomor' => '4',
                'urutan' => 4,
                'judul' => 'Aplikasi Pupuk Dasar Organik & Agens Hayati',
                'waktu' => 'H-10 s/d H-7 Sebelum Tanam',
                'deskripsi' => 'Taburkan pupuk kandang matang/kompos fermentasi (1–2 kg per meter bedengan) bersama pupuk dasar kimia lambat urai seperti SP-36 atau NPK seimbang. Inokulasi agens hayati Trichoderma sp. bersama pupuk kandang untuk mencegah serangan penyakit layu jamur fusarium.',
                'tips' => 'Pastikan pupuk kandang sudah terfermentasi sempurna (dingin, tidak berbau menyengat, warna cokelat kehitaman) agar tidak membakar akar bibit baru.',
                'spesifikasi' => null,
                'foto' => null,
            ],
            [
                'nomor' => '5',
                'urutan' => 5,
                'judul' => 'Penggemburan Halus (Rotary) & Pembuatan Bedengan',
                'waktu' => 'H-7 s/d H-4 Sebelum Tanam',
                'deskripsi' => 'Cacah kembali tanah agar pupuk kandang tercampur rata. Bentuk bedengan membujur dari Timur ke Barat dengan spesifikasi standar:',
                'tips' => null,
                'spesifikasi' => [
                    ['label' => 'Lebar Bedengan', 'nilai' => '100 – 110 cm'],
                    ['label' => 'Tinggi Bedengan', 'nilai' => '30 – 40 cm'],
                    ['label' => 'Lebar Parit', 'nilai' => '50 – 60 cm'],
                    ['label' => 'Kemiringan Parit', 'nilai' => '1 – 2% (Lancar)'],
                ],
                'foto' => null,
            ],
            [
                'nomor' => '6',
                'urutan' => 6,
                'judul' => 'Pemasangan Mulsa Plastik Hitam Perak (MPHP) & Pelubangan',
                'waktu' => 'H-3 s/d H-1 Sebelum Tanam',
                'deskripsi' => 'Pasang mulsa plastik saat siang hari yang terik agar plastik memuai dan dapat ditarik dengan kencang tanpa mudah kendur. Bagian berwarna perak menghadap ke atas (memantulkan sinar UV untuk mengusir thrips/aphids) dan hitam ke bawah (menahan gulma). Buat lubang tanam menggunakan kaleng panas sesuai jarak tanam varietas (misal 50 x 60 cm zig-zag).',
                'tips' => 'Setelah mulsa dilubangi, biarkan bedengan terkena udara selama 1–2 hari agar gas beracun sisa fermentasi di bawah mulsa dapat keluar sebelum bibit dimasukkan.',
                'spesifikasi' => null,
                'foto' => null,
            ],
        ];

        foreach ($steps as $step) {
            LandPreparationStep::updateOrCreate(
                ['nomor' => $step['nomor']],
                $step
            );
        }
    }
}
