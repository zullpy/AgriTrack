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
        Schema::table('crop_activities', function (Blueprint $table) {
            if (! Schema::hasColumn('crop_activities', 'metode_aplikasi')) {
                $table->string('metode_aplikasi', 20)->default('Semprot')->after('nama_kegiatan');
            }
            if (! Schema::hasColumn('crop_activities', 'populasi_target')) {
                $table->decimal('populasi_target', 12, 2)->nullable()->after('jumlah_tank');
            }
            if (! Schema::hasColumn('crop_activities', 'dosis_per_tanaman')) {
                $table->decimal('dosis_per_tanaman', 10, 2)->nullable()->after('populasi_target');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_activities', function (Blueprint $table) {
            $table->dropColumn(['metode_aplikasi', 'populasi_target', 'dosis_per_tanaman']);
        });
    }
};
