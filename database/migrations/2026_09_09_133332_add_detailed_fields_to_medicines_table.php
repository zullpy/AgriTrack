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
            $table->string('cara_kerja')->nullable()->after('jenis'); // Sistemik, Kontak, Sistemik + Kontak
            $table->unsignedBigInteger('harga')->nullable()->after('dosis_anjuran');
            $table->string('unsur_bahan')->nullable()->after('harga');
            $table->text('keterangan')->nullable()->after('catatan_keamanan');
            $table->date('tanggal_beli')->nullable()->after('keterangan');
            $table->string('toko_obat')->nullable()->after('tanggal_beli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn([
                'cara_kerja',
                'harga',
                'unsur_bahan',
                'keterangan',
                'tanggal_beli',
                'toko_obat',
            ]);
        });
    }
};
