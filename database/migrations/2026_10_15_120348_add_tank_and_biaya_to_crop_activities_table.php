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
            $table->decimal('jumlah_tank', 6, 2)->nullable()->default(1.00)->after('aplikasi_obat');
            $table->decimal('total_biaya_obat', 14, 2)->nullable()->after('jumlah_tank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_activities', function (Blueprint $table) {
            $table->dropColumn(['jumlah_tank', 'total_biaya_obat']);
        });
    }
};
