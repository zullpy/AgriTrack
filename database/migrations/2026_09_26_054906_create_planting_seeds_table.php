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
        Schema::create('planting_seeds', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bibit')->comment('Nama komoditas / bibit tanaman, misal: Wortel, Cabai Rawit');
            $table->string('varietas')->nullable()->comment('Varietas benih, misal: Kuroda, Ori 212');
            $table->text('deskripsi')->nullable()->comment('Catatan atau deskripsi umum pembibitan');
            $table->integer('urutan')->default(1)->comment('Urutan tampilan kartu bibit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planting_seeds');
    }
};
