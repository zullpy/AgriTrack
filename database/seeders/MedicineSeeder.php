<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        Medicine::create([
            'nama' => 'Decis 25 EC',
            'jenis' => 'Pestisida',
            'tanaman_sasaran' => 'Padi, Jagung',
            'dosis_anjuran' => '2 ml/liter air',
            'interval_aplikasi' => 'Setiap 7 hari',
            'catatan_keamanan' => 'Gunakan sarung tangan dan masker saat penyemprotan.',
            'stok' => 12,
        ]);

        Medicine::create([
            'nama' => 'Gandasil D',
            'jenis' => 'Pupuk',
            'tanaman_sasaran' => 'Semua Tanaman',
            'dosis_anjuran' => '10-30 gram/10 liter air',
            'interval_aplikasi' => 'Setiap 10 hari',
            'catatan_keamanan' => 'Simpan di tempat kering dan sejuk.',
            'stok' => 4,
        ]);

        Medicine::create([
            'nama' => 'Roundup 486 SL',
            'jenis' => 'Herbisida',
            'tanaman_sasaran' => 'Gulma Lahan',
            'dosis_anjuran' => '5-10 ml/liter air',
            'interval_aplikasi' => 'Sesuai kebutuhan',
            'catatan_keamanan' => 'Hindari kontak langsung dengan tanaman utama.',
            'stok' => 0,
        ]);

        Medicine::create([
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'dosis_anjuran' => '1.5-2 gram/liter air',
            'interval_aplikasi' => 'Setiap 7 hari',
            'catatan_keamanan' => 'Aplikasi sebelum timbul gejala penyakit berat.',
            'stok' => 8,
        ]);
    }
}
