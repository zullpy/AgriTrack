<?php

use App\Models\Medicine;
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
        if (! Schema::hasColumn('medicines', 'stok_volume')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->decimal('stok_volume', 14, 2)->default(0)->after('stok');
            });
        }

        Schema::table('crop_activities', function (Blueprint $table) {
            if (! Schema::hasColumn('crop_activities', 'stok_dipotong')) {
                $table->boolean('stok_dipotong')->default(false)->after('status');
            }
            if (! Schema::hasColumn('crop_activities', 'stok_deductions')) {
                $table->json('stok_deductions')->nullable()->after('stok_dipotong');
            }
        });

        // Backfill stok_volume untuk semua obat yang sudah ada
        if (Schema::hasColumn('medicines', 'stok_volume')) {
            $medicines = Medicine::all();
            foreach ($medicines as $med) {
                $stok = (float) ($med->stok ?? 0);
                $baseTotal = (float) ($med->base_total_unit ?: 1.0);
                $med->stok_volume = $stok * $baseTotal;
                $med->saveQuietly();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_activities', function (Blueprint $table) {
            if (Schema::hasColumn('crop_activities', 'stok_deductions')) {
                $table->dropColumn('stok_deductions');
            }
            if (Schema::hasColumn('crop_activities', 'stok_dipotong')) {
                $table->dropColumn('stok_dipotong');
            }
        });

        Schema::table('medicines', function (Blueprint $table) {
            if (Schema::hasColumn('medicines', 'stok_volume')) {
                $table->dropColumn('stok_volume');
            }
        });
    }
};
