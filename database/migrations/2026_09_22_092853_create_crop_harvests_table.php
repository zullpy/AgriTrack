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
        Schema::create('crop_harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnDelete();
            $table->unsignedInteger('panen_ke')->default(1);
            $table->date('tanggal_panen');
            $table->unsignedInteger('hst_saat_panen')->nullable();
            $table->string('total_panen')->nullable();
            $table->string('harga_panen')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_harvests');
    }
};
