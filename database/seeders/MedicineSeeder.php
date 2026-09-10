<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        Medicine::truncate();

        Medicine::create([
            'nama' => 'Curacron 500 EC',
            'jenis' => 'Insektisida',
            'cara_kerja' => 'Kontak',
            'tanaman_sasaran' => 'Cabai, Bawang Merah, Tomat',
            'dosis_anjuran' => '15 ml / 16 liter air',
            'interval_aplikasi' => 'Setiap 5-7 hari',
            'harga' => 68000,
            'unsur_bahan' => 'Profilofos 500 g/l',
            'catatan_keamanan' => 'Gunakan masker dan APD lengkap saat penyemprotan.',
            'keterangan' => 'Efektif mengendalikan ulat grayak, kutu daun, dan thrips.',
            'tanggal_beli' => '2026-08-15',
            'toko_obat' => 'Toko Tani Makmur',
        ]);

        Medicine::create([
            'nama' => 'Dithane M-45 80 WP',
            'jenis' => 'Fungisida',
            'cara_kerja' => 'Kontak',
            'tanaman_sasaran' => 'Cabai, Tomat, Kentang, Bawang',
            'dosis_anjuran' => '2 sendok makan / 16 liter air',
            'interval_aplikasi' => 'Setiap 7 hari',
            'harga' => 85000,
            'unsur_bahan' => 'Mankozeb 80%',
            'catatan_keamanan' => 'Jangan dicampur langsung dengan pestisida sangat alkalis.',
            'keterangan' => 'Fungisida protektif spektrum luas mencegah bercak daun dan busuk daun.',
            'tanggal_beli' => '2026-08-20',
            'toko_obat' => 'Kios Pertanian Subur',
        ]);

        Medicine::create([
            'nama' => 'Gandasil D',
            'jenis' => 'Pupuk',
            'cara_kerja' => 'Sistemik',
            'tanaman_sasaran' => 'Semua Tanaman (Fase Vegetatif)',
            'dosis_anjuran' => '2 sendok makan / 16 liter air',
            'interval_aplikasi' => 'Setiap 7-10 hari',
            'harga' => 25000,
            'unsur_bahan' => 'N: 20%, P2O5: 15%, K2O: 15% + Mikro (Mg, Mn, B, Cu, Co, Zn)',
            'catatan_keamanan' => 'Simpan di tempat kering dan sejuk terhindar dari sinar matahari langsung.',
            'keterangan' => 'Pupuk daun lengkap untuk memacu pertumbuhan tunas, batang, dan daun hijau.',
            'tanggal_beli' => '2026-09-01',
            'toko_obat' => 'Toko Tani Makmur',
        ]);

        Medicine::create([
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'cara_kerja' => 'Kontak',
            'tanaman_sasaran' => 'Cabai, Tomat, Padi, Semangka',
            'dosis_anjuran' => '1.5 - 2 gram / liter air',
            'interval_aplikasi' => 'Setiap 7 hari',
            'harga' => 52000,
            'unsur_bahan' => 'Propineb 70% + Zinc',
            'catatan_keamanan' => 'Aplikasi pencegahan sebelum timbul gejala serangan penyakit.',
            'keterangan' => 'Mengandung unsur mikro Zinc yang memberi efek menghijaukan tanaman.',
            'tanggal_beli' => '2026-08-28',
            'toko_obat' => 'Sentra Tani Barokah',
        ]);

        Medicine::create([
            'nama' => 'Furadan 3GR',
            'jenis' => 'Insektisida',
            'cara_kerja' => 'Sistemik',
            'tanaman_sasaran' => 'Tanah Kebun, Padi, Jagung, Cabai',
            'dosis_anjuran' => '5-10 gram / lubang tanam atau bedengan',
            'interval_aplikasi' => 'Saat olah lahan / pemupukan dasar',
            'harga' => 45000,
            'unsur_bahan' => 'Karbofuran 3%',
            'catatan_keamanan' => 'Obat keras berbentuk granul. Tabur langsung ke tanah, dilarang disemprot.',
            'keterangan' => 'Membasmi uret, orong-orong, rayap, dan cacing tanah perusak akar muda.',
            'tanggal_beli' => '2026-08-10',
            'toko_obat' => 'Toko Tani Makmur',
        ]);

        Medicine::create([
            'nama' => 'Decis 25 EC',
            'jenis' => 'Insektisida',
            'cara_kerja' => 'Kontak',
            'tanaman_sasaran' => 'Padi, Jagung, Kedelai, Cabai',
            'dosis_anjuran' => '1 - 2 ml / liter air',
            'interval_aplikasi' => 'Setiap 7 hari saat ada serangan',
            'harga' => 38000,
            'unsur_bahan' => 'Deltametrin 25 g/l',
            'catatan_keamanan' => 'Gunakan sarung tangan dan masker saat penyemprotan.',
            'keterangan' => 'Insektisida kontak dan lambung racun knock down cepat.',
            'tanggal_beli' => '2026-09-02',
            'toko_obat' => 'Kios Pertanian Subur',
        ]);
    }
}
