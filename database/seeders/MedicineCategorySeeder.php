<?php

namespace Database\Seeders;

use App\Models\MedicineCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MedicineCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            ['nama' => 'Fungisida', 'warna' => 'purple', 'deskripsi' => 'Pencegah & pengendali jamur / fungi', 'urutan' => 1],
            ['nama' => 'Insektisida', 'warna' => 'rose', 'deskripsi' => 'Pengendali hama serangga & ulat', 'urutan' => 2],
            ['nama' => 'Pupuk', 'warna' => 'emerald', 'deskripsi' => 'Nutrisi makro & mikro tanaman', 'urutan' => 3],
            ['nama' => 'Vitamin', 'warna' => 'teal', 'deskripsi' => 'Suplemen tanaman & anti-stres', 'urutan' => 4],
            ['nama' => 'Bibit', 'warna' => 'lime', 'deskripsi' => 'Benih & bibit tanaman', 'urutan' => 5],
            ['nama' => 'Perlengkapan', 'warna' => 'amber', 'deskripsi' => 'Mulsa, ajir, tali, selang, perekat dll', 'urutan' => 6],
            ['nama' => 'Peralatan', 'warna' => 'blue', 'deskripsi' => 'Tangki semprot, gunting, koret, cangkul dll', 'urutan' => 7],
        ];

        foreach ($defaults as $cat) {
            MedicineCategory::firstOrCreate(
                ['nama' => $cat['nama']],
                [
                    'warna' => $cat['warna'],
                    'deskripsi' => $cat['deskripsi'],
                    'urutan' => $cat['urutan'],
                ]
            );
        }

        if (Schema::hasTable('medicines')) {
            $existingJenis = DB::table('medicines')
                ->whereNotNull('jenis')
                ->where('jenis', '!=', '')
                ->distinct()
                ->pluck('jenis');

            $order = 10;
            foreach ($existingJenis as $jenis) {
                MedicineCategory::firstOrCreate(
                    ['nama' => $jenis],
                    [
                        'warna' => MedicineCategory::getNextAvailableColor(),
                        'deskripsi' => null,
                        'urutan' => $order++,
                    ]
                );
            }
        }
    }
}
