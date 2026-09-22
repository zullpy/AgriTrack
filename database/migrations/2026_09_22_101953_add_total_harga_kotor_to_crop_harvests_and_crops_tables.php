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
        Schema::table('crop_harvests', function (Blueprint $table) {
            $table->string('total_harga_kotor')->nullable()->after('harga_panen');
        });

        Schema::table('crops', function (Blueprint $table) {
            $table->string('total_harga_kotor')->nullable()->after('harga_panen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_harvests', function (Blueprint $table) {
            $table->dropColumn('total_harga_kotor');
        });

        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn('total_harga_kotor');
        });
    }
};
