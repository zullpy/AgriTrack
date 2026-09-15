<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plant_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique()->comment('Slug unik, e.g. timun, cabe, jagung');
            $table->string('name', 100)->comment('Nama tampilan, e.g. Timun');
            $table->string('emoji', 20)->default('🌱')->comment('Emoji karakter tanaman');
            $table->string('cycle', 80)->comment('Siklus, e.g. 35 – 45 HST');
            $table->string('theme', 30)->default('emerald')->comment('Warna tema Tailwind: emerald, rose, amber, sky, yellow, lime');
            $table->string('keywords', 255)->nullable()->comment('Kata kunci match ke crops.nama_tanaman, comma-separated');
            $table->unsignedTinyInteger('urutan')->default(99)->comment('Urutan tampil di dashboard');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plant_catalogs');
    }
};
