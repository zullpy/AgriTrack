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
        Schema::table('plant_catalogs', function (Blueprint $table) {
            $table->string('varietas', 150)->nullable()->after('name');
            $table->text('deskripsi')->nullable()->after('cycle');
            $table->longText('tahap_semai')->nullable()->after('keywords');
            $table->longText('tahap_penanaman')->nullable()->after('tahap_semai');
            $table->longText('tahap_pemeliharaan')->nullable()->after('tahap_penanaman');
            $table->longText('tips_penting')->nullable()->after('tahap_pemeliharaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plant_catalogs', function (Blueprint $table) {
            $table->dropColumn([
                'varietas',
                'deskripsi',
                'tahap_semai',
                'tahap_penanaman',
                'tahap_pemeliharaan',
                'tips_penting',
            ]);
        });
    }
};
