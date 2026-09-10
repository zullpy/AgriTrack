<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\CropActivity;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CropSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // 1. Tanaman Aktif 1: Jagung Hibrida (Tanam 25 hari lalu)
        $jagung = Crop::create([
            'nama_tanaman' => 'Jagung Manis',
            'varietas' => 'Talenta F1',
            'tanggal_tanam' => $today->copy()->subDays(25),
            'status' => 'Sedang Ditanam',
            'catatan' => 'Lahan Blok A (1.200 m²), jarak tanam 70x20 cm.',
        ]);

        CropActivity::create([
            'crop_id' => $jagung->id,
            'nama_kegiatan' => 'Pemupukan Dasar & Penyiangan',
            'target_hst' => 10,
            'status' => 'Selesai',
            'tanggal_selesai' => $today->copy()->subDays(15),
            'catatan' => 'Pupuk kandang + NPK 15-15-15.',
        ]);

        CropActivity::create([
            'crop_id' => $jagung->id,
            'nama_kegiatan' => 'Penyemprotan Pencegahan Ulat Grayak',
            'target_hst' => 25,
            'status' => 'Belum',
            'catatan' => 'Gunakan insektisida sistemik dosis 2 ml/liter.',
        ]);

        CropActivity::create([
            'crop_id' => $jagung->id,
            'nama_kegiatan' => 'Pemupukan Susulan Kedua',
            'target_hst' => 45,
            'status' => 'Belum',
            'catatan' => 'Aplikasi Urea 150 kg/ha dan pembumbunan tanah.',
        ]);

        CropActivity::create([
            'crop_id' => $jagung->id,
            'nama_kegiatan' => 'Panen Jagung Muda',
            'target_hst' => 75,
            'status' => 'Belum',
            'catatan' => 'Panen saat tongkol terisi padat dan kelobot hijau kekuningan.',
        ]);

        // 2. Tanaman Aktif 2: Cabai Rawit (Tanam 12 hari lalu)
        $cabai = Crop::create([
            'nama_tanaman' => 'Cabai Rawit',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => $today->copy()->subDays(12),
            'status' => 'Sedang Ditanam',
            'catatan' => 'Bedengan Blok B (800 m²) dengan mulsa hitam perak.',
        ]);

        CropActivity::create([
            'crop_id' => $cabai->id,
            'nama_kegiatan' => 'Kocor Pupuk Daun Pertama',
            'target_hst' => 7,
            'status' => 'Selesai',
            'tanggal_selesai' => $today->copy()->subDays(5),
            'catatan' => 'Gandasil D konsentrasi 2 gr/liter.',
        ]);

        CropActivity::create([
            'crop_id' => $cabai->id,
            'nama_kegiatan' => 'Pemasangan Ajir Bambu',
            'target_hst' => 15,
            'status' => 'Belum',
            'catatan' => 'Pasang ajir tegak setinggi 1,5 meter untuk penopang.',
        ]);

        CropActivity::create([
            'crop_id' => $cabai->id,
            'nama_kegiatan' => 'Penyemprotan Antraknosa & Thrips',
            'target_hst' => 30,
            'status' => 'Belum',
            'catatan' => 'Rotasi bahan aktif fungisida.',
        ]);

        // 3. Tanaman Riwayat: Padi Ciherang (Sudah Dipanen)
        $padi = Crop::create([
            'nama_tanaman' => 'Padi Sawah',
            'varietas' => 'Inpari 32',
            'tanggal_tanam' => $today->copy()->subDays(125),
            'status' => 'Sudah Dipanen',
            'tanggal_panen' => $today->copy()->subDays(15),
            'total_hst_panen' => 110,
            'catatan' => 'Petak Sawah Barat. Hasil panen 6,8 ton GKP/ha.',
        ]);

        CropActivity::create([
            'crop_id' => $padi->id,
            'nama_kegiatan' => 'Pemupukan Pertama',
            'target_hst' => 14,
            'status' => 'Selesai',
            'tanggal_selesai' => $today->copy()->subDays(111),
            'catatan' => 'Urea dan SP36.',
        ]);

        CropActivity::create([
            'crop_id' => $padi->id,
            'nama_kegiatan' => 'Panen Raya',
            'target_hst' => 110,
            'status' => 'Selesai',
            'tanggal_selesai' => $today->copy()->subDays(15),
            'catatan' => 'Kadar air gabah 22%.',
        ]);
    }
}
