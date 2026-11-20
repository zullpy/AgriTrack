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
        Schema::table('plant_catalogs', function (Blueprint $table) {
            $table->string('cycle', 80)->default('—')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plant_catalogs', function (Blueprint $table) {
            $table->string('cycle', 80)->nullable(false)->change();
        });
    }
};
