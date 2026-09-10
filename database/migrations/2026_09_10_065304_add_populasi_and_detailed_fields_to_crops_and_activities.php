
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
            $table->string('populasi')->nullable()->after('varietas');
        });

        Schema::table('crop_activities', function (Blueprint $table) {
            $table->text('aplikasi_obat')->nullable()->after('nama_kegiatan');
            $table->string('sasaran')->nullable()->after('aplikasi_obat');
            $table->text('keterangan')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_activities', function (Blueprint $table) {
            $table->dropColumn(['aplikasi_obat', 'sasaran', 'keterangan']);
        });

        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn('populasi');
        });
    }
};
