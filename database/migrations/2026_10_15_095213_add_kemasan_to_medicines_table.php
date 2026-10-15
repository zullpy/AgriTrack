<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->decimal('kemasan_jumlah', 10, 2)->nullable()->after('harga');
            $table->string('kemasan_satuan', 50)->nullable()->after('kemasan_jumlah');
        });

        // Backfill data ukuran & satuan kemasan untuk data obat yang sudah ada berdasarkan nama
        $medicines = DB::table('medicines')->get();
        foreach ($medicines as $m) {
            $nama = $m->nama;
            $jumlah = null;
            $satuan = null;

            if (preg_match('/(\d+(?:[.,]\d+)?)\s*(kg|kilogram|gram|gr|g|liter|ltr|lt|l|ml|sachet|bungkus)\b/i', $nama, $matches)) {
                $jumlah = (float) str_replace(',', '.', $matches[1]);
                $rawSat = strtolower($matches[2]);
                if (in_array($rawSat, ['kg', 'kilogram'], true)) {
                    $satuan = 'kg';
                } elseif (in_array($rawSat, ['liter', 'ltr', 'lt', 'l'], true)) {
                    $satuan = 'liter';
                } elseif (in_array($rawSat, ['gram', 'gr', 'g'], true)) {
                    $satuan = 'gram';
                } elseif ($rawSat === 'ml') {
                    $satuan = 'ml';
                } elseif (in_array($rawSat, ['sachet', 'bungkus'], true)) {
                    $satuan = 'sachet';
                }
            } elseif (stripos($nama, 'sachet') !== false) {
                $jumlah = 1.0;
                $satuan = 'sachet';
            } elseif (stripos($nama, 'karung') !== false) {
                $jumlah = 1.0;
                $satuan = 'pcs';
            } elseif (in_array($m->jenis, ['Pupuk'], true)) {
                // Default pupuk eceran standar di kios 1 kg jika tidak tertera
                $jumlah = 1.0;
                $satuan = 'kg';
            }

            if ($jumlah !== null && $satuan !== null) {
                DB::table('medicines')->where('id', $m->id)->update([
                    'kemasan_jumlah' => $jumlah,
                    'kemasan_satuan' => $satuan,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['kemasan_jumlah', 'kemasan_satuan']);
        });
    }
};
