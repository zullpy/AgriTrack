<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\CropActivity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KalenderHstTest extends TestCase
{
    use RefreshDatabase;

    public function test_kalender_hst_page_loads_successfully(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Padi Ciherang',
            'varietas' => 'Inpari 32',
            'tanggal_tanam' => Carbon::now()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        CropActivity::create([
            'crop_id' => $crop->id,
            'target_hst' => 14,
            'nama_kegiatan' => 'Pemupukan Susulan 1',
            'status' => 'Belum',
        ]);

        $response = $this->get('/kalender-hst');
        $response->assertStatus(200);
        $response->assertSee('Padi Ciherang');
        $response->assertSee('Pemupukan Susulan 1');
    }

    public function test_can_create_new_crop(): void
    {
        $response = $this->post('/kalender-hst/tanaman', [
            'nama_tanaman' => 'Jagung Manis',
            'varietas' => 'Bonanza F1',
            'tanggal_tanam' => Carbon::now()->subDays(5)->toDateString(),
            'catatan' => 'Bibit unggul',
        ]);

        $response->assertRedirect('/kalender-hst?tab=aktif');
        $this->assertDatabaseHas('crops', [
            'nama_tanaman' => 'Jagung Manis',
            'status' => 'Sedang Ditanam',
        ]);
    }

    public function test_can_create_crop_activity(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'target_hst' => 7,
            'nama_kegiatan' => 'Semprot Fungisida',
            'catatan' => 'Dosis 2ml/L',
        ]);

        $response->assertRedirect('/kalender-hst?tab=aktif');
        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop->id,
            'target_hst' => 7,
            'nama_kegiatan' => 'Semprot Fungisida',
            'status' => 'Belum',
        ]);
    }

    public function test_can_toggle_activity_status(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Bawang Merah',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $activity = CropActivity::create([
            'crop_id' => $crop->id,
            'target_hst' => 5,
            'nama_kegiatan' => 'Siram Pagi',
            'status' => 'Belum',
        ]);

        $response = $this->postJson("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $response->assertJson([
            'success' => true,
            'status' => 'Selesai',
        ]);

        $this->assertEquals('Selesai', $activity->fresh()->status);
        $this->assertNotNull($activity->fresh()->tanggal_selesai);

        // Toggle back to Belum
        $response2 = $this->postJson("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $response2->assertJson([
            'success' => true,
            'status' => 'Belum',
        ]);
        $this->assertEquals('Belum', $activity->fresh()->status);
        $this->assertNull($activity->fresh()->tanggal_selesai);
    }

    public function test_can_mark_crop_as_harvested(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Padi IR64',
            'tanggal_tanam' => Carbon::now()->subDays(105)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $harvestDate = Carbon::now()->toDateString();
        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/panen", [
            'tanggal_panen' => $harvestDate,
            'catatan' => 'Hasil sangat memuaskan',
        ]);

        $response->assertRedirect('/kalender-hst?tab=riwayat');

        $crop->refresh();
        $this->assertEquals('Sudah Dipanen', $crop->status);
        $this->assertEquals($harvestDate, $crop->tanggal_panen->toDateString());
        $this->assertEquals(105, $crop->total_hst_panen);
    }

    public function test_can_delete_crop(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Jagung Hibrida',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->delete("/kalender-hst/tanaman/{$crop->id}");
        $response->assertRedirect('/kalender-hst');
        $this->assertDatabaseMissing('crops', ['id' => $crop->id]);
    }

    public function test_can_view_crop_hst_menu(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Merah',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => Carbon::now()->subDays(12)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        CropActivity::create([
            'crop_id' => $crop->id,
            'target_hst' => 12,
            'nama_kegiatan' => 'Penyiraman & Penyiangan Gulma',
            'status' => 'Belum',
        ]);

        $response = $this->get("/kalender-hst/tanaman/{$crop->id}");
        $response->assertStatus(200);
        $response->assertSee('Cabai Rawit Merah');
        $response->assertSee('HST 12');
        $response->assertSee('Penyiraman &amp; Penyiangan Gulma', false);
        $response->assertSee('Log Kegiatan Per HST');
    }

    public function test_can_create_activity_with_automatic_date_sync(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Padi Mekongga',
            'tanggal_tanam' => Carbon::parse('2026-09-01')->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // Input tanggal_kegiatan tanpa target_hst -> sistem menghitung target_hst otomatis (2026-09-15 - 2026-09-01 = 14)
        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Pemupukan Susulan Urea',
            'tanggal_kegiatan' => '2026-09-15',
            'catatan' => 'Dosis 50kg/ha',
        ]);

        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Pemupukan Susulan Urea',
            'target_hst' => 14,
        ]);
    }

    public function test_can_update_activity(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Tomat Servvo',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $activity = CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Kegiatan Lama',
            'target_hst' => 5,
            'status' => 'Belum',
        ]);

        $response = $this->put("/kalender-hst/kegiatan/{$activity->id}", [
            'nama_kegiatan' => 'Kegiatan Diperbarui',
            'target_hst' => 10,
            'catatan' => 'Catatan baru',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('crop_activities', [
            'id' => $activity->id,
            'nama_kegiatan' => 'Kegiatan Diperbarui',
            'target_hst' => 10,
            'catatan' => 'Catatan baru',
        ]);
    }

    public function test_harvested_crop_shows_log_button_in_riwayat_panen(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Padi Pandan Wangi',
            'varietas' => 'Lokal',
            'tanggal_tanam' => Carbon::now()->subDays(100)->toDateString(),
            'status' => 'Sudah Dipanen',
            'tanggal_panen' => Carbon::now()->subDays(10)->toDateString(),
            'total_hst_panen' => 90,
            'catatan' => 'Hasil 6 ton/ha',
        ]);

        CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Pemupukan Dasar',
            'target_hst' => 0,
            'status' => 'Selesai',
        ]);

        // Verifikasi di tab riwayat pada halaman index terdapat tombol menu HST
        $response = $this->get('/kalender-hst?tab=riwayat');
        $response->assertStatus(200);
        $response->assertSee('Padi Pandan Wangi');
        $response->assertSee('HST 90');
        $response->assertSee('Buka Menu HST Tanaman (Log Harian)');
        $response->assertSee("/kalender-hst/tanaman/{$crop->id}");

        // Verifikasi bahwa halaman show tanaman panen menampilkan riwayat milestone panen
        $showResponse = $this->get("/kalender-hst/tanaman/{$crop->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Padi Pandan Wangi');
        $showResponse->assertSee('Sudah Dipanen');
        $showResponse->assertSee('Pemupukan Dasar');
        $showResponse->assertSee('HST 90');
    }

    public function test_monthly_calendar_only_shows_hst_up_to_tomorrow(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Padi Rojolele',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->get('/kalender-hst?tab=kalender');
        $response->assertStatus(200);

        // Hari ini adalah HST 0, besok adalah HST 1
        $response->assertSee('HST 0');
        $response->assertSee('HST 1');
        // Tidak boleh menghasilkan HST 10 atau HST 20 di tanggal mendatang
        $response->assertDontSee('HST 10');
        $response->assertDontSee('HST 20');
        $response->assertSee('Besok');
    }

    public function test_crop_table_layout_shows_benih_populasi_and_auto_hari_menanam_on_0hst(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Merah',
            'varietas' => 'Kaliber',
            'populasi' => '2.500 Pohon',
            'tanggal_tanam' => Carbon::parse('2026-09-09')->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Nyemprot',
            'aplikasi_obat' => 'Fungisida : Dithane 2 sendok (16 liter), Insektisida : Curacron 15 ml (16 liter), Vitamin : Gandasil D 2 sendok (16 liter)',
            'sasaran' => 'Ulat Grayak & Patek',
            'target_hst' => 1,
            'status' => 'Belum',
            'keterangan' => 'Semprot kabut merata',
        ]);

        $response = $this->get("/kalender-hst/tanaman/{$crop->id}");
        $response->assertStatus(200);

        // Header check
        $response->assertSee('Benih yang Ditanam');
        $response->assertSee('Kaliber');
        $response->assertSee('Populasi');
        $response->assertSee('2.500 Pohon');
        $response->assertSee('Tanggal Menanam');

        // Table headers check
        $response->assertSee('KALENDER');
        $response->assertSee('KEGIATAN');
        $response->assertSee('APLIKASI OBAT');
        $response->assertSee('SASARAN');
        $response->assertSee('KETERANGAN');

        // 0HST check: automatic 'Hari Menanam'
        $response->assertSee('0HST');
        $response->assertSee('Hari Menanam');

        // 1HST row check: Nyemprot, obat, sasaran, keterangan
        $response->assertSee('1HST');
        $response->assertSee('Nyemprot');
        $response->assertSee('Dithane 2 sendok');
        $response->assertSee('Curacron 15 ml');
        $response->assertSee('Gandasil D 2 sendok');
        $response->assertSee('Ulat Grayak &amp; Patek', false);
        $response->assertSee('Semprot kabut merata');
    }

    public function test_can_create_activity_with_aplikasi_obat_sasaran_and_keterangan(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Bawang Merah',
            'varietas' => 'Tajuk',
            'populasi' => '1.000 kg bibit',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Nyemprot Pagi',
            'aplikasi_obat' => 'Insektisida : Curacron 500 EC 15 ml (16 liter)',
            'sasaran' => 'Ulat Grayak',
            'keterangan' => 'Aplikasi pagi pukul 06:30',
            'target_hst' => 3,
        ]);

        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Nyemprot Pagi',
            'aplikasi_obat' => 'Insektisida : Curacron 500 EC 15 ml (16 liter)',
            'sasaran' => 'Ulat Grayak',
            'keterangan' => 'Aplikasi pagi pukul 06:30',
            'target_hst' => 3,
        ]);
    }
}
