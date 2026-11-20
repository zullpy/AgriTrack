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
        Schema::create('plant_catalog_hamas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_catalog_id')->constrained('plant_catalogs')->cascadeOnDelete();
            $table->string('judul');
            $table->string('kategori')->default('Hama & Penyakit');
            $table->longText('gejala')->nullable();
            $table->longText('solusi')->nullable();
            $table->json('foto')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index(['plant_catalog_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_catalog_hamas');
    }
};
