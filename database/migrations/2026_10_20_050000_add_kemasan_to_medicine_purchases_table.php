<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medicine_purchases', function (Blueprint $table) {
            $table->decimal('kemasan_jumlah', 10, 2)->nullable()->after('jumlah');
            $table->string('kemasan_satuan', 50)->nullable()->after('kemasan_jumlah');
        });

        // Backfill kemasan data from parent medicines
        $purchases = DB::table('medicine_purchases')->get();
        foreach ($purchases as $p) {
            $medicine = DB::table('medicines')->where('id', $p->medicine_id)->first();
            if ($medicine && ($medicine->kemasan_jumlah !== null || $medicine->kemasan_satuan !== null)) {
                DB::table('medicine_purchases')->where('id', $p->id)->update([
                    'kemasan_jumlah' => $medicine->kemasan_jumlah,
                    'kemasan_satuan' => $medicine->kemasan_satuan,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_purchases', function (Blueprint $table) {
            $table->dropColumn(['kemasan_jumlah', 'kemasan_satuan']);
        });
    }
};
