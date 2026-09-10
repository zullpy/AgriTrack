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
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tanaman');
            $table->string('varietas')->nullable();
            $table->date('tanggal_tanam');
            $table->string('status')->default('Sedang Ditanam');
            $table->date('tanggal_panen')->nullable();
            $table->unsignedInteger('total_hst_panen')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('crop_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnDelete();
            $table->string('nama_kegiatan');
            $table->unsignedInteger('target_hst');
            $table->string('status')->default('Belum');
            $table->date('tanggal_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_activities');
        Schema::dropIfExists('crops');
    }
};
