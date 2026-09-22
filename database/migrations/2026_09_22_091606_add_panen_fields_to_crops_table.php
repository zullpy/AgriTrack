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
        Schema::table('crops', function (Blueprint $table) {
            $table->string('total_panen')->nullable()->after('total_hst_panen');
            $table->string('harga_panen')->nullable()->after('total_panen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn(['total_panen', 'harga_panen']);
        });
    }
};
