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
            $table->string('foto_nota')->nullable()->after('fase');
        });

        Schema::create('medicine_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            $table->string('toko_obat')->nullable();
            $table->unsignedBigInteger('harga')->nullable();
            $table->date('tanggal_beli')->nullable();
            $table->string('foto_nota')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Migrasi data pembelian yang sudah ada ke tabel medicine_purchases
        $now = now();
        $existing = DB::table('medicines')->get();
        foreach ($existing as $med) {
            if ($med->toko_obat || $med->harga !== null || $med->tanggal_beli) {
                DB::table('medicine_purchases')->insert([
                    'medicine_id' => $med->id,
                    'toko_obat' => $med->toko_obat,
                    'harga' => $med->harga,
                    'tanggal_beli' => $med->tanggal_beli,
                    'foto_nota' => null,
                    'catatan' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_purchases');

        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn('foto_nota');
        });
    }
};
