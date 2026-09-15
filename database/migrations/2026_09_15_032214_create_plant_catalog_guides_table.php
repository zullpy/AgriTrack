<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plant_catalog_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_catalog_id')->constrained()->cascadeOnDelete();
            $table->string('phase', 100)->comment('Nama fase, e.g. Pemupukan Dasar');
            $table->string('hst', 80)->comment('Rentang HST, e.g. 0 HST (Sebelum Tanam)');
            $table->string('focus', 255)->comment('Fokus nutrisi fase ini');
            $table->text('nutrients')->comment('Daftar pupuk JSON array, e.g. ["NPK 16-16-16","Urea"]');
            $table->text('dosis')->comment('Dosis aplikasi');
            $table->text('metode')->comment('Cara/metode aplikasi');
            $table->text('tips')->nullable()->comment('Tips praktis lapangan');
            $table->unsignedTinyInteger('urutan')->default(99);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plant_catalog_guides');
    }
};
