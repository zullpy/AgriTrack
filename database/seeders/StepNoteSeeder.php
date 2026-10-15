<?php

namespace Database\Seeders;

use App\Models\StepNote;
use Illuminate\Database\Seeder;

class StepNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notes = [
            [
                'judul' => 'Waktu Terbaik Penyemprotan Pestisida & Fungisida',
                'catatan' => "Lakukan penyemprotan pada pagi hari (pukul 06.00 – 09.00) atau sore hari (pukul 15.30 – 17.30) saat stomata daun terbuka dan terik matahari tidak terlalu panas.\n\nHindari menyemprot saat angin kencang agar kabut tidak terbuang (drift), dan jangan menyemprot saat cuaca mendung gelap menjelang hujan.",
            ],
            [
                'judul' => 'Kualitas Air Pelarut Tangki Semprot',
                'catatan' => "Gunakan air bersih, jernih, bebas lumpur, dan tidak berbau kaporit/klorin tinggi.\n\nAir keruh yang mengandung partikel liat (clay) dapat mengikat bahan aktif herbisida/insektisida tertentu sehingga menurunkan efektivitas obat secara drastis.",
            ],
            [
                'judul' => 'Sanitasi & Pencucian Tangki Setelah Pemakaian',
                'catatan' => "Bilas tangki semprot minimal 2–3 kali dengan air bersih setelah selesai penyemprotan.\n\nHal ini sangat krusial terutama setelah menggunakan herbisida agar sisa racun di dalam selang atau nozzle tidak meracuni tanaman pada jadwal penyemprotan nutrisi/fungisida berikutnya.",
            ],
        ];

        foreach ($notes as $note) {
            StepNote::updateOrCreate(
                ['judul' => $note['judul']],
                $note
            );
        }
    }
}
