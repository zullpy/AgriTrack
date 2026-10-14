<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('medicine_categories')) {
            Schema::create('medicine_categories', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 100)->unique();
                $table->string('warna', 50)->default('purple');
                $table->string('deskripsi', 255)->nullable();
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });

            $now = now();
            $defaults = [
                ['nama' => 'Fungisida', 'warna' => 'purple', 'deskripsi' => 'Pencegah & pengendali jamur / fungi', 'urutan' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['nama' => 'Insektisida', 'warna' => 'rose', 'deskripsi' => 'Pengendali hama serangga & ulat', 'urutan' => 2, 'created_at' => $now, 'updated_at' => $now],
                ['nama' => 'Pupuk', 'warna' => 'emerald', 'deskripsi' => 'Nutrisi makro & mikro tanaman', 'urutan' => 3, 'created_at' => $now, 'updated_at' => $now],
                ['nama' => 'Vitamin', 'warna' => 'teal', 'deskripsi' => 'Suplemen tanaman & anti-stres', 'urutan' => 4, 'created_at' => $now, 'updated_at' => $now],
                ['nama' => 'Bibit', 'warna' => 'lime', 'deskripsi' => 'Benih & bibit tanaman', 'urutan' => 5, 'created_at' => $now, 'updated_at' => $now],
                ['nama' => 'Perlengkapan', 'warna' => 'amber', 'deskripsi' => 'Mulsa, ajir, tali, selang, perekat dll', 'urutan' => 6, 'created_at' => $now, 'updated_at' => $now],
                ['nama' => 'Peralatan', 'warna' => 'blue', 'deskripsi' => 'Tangki semprot, gunting, koret, cangkul dll', 'urutan' => 7, 'created_at' => $now, 'updated_at' => $now],
            ];

            DB::table('medicine_categories')->insert($defaults);
        }

        // Masukkan jenis obat yang mungkin sudah ada di database medicines namun belum ada di defaults
        if (Schema::hasTable('medicines')) {
            $existingJenis = DB::table('medicines')
                ->whereNotNull('jenis')
                ->where('jenis', '!=', '')
                ->distinct()
                ->pluck('jenis');

            $order = 10;
            foreach ($existingJenis as $jenis) {
                if (! DB::table('medicine_categories')->where('nama', $jenis)->exists()) {
                    DB::table('medicine_categories')->insert([
                        'nama' => $jenis,
                        'warna' => 'indigo',
                        'deskripsi' => null,
                        'urutan' => $order++,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_categories');
    }
};
