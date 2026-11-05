<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\CropActivity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KalenderHssTest extends TestCase
{
    use RefreshDatabase;

    public function test_kalender_route_redirects_to_kalender_hst(): void
    {
        $response = $this->get('/kalender');
        $response->assertRedirect('/kalender-hst');
    }

    public function test_kalender_hss_and_hst_isolate_respective_crops(): void
    {
        $cropHst = Crop::create([
            'nama_tanaman' => 'Padi Inpari 32',
            'varietas' => 'Inpari',
            'tanggal_tanam' => Carbon::now()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
            'tipe' => 'hst',
        ]);

        $cropHss = Crop::create([
            'nama_tanaman' => 'Bibit Cabe Rawit Ori',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => Carbon::now()->subDays(5)->toDateString(),
            'status' => 'Sedang Disemai',
            'tipe' => 'hss',
        ]);

        // HSS index should only show HSS crop
        $responseHss = $this->get('/kalender-hss');
        $responseHss->assertStatus(200);
        $responseHss->assertSee('Bibit Cabe Rawit Ori');
        $responseHss->assertDontSee('Padi Inpari 32');
        $responseHss->assertSee('HSS');
        $responseHss->assertSee('Semai Aktif');

        // HST index should only show HST crop
        $responseHst = $this->get('/kalender-hst');
        $responseHst->assertStatus(200);
        $responseHst->assertSee('Padi Inpari 32');
        $responseHst->assertDontSee('Bibit Cabe Rawit Ori');
        $responseHst->assertSee('HST');
        $responseHst->assertSee('Tanaman Aktif');
    }

    public function test_can_create_new_hss_crop(): void
    {
        $response = $this->post('/kalender-hss/tanaman', [
            'nama_tanaman' => 'Semai Terong Yuvita',
            'varietas' => 'Yuvita F1',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'populasi' => '500 Tray',
            'tipe' => 'hss',
            'catatan' => 'Semai di baki semai rak A',
        ]);

        $response->assertRedirect('/kalender-hss?tab=aktif');
        $this->assertDatabaseHas('crops', [
            'nama_tanaman' => 'Semai Terong Yuvita',
            'varietas' => 'Yuvita F1',
            'tipe' => 'hss',
            'status' => 'Sedang Disemai',
        ]);
    }

    public function test_hss_crop_detail_shows_hss_labels_and_actions(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Bibit Tomat Servo',
            'varietas' => 'Servo F1',
            'populasi' => '1.000 Polybag',
            'tanggal_tanam' => Carbon::now()->subDays(6)->toDateString(),
            'status' => 'Sedang Disemai',
            'tipe' => 'hss',
        ]);

        CropActivity::create([
            'crop_id' => $crop->id,
            'target_hst' => 3,
            'nama_kegiatan' => 'Kocor Vitamin B1',
            'status' => 'Selesai',
        ]);

        $response = $this->get("/kalender-hss/tanaman/{$crop->id}");
        $response->assertStatus(200);

        // Check HSS-specific elements
        $response->assertSee('Menu HSS');
        $response->assertSee('Kembali ke Kalender HSS');
        $response->assertSee('Mulai Tanam (Ke HST)');
        $response->assertSee('Bibit Tomat Servo');
        $response->assertSee('Benih yang Disemai');
        $response->assertSee('Tanggal Semai');
        $response->assertSee('6HSS');
        $response->assertSee('Kocor Vitamin B1');
    }

    public function test_can_mulai_tanam_from_hss_to_hst_and_moves_to_riwayat_semai(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Semai Melon Golden',
            'varietas' => 'Alisha',
            'populasi' => '2.000 Benih',
            'tanggal_tanam' => Carbon::now()->subDays(14)->toDateString(),
            'status' => 'Sedang Disemai',
            'tipe' => 'hss',
        ]);

        // Verify active index page has 'Mulai Tanam' button
        $responseActive = $this->get('/kalender-hss?tab=aktif');
        $responseActive->assertStatus(200);
        $responseActive->assertSee('Mulai Tanam (Pindah ke HST)');

        $tanamDate = Carbon::today()->toDateString();

        $response = $this->post("/kalender-hss/tanaman/{$crop->id}/pindah-tanam", [
            'tanggal_tanam' => $tanamDate,
            'populasi' => '1.800 Pohon',
            'catatan' => 'Pindah ke lahan bedengan blok 2',
        ]);

        // Sown crop updated and moved to riwayat semai
        $crop->refresh();
        $this->assertEquals('Sudah Pindah Tanam', $crop->status);
        $this->assertEquals(14, $crop->total_hst_panen);

        // New HST crop created at 0 HST
        $newHstCrop = Crop::where('nama_tanaman', 'Semai Melon Golden')
            ->where('tipe', 'hst')
            ->first();

        $this->assertNotNull($newHstCrop);
        $this->assertEquals('Sedang Ditanam', $newHstCrop->status);
        $this->assertEquals('1.800 Pohon', $newHstCrop->populasi);
        $this->assertEquals($tanamDate, $newHstCrop->tanggal_tanam->toDateString());
        $this->assertEquals(0, $newHstCrop->current_hst);

        // Redirects to new HST crop show page
        $response->assertRedirect("/kalender-hst/tanaman/{$newHstCrop->id}");

        // Riwayat Semai tab in Kalender HSS shows the completed seedling
        $responseRiwayat = $this->get('/kalender-hss?tab=riwayat');
        $responseRiwayat->assertStatus(200);
        $responseRiwayat->assertSee('Semai Melon Golden');
        $responseRiwayat->assertSee('Sudah Pindah Tanam');
        $responseRiwayat->assertSee('HSS 14');
        $responseRiwayat->assertSee('Mulai Tanam (Lahan)');
    }

    public function test_hss_end_crop_redirects_to_kalender_hss(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Semai Cabai Gagal',
            'tanggal_tanam' => Carbon::now()->subDays(7)->toDateString(),
            'status' => 'Sedang Disemai',
            'tipe' => 'hss',
        ]);

        $response = $this->post("/kalender-hss/tanaman/{$crop->id}/akhiri", [
            'tanggal_akhir' => Carbon::today()->toDateString(),
            'alasan' => 'Gagal Panen (Cuaca / Bencana)',
            'catatan' => 'Banjir merendam persemaian',
        ]);

        $response->assertRedirect('/kalender-hss?tab=riwayat');
        $crop->refresh();
        $this->assertEquals('Diakhiri', $crop->status);
        $this->assertEquals(7, $crop->total_hst_panen);
    }

    public function test_hss_removes_panen_button_harvest_tabs_and_photo_documentation(): void
    {
        $cropHss = Crop::create([
            'nama_tanaman' => 'Semai Tomat Unggul',
            'tanggal_tanam' => Carbon::now()->subDays(5)->toDateString(),
            'status' => 'Sedang Disemai',
            'tipe' => 'hss',
        ]);

        $cropHst = Crop::create([
            'nama_tanaman' => 'Tanaman Tomat Lahan',
            'tanggal_tanam' => Carbon::now()->subDays(20)->toDateString(),
            'status' => 'Sedang Ditanam',
            'tipe' => 'hst',
        ]);

        // 1. In Kalender HSS Index:
        $responseHssIndex = $this->get('/kalender-hss');
        $responseHssIndex->assertStatus(200);
        $responseHssIndex->assertDontSee('tab-btn-panen');
        $responseHssIndex->assertDontSee('tab-content-panen');
        $responseHssIndex->assertDontSee('Panen ke-');
        $responseHssIndex->assertDontSee('Dokumentasi Foto');
        $responseHssIndex->assertDontSee('Catatan / Dosis Bahan');

        // 2. In Kalender HSS Show:
        $responseHssShow = $this->get("/kalender-hss/tanaman/{$cropHss->id}");
        $responseHssShow->assertStatus(200);
        $responseHssShow->assertDontSee('Panen ke-');
        $responseHssShow->assertDontSee('Dokumentasi Foto Kegiatan');
        $responseHssShow->assertDontSee('>FOTO<', false);

        // 3. In Kalender HST: ensure Panen remains, but Dokumentasi Foto and Catatan on active crops are removed
        $responseHstIndex = $this->get('/kalender-hst');
        $responseHstIndex->assertStatus(200);
        $responseHstIndex->assertSee('tab-btn-panen');
        $responseHstIndex->assertSee('Panen ke-');
        $responseHstIndex->assertDontSee('Dokumentasi Foto');
        $responseHstIndex->assertDontSee('Catatan / Dosis Bahan');

        $responseHstShow = $this->get("/kalender-hst/tanaman/{$cropHst->id}");
        $responseHstShow->assertStatus(200);
        $responseHstShow->assertSee('Panen ke-');
        $responseHstShow->assertDontSee('Dokumentasi Foto Kegiatan');
        $responseHstShow->assertDontSee('>FOTO<', false);
    }

    public function test_hss_table_shows_catat_action_in_kegiatan_column_and_allows_logging_activity(): void
    {
        $cropHss = Crop::create([
            'nama_tanaman' => 'Semai Cabai Rawit Merah',
            'varietas' => 'Maruti',
            'tanggal_tanam' => Carbon::now()->subDays(1)->toDateString(), // 1 HSS today
            'status' => 'Sedang Disemai',
            'tipe' => 'hss',
        ]);

        // GET show page
        $response = $this->get("/kalender-hss/tanaman/{$cropHss->id}");
        $response->assertStatus(200);

        // Verify Menyemai Bibit appears on Day 0
        $response->assertSee('Menyemai Bibit');

        // Verify the 'Catat' button is present in the Kegiatan column (using openAddActivityModal)
        $response->assertSee('openAddActivityModal(1,');
        $response->assertSee('openAddActivityModal(0,');
        $response->assertSee('<span>Catat</span>', false);

        // Test logging an activity to HSS crop
        $storeResponse = $this->post("/kalender-hss/tanaman/{$cropHss->id}/kegiatan", [
            'nama_kegiatan' => 'Penyiraman & B1',
            'target_hst' => 1,
            'status' => 'Selesai',
            'sasaran' => 'Akar bibit',
            'keterangan' => 'Disiram pagi hari',
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $cropHss->id,
            'nama_kegiatan' => 'Penyiraman & B1',
            'target_hst' => 1,
            'status' => 'Selesai',
        ]);

        // Verify the recorded activity is now rendered in the table
        $responseAfter = $this->get("/kalender-hss/tanaman/{$cropHss->id}");
        $responseAfter->assertStatus(200);
        $responseAfter->assertSee('Penyiraman & B1');
    }
}
