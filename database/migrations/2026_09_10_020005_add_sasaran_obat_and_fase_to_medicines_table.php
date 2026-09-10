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
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('sasaran_obat')->nullable()->after('cara_kerja');
            $table->string('fase')->nullable()->after('unsur_bahan'); // Vegetatif, Generatif, Semua Fase
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['sasaran_obat', 'fase']);
        });
    }
};
