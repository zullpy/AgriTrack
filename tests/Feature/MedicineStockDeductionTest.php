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

    public function test_kocor_method_deducts_medicine_based_on_dose_per_plant_multiplied_by_population(): void
    {
        $poc = Medicine::create([
            'nama' => 'POC Bio Booster',
            'jenis' => 'Pupuk',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 80000,
            'stok' => 5,
            'stok_volume' => 5000.0, // 5.000 ml
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'liter',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Kaliber',
            'varietas' => 'Kaliber',
            'populasi' => '1.000 Pohon',
            'tanggal_tanam' => now()->subDays(15)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // Catat kegiatan Kocor: 5 ml per tanaman untuk 1.000 populasi
        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Kocor Pupuk Cair Susulan 1',
            'metode_aplikasi' => 'Kocor',
            'aplikasi_obat' => 'POC Bio Booster: 5 ml / tanaman',
            'target_hst' => 15,
            'status' => 'Selesai',
        ]);

        $response->assertRedirect();

        $activity = $crop->activities()->latest('id')->first();
        $this->assertNotNull($activity);
        $this->assertEquals('Kocor', $activity->metode_aplikasi);
        $this->assertEquals(1000.0, (float) $activity->populasi_target);
        $this->assertTrue((bool) $activity->stok_dipotong);

        $poc->refresh();
        // 5.000 ml - (5 ml * 1.000 populasi) = 0 ml
        $this->assertEquals(0.0, (float) $poc->stok_volume);

        // Toggle kembali ke Belum: stok harus kembali utuh 5.000 ml
        $toggleResponse = $this->postJson("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $toggleResponse->assertOk();

        $poc->refresh();
        $this->assertEquals(5000.0, (float) $poc->stok_volume);

        // Toggle lagi ke Selesai: stok terpotong kembali menjadi 0 ml
        $this->postJson("/kalender-hst/kegiatan/{$activity->id}/toggle")->assertOk();
        $poc->refresh();
        $this->assertEquals(0.0, (float) $poc->stok_volume);
    }

    public function test_tabur_method_deducts_medicine_using_mg_converted_to_grams(): void
    {
        $furadan = Medicine::create([
            'nama' => 'Furadan 3GR',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'harga' => 45000,
            'stok' => 2,
            'stok_volume' => 2000.0, // 2.000 gram
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'kg',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Setan',
            'populasi' => '1.000 Pohon',
            'tanggal_tanam' => now()->subDays(5)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // User menabur dengan satuan mg: 500 mg per tanaman untuk 1.000 pohon
        // 500 mg = 0.5 gram * 1.000 = 500 gram terpotong dari stok
        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Tabur Nematisida Lubang Tanam',
            'metode_aplikasi' => 'Tabur',
            'aplikasi_obat' => 'Furadan 3GR: 500 mg / tanaman',
            'target_hst' => 5,
            'status' => 'Selesai',
        ]);

        $response->assertRedirect();

        $furadan->refresh();
        // 2.000 gram - (500 mg * 1.000 pohon = 500 gram) = 1.500 gram
        $this->assertEquals(1500.0, (float) $furadan->stok_volume);
    }

    public function test_update_activity_switches_metode_aplikasi_and_recalculates_stock_correctly(): void
    {
        $dithane = Medicine::create([
            'nama' => 'Dithane M-45 80 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 60000,
            'stok' => 1,
            'stok_volume' => 1000.0, // 1.000 gram
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'kg',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Shypoon',
            'populasi' => '500 Tanaman',
            'tanggal_tanam' => now()->subDays(20)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // 1. Awalnya dicatat Semprot: 2 tank * 20 gram = 40 gram
        $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Semprot Fungisida',
            'metode_aplikasi' => 'Semprot',
            'jumlah_tank' => 2,
            'aplikasi_obat' => 'Dithane M-45 80 WP: 20 gram',
            'target_hst' => 20,
            'status' => 'Selesai',
        ]);

        $activity = $crop->activities()->latest('id')->first();
        $dithane->refresh();
        $this->assertEquals(960.0, (float) $dithane->stok_volume);

        // 2. Diedit menjadi Kocor: 1 gram * 500 populasi = 500 gram (stok lama 40g dikembalikan, lalu dipotong 500g -> sisa 500g)
        $updateResponse = $this->put("/kalender-hst/kegiatan/{$activity->id}", [
            'nama_kegiatan' => 'Ganti Jadi Kocor Fungisida',
            'metode_aplikasi' => 'Kocor',
            'populasi_target' => 500,
            'aplikasi_obat' => 'Dithane M-45 80 WP: 1 gram / tanaman',
            'target_hst' => 20,
            'tanggal_kegiatan' => now()->toDateString(),
        ]);

        $updateResponse->assertRedirect();

        $activity->refresh();
        $dithane->refresh();

        $this->assertEquals('Kocor', $activity->metode_aplikasi);
        $this->assertEquals(500.0, (float) $activity->populasi_target);
        // 1.000 - 500 = 500 gram
        $this->assertEquals(500.0, (float) $dithane->stok_volume);
    }

    public function test_kocor_and_tabur_with_direct_total_medicine_dose_and_dosis_per_tanaman(): void
    {
        $npk = Medicine::create([
            'nama' => 'NPK Mutiara 16-16-16',
            'jenis' => 'Pupuk',
            'tanaman_sasaran' => 'Semua Tanaman',
            'harga' => 20000,
            'stok' => 10,
            'stok_volume' => 10000.0, // 10.000 gram
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'kg',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Tomat Servo F1',
            'populasi' => '500 Tanaman',
            'tanggal_tanam' => now()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // Input kocor baru: user memasukkan total pupuk 1.000 gram NPK yang dilarutkan, dan dosis larutan 200 ml / tanaman
        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Kocor NPK Fase Vegetatif',
            'metode_aplikasi' => 'Kocor',
            'dosis_per_tanaman' => 200,
            'aplikasi_obat' => 'NPK Mutiara 16-16-16: 1000 gram',
            'target_hst' => 10,
            'status' => 'Selesai',
        ]);

        $response->assertRedirect();

        $activity = $crop->activities()->latest('id')->first();
        $this->assertNotNull($activity);
        $this->assertEquals('Kocor', $activity->metode_aplikasi);
        $this->assertEquals(200.0, (float) $activity->dosis_per_tanaman);

        $npk->refresh();
        // Stok terpotong 1000 gram langsung (bukan dikali populasi)
        $this->assertEquals(9000.0, (float) $npk->stok_volume);
    }

    public function test_marking_activity_done_when_dosage_exceeds_stock_records_shortage_and_notifies_user(): void
    {
        $curacron = Medicine::create([
            'nama' => 'Curacron 500 EC',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 45000,
            'stok' => 1,
            'stok_volume' => 50.0, // Hanya sisa 50 ml
            'kemasan_jumlah' => 100,
            'kemasan_satuan' => 'ml',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Merah',
            'populasi' => '1000 Tanaman',
            'tanggal_tanam' => now()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // Simpan kegiatan yang membutuhkan 20 ml x 5 tank = 100 ml (melebihi stok 50 ml)
        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Nyemprot Kutu Kebul',
            'metode_aplikasi' => 'Semprot',
            'aplikasi_obat' => 'Curacron 500 EC: 20 ml',
            'jumlah_tank' => 5,
            'target_hst' => 10,
            'status' => 'Selesai',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', function ($msg) {
            return str_contains($msg, 'Peringatan: Stok obat berikut melebihi persediaan gudang dan kini tersisa 0')
                && str_contains($msg, 'Curacron 500 EC (kurang 50 ml)');
        });

        $curacron->refresh();
        $this->assertEquals(0.0, (float) $curacron->stok_volume);
        $this->assertEquals(0, $curacron->stok);

        $activity = $crop->activities()->latest('id')->first();
        $this->assertTrue((bool) $activity->stok_dipotong);
        $deductions = $activity->stok_deductions;
        $this->assertNotEmpty($deductions);
        $this->assertEquals(50.0, (float) $deductions[0]['shortage']);
    }

    public function test_toggle_activity_status_includes_stock_shortage_warning_when_exceeded(): void
    {
        $dithane = Medicine::create([
            'nama' => 'Dithane M-45',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat',
            'harga' => 60000,
            'stok' => 1,
            'stok_volume' => 100.0, // Sisa 100 gram
            'kemasan_jumlah' => 1000,
            'kemasan_satuan' => 'gram',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Tomat Rampai',
            'populasi' => '500 Tanaman',
            'tanggal_tanam' => now()->subDays(5)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $activity = $crop->activities()->create([
            'nama_kegiatan' => 'Semprot Karat Daun',
            'metode_aplikasi' => 'Semprot',
            'aplikasi_obat' => 'Dithane M-45: 50 gram',
            'jumlah_tank' => 4, // 50 gram x 4 tank = 200 gram (melebihi stok 100 gram)
            'target_hst' => 5,
            'status' => 'Belum',
        ]);

        $response = $this->postJson("/kalender-hst/kegiatan/{$activity->id}/toggle");
        $response->assertOk();
        $json = $response->json();

        $this->assertTrue($json['success']);
        $this->assertEquals('Selesai', $json['status']);
        $this->assertStringContainsString('Peringatan: Stok obat berikut melebihi persediaan', $json['message']);
        $this->assertStringContainsString('Dithane M-45 (kurang 100 gram)', $json['message']);

        $dithane->refresh();
        $this->assertEquals(0.0, (float) $dithane->stok_volume);
    }
}
