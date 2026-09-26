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
        Schema::create('land_preparation_steps', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->default('1')->comment('Nomor langkah / tahapan');
            $table->integer('urutan')->default(1)->comment('Urutan sorting langkah');
            $table->string('judul')->comment('Judul tahapan');
            $table->string('waktu')->nullable()->comment('Estimasi waktu pelaksanaan');
            $table->text('deskripsi')->comment('Deskripsi penjelasan langkah');
            $table->text('tips')->nullable()->comment('Tips praktisi');
            $table->json('spesifikasi')->nullable()->comment('Spesifikasi teknis (key-value array)');
            $table->json('foto')->nullable()->comment('Daftar foto dokumentasi/panduan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_preparation_steps');
    }
};
