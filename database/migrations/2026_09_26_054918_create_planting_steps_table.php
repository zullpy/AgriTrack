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
        Schema::create('planting_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planting_seed_id')
                ->constrained('planting_seeds')
                ->cascadeOnDelete()
                ->comment('Relasi ke bibit/komoditas tanaman');
            $table->string('nomor', 50)->default('1')->comment('Nomor langkah / tahapan');
            $table->integer('urutan')->default(1)->comment('Urutan sorting langkah');
            $table->string('judul')->comment('Judul tahapan penanaman bibit');
            $table->string('waktu')->nullable()->comment('Estimasi waktu pelaksanaan, misal: H-20 Sebelum Tanam');
            $table->text('deskripsi')->comment('Deskripsi penjelasan langkah penanaman');
            $table->text('tips')->nullable()->comment('Tips praktisi lapangan');
            $table->json('foto')->nullable()->comment('Daftar foto dokumentasi/panduan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planting_steps');
    }
};
