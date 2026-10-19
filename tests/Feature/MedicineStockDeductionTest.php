<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicineStockDeductionTest extends TestCase
{
    use RefreshDatabase;

    public function test_marking_activity_done_deducts_liquid_medicine_per_ml_multiplied_by_tank(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Decis 25 EC',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'harga' => 50000,
            'stok' => 2,
            'stok_volume' => 200.0,
            'kemasan_jumlah' => 100,
            'kemasan_satuan' => 'ml',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Merah',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => now()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $activity = $crop->activities()->create([
            'nama_kegiatan' => 'Nyemprot Ulat',
            'aplikasi_obat' => 'Decis 25 EC: 15 ml (Rp 11.400)',
            'jumlah_tank' => 2,
            'target_hst' => 10,
            'status' => 'Belum',
        ]);

        $response = $this->post("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $response->assertRedirect();

        $activity->refresh();
        $medicine->refresh();

        $this->assertEquals('Selesai', $activity->status);
        $this->assertTrue((bool) $activity->stok_dipotong);

        // 200 ml - (15 ml * 2 tank) = 170 ml
        $this->assertEquals(170.0, (float) $medicine->stok_volume);
        $this->assertEquals(2, $medicine->stok);
        $this->assertEquals('170 ml', $medicine->stok_volume_label);
    }

    public function test_marking_activity_done_deducts_powder_medicine_per_gram_multiplied_by_tank(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 70000,
            'stok' => 1,
            'stok_volume' => 500.0,
            'kemasan_jumlah' => 500,
            'kemasan_satuan' => 'gram',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Tomat Servo',
            'varietas' => 'F1',
            'tanggal_tanam' => now()->subDays(5)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $activity = $crop->activities()->create([
            'nama_kegiatan' => 'Pencegahan Jamur',
            'aplikasi_obat' => 'Antracol 70 WP: 20 gram',
            'jumlah_tank' => 3,
            'target_hst' => 5,
            'status' => 'Belum',
        ]);

        $response = $this->postJson("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'Selesai',
                'stok_dipotong' => true,
            ]);

        $activity->refresh();
        $medicine->refresh();

        // 500 gram - (20 gram * 3 tank) = 440 gram
        $this->assertEquals(440.0, (float) $medicine->stok_volume);
        $this->assertEquals(1, $medicine->stok);
        $this->assertEquals('440 gram', $medicine->stok_volume_label);
    }

    public function test_marking_activity_done_deducts_equipment_per_pcs_without_tank_multiplier(): void
    {
        $equipment = Medicine::create([
            'nama' => 'Karung Waring 50x80',
            'jenis' => 'Perlengkapan',
            'tanaman_sasaran' => 'Semua Tanaman',
            'harga' => 2000,
            'stok' => 50,
            'stok_volume' => 50.0,
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'pcs',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Bawang Merah',
            'varietas' => 'Tajuk',
            'tanggal_tanam' => now()->subDays(20)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $activity = $crop->activities()->create([
            'nama_kegiatan' => 'Pasang Waring Pelindung',
            'aplikasi_obat' => 'Karung Waring 50x80: 8 pcs',
            'jumlah_tank' => 4, // Tank should NOT multiply equipment pieces!
            'target_hst' => 20,
            'status' => 'Belum',
        ]);

        $response = $this->post("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $response->assertRedirect();

        $equipment->refresh();

        // 50 pcs - 8 pcs = 42 pcs (NOT 50 - 32)
        $this->assertEquals(42.0, (float) $equipment->stok_volume);
        $this->assertEquals(42, $equipment->stok);
        $this->assertEquals('42 pcs', $equipment->stok_volume_label);
    }

    public function test_toggling_activity_back_to_belum_restores_deducted_stock(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Curacron 500 EC',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 85000,
            'stok' => 1,
            'stok_volume' => 100.0,
            'kemasan_jumlah' => 100,
            'kemasan_satuan' => 'ml',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit',
            'varietas' => 'Kaliber',
            'tanggal_tanam' => now()->subDays(15)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $activity = $crop->activities()->create([
            'nama_kegiatan' => 'Semprot Kutu Kebul',
            'aplikasi_obat' => 'Curacron 500 EC: 10 ml',
            'jumlah_tank' => 2,
            'target_hst' => 15,
            'status' => 'Belum',
        ]);

        // Toggle to Selesai (deducts 20 ml)
        $this->post("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $medicine->refresh();
        $this->assertEquals(80.0, (float) $medicine->stok_volume);

        // Toggle back to Belum (restores 20 ml)
        $this->post("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $activity->refresh();
        $medicine->refresh();

        $this->assertEquals('Belum', $activity->status);
        $this->assertFalse((bool) $activity->stok_dipotong);
        $this->assertEquals(100.0, (float) $medicine->stok_volume);
    }

    public function test_deleting_activity_restores_deducted_stock(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Dithane M-45',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 60000,
            'stok' => 1,
            'stok_volume' => 1000.0,
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'kg',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Kentang',
            'varietas' => 'Granola',
            'tanggal_tanam' => now()->subDays(30)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $activity = $crop->activities()->create([
            'nama_kegiatan' => 'Semprot Busuk Daun',
            'aplikasi_obat' => 'Dithane M-45: 25 gram',
            'jumlah_tank' => 2,
            'target_hst' => 30,
            'status' => 'Belum',
        ]);

        // Selesai -> potong 50 gram
        $this->post("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $medicine->refresh();
        $this->assertEquals(950.0, (float) $medicine->stok_volume);

        // Hapus kegiatan -> kembalikan 50 gram
        $this->delete("/kalender-hst/kegiatan/{$activity->id}");
        $medicine->refresh();

        $this->assertDatabaseMissing('crop_activities', ['id' => $activity->id]);
        $this->assertEquals(1000.0, (float) $medicine->stok_volume);
    }

    public function test_updating_finished_activity_recalculates_stock_deductions(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Alika 247 ZC',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 45000,
            'stok' => 2,
            'stok_volume' => 200.0,
            'kemasan_jumlah' => 100,
            'kemasan_satuan' => 'ml',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai',
            'varietas' => 'Genki',
            'tanggal_tanam' => now()->subDays(7)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $activity = $crop->activities()->create([
            'nama_kegiatan' => 'Semprot Thrips',
            'aplikasi_obat' => 'Alika 247 ZC: 15 ml',
            'jumlah_tank' => 2,
            'target_hst' => 7,
            'status' => 'Belum',
        ]);

        // Selesai -> potong 30 ml
        $this->post("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $medicine->refresh();
        $this->assertEquals(170.0, (float) $medicine->stok_volume);

        // Edit kegiatan: ubah dosis jadi 10 ml
        $this->put("/kalender-hst/kegiatan/{$activity->id}", [
            'nama_kegiatan' => 'Semprot Thrips (Dosis Ringan)',
            'aplikasi_obat' => 'Alika 247 ZC: 10 ml',
            'jumlah_tank' => 2,
            'target_hst' => 7,
        ]);

        $medicine->refresh();
        // 200 ml - (10 ml * 2 tank) = 180 ml
        $this->assertEquals(180.0, (float) $medicine->stok_volume);
    }

    public function test_creating_activity_with_status_selesai_immediately_deducts_stock(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Score 250 EC',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi',
            'harga' => 75000,
            'stok' => 1,
            'stok_volume' => 250.0,
            'kemasan_jumlah' => 250,
            'kemasan_satuan' => 'ml',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Padi Ciherang',
            'varietas' => 'Ciherang Super',
            'tanggal_tanam' => now()->subDays(25)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Semprot Jamur Langsung Selesai',
            'aplikasi_obat' => 'Score 250 EC: 20 ml',
            'jumlah_tank' => 2,
            'target_hst' => 25,
            'status' => 'Selesai',
        ]);

        $response->assertRedirect();

        $medicine->refresh();
        // 250 ml - (20 ml * 2 tank) = 210 ml
        $this->assertEquals(210.0, (float) $medicine->stok_volume);
    }

    public function test_creating_activity_with_medicine_defaults_to_selesai_and_deducts_stock(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Score 250 EC Default',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi',
            'harga' => 75000,
            'stok' => 1,
            'stok_volume' => 250.0,
            'kemasan_jumlah' => 250,
            'kemasan_satuan' => 'ml',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Padi Ciherang 2',
            'varietas' => 'Ciherang Super',
            'tanggal_tanam' => now()->subDays(25)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // Kirim tanpa status
        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Semprot Jamur Auto Selesai',
            'aplikasi_obat' => 'Score 250 EC Default: 20 ml',
            'jumlah_tank' => 1,
            'target_hst' => 25,
        ]);

        $response->assertRedirect();

        $medicine->refresh();
        $this->assertEquals(230.0, (float) $medicine->stok_volume);
    }

    public function test_creating_activity_with_explicit_status_belum_does_not_deduct_stock(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Score 250 EC Rencana',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi',
            'harga' => 75000,
            'stok' => 1,
            'stok_volume' => 250.0,
            'kemasan_jumlah' => 250,
            'kemasan_satuan' => 'ml',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Padi Ciherang 3',
            'varietas' => 'Ciherang Super',
            'tanggal_tanam' => now()->subDays(25)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // User uncheck checkbox -> kirim status = Belum
        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Rencana Semprot Jamur',
            'aplikasi_obat' => 'Score 250 EC Rencana: 20 ml',
            'jumlah_tank' => 1,
            'target_hst' => 25,
            'status' => 'Belum',
        ]);

        $response->assertRedirect();

        $medicine->refresh();
        // Stok tetap utuh 250 ml
        $this->assertEquals(250.0, (float) $medicine->stok_volume);
    }

    public function test_activity_with_mixed_medicine_and_equipment_deducts_medicine_multiplied_and_equipment_direct(): void
    {
        $decis = Medicine::create([
            'nama' => 'Decis Mix Test',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 50000,
            'stok' => 1,
            'stok_volume' => 100.0,
            'kemasan_jumlah' => 100,
            'kemasan_satuan' => 'ml',
        ]);

        $karung = Medicine::create([
            'nama' => 'Karung Plastik 50kg',
            'jenis' => 'Perlengkapan',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 3000,
            'stok' => 50,
            'stok_volume' => 50.0,
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'pcs',
        ]);

        $tali = Medicine::create([
            'nama' => 'Tali Cap Bel Golong',
            'jenis' => 'Perlengkapan',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 15000,
            'stok' => 10,
            'stok_volume' => 10.0,
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'rol',
        ]);

        $kresek = Medicine::create([
            'nama' => 'Plastik Kresek Panen',
            'jenis' => 'Perlengkapan',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 500,
            'stok' => 100,
            'stok_volume' => 100.0,
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'lembar',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Merah Mix',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => now()->subDays(40)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // Nyemprot sekalian panen pakai karung, tali, kresek dengan 3 tank semprot
        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Semprot dan Panen Raya',
            'aplikasi_obat' => 'Decis Mix Test: 15 ml, Karung Plastik 50kg: 10 pcs, Tali Cap Bel Golong: 2 rol, Plastik Kresek Panen: 5 lembar',
            'jumlah_tank' => 3,
            'target_hst' => 40,
            'status' => 'Selesai',
        ]);

        $response->assertRedirect();

        $decis->refresh();
        $karung->refresh();
        $tali->refresh();
        $kresek->refresh();

        // Decis obat: 100 ml - (15 ml * 3 tank) = 55 ml
        $this->assertEquals(55.0, (float) $decis->stok_volume);

        // Karung perlengkapan: 50 pcs - 10 pcs = 40 pcs (TIDAK dikali tank)
        $this->assertEquals(40.0, (float) $karung->stok_volume);
        $this->assertEquals(40, $karung->stok);

        // Tali perlengkapan: 10 rol - 2 rol = 8 rol (TIDAK dikali tank)
        $this->assertEquals(8.0, (float) $tali->stok_volume);
        $this->assertEquals(8, $tali->stok);

        // Kresek perlengkapan: 100 lembar - 5 lembar = 95 lembar (TIDAK dikali tank)
        $this->assertEquals(95.0, (float) $kresek->stok_volume);
        $this->assertEquals(95, $kresek->stok);
    }
}
