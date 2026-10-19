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
            $table->unsignedInteger('stok')->default(0)->after('harga');
        });

        Schema::table('medicine_purchases', function (Blueprint $table) {
            $table->unsignedInteger('jumlah')->nullable()->default(1)->after('harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_purchases', function (Blueprint $table) {
            $table->dropColumn('jumlah');
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn('stok');
        });
    }
};
