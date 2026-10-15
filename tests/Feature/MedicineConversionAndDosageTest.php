<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicineConversionAndDosageTest extends TestCase
{
    use RefreshDatabase;

    public function test_medicine_converts_kg_to_grams_and_calculates_price_per_gram(): void
    {
        $med = Medicine::create([
            'nama' => 'KALSIUM CALHA 1 kg',
            'jenis' => 'Pupuk',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'harga' => 25000,
            'kemasan_jumlah' => 1.0,
            'kemasan_satuan' => 'kg',
        ]);

        $this->assertEquals(1000.0, $med->base_total_unit);
        $this->assertEquals('gram', $med->base_unit_label);
        $this->assertEquals(25.0, $med->harga_per_base_unit);
        $this->assertEquals('Rp 25 / gram', $med->formatted_harga_per_base_unit);

        // Biaya 5 gram = 5 * 25 = 125
        $biaya5Gram = $med->harga_per_base_unit * 5;
        $this->assertEquals(125.0, $biaya5Gram);
    }

    public function test_medicine_converts_liter_to_ml_and_calculates_price_per_ml(): void
    {
        $med = Medicine::create([
            'nama' => 'PUPUK CAIR ORGANIK 1 Liter',
            'jenis' => 'Pupuk',
            'tanaman_sasaran' => 'Semua Tanaman',
            'harga' => 120000,
            'kemasan_jumlah' => 1.0,
            'kemasan_satuan' => 'liter',
        ]);

        $this->assertEquals(1000.0, $med->base_total_unit);
        $this->assertEquals('ml', $med->base_unit_label);
        $this->assertEquals(120.0, $med->harga_per_base_unit);
        $this->assertEquals('Rp 120 / ml', $med->formatted_harga_per_base_unit);

        // Biaya 15 ml = 15 * 120 = 1800
        $biaya15Ml = $med->harga_per_base_unit * 15;
        $this->assertEquals(1800.0, $biaya15Ml);
    }

    public function test_medicine_handles_gram_and_ml_packages_correctly(): void
    {
        $gramMed = Medicine::create([
            'nama' => 'ASAM HUMAT POWER SOIL 500 gram',
            'jenis' => 'Pupuk',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 35000,
            'kemasan_jumlah' => 500.0,
            'kemasan_satuan' => 'gram',
        ]);

        $this->assertEquals(500.0, $gramMed->base_total_unit);
        $this->assertEquals('gram', $gramMed->base_unit_label);
        $this->assertEquals(70.0, $gramMed->harga_per_base_unit);
        $this->assertEquals('Rp 70 / gram', $gramMed->formatted_harga_per_base_unit);

        $mlMed = Medicine::create([
            'nama' => 'DECIS 100 ml',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Tomat',
            'harga' => 38000,
            'kemasan_jumlah' => 100.0,
            'kemasan_satuan' => 'ml',
        ]);

        $this->assertEquals(100.0, $mlMed->base_total_unit);
        $this->assertEquals('ml', $mlMed->base_unit_label);
        $this->assertEquals(380.0, $mlMed->harga_per_base_unit);
        $this->assertEquals('Rp 380 / ml', $mlMed->formatted_harga_per_base_unit);
    }

    public function test_can_store_and_update_medicine_with_kemasan(): void
    {
        MedicineCategory::firstOrCreate(['nama' => 'Fungisida'], ['warna' => 'emerald', 'urutan' => 1]);

        $response = $this->post('/data-obat', [
            'nama' => 'Antracol 500 gram Super',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 80000,
            'kemasan_jumlah' => 500,
            'kemasan_satuan' => 'gram',
        ]);

        $response->assertRedirect('/data-obat');
        $this->assertDatabaseHas('medicines', [
            'nama' => 'Antracol 500 gram Super',
            'kemasan_jumlah' => 500.0,
            'kemasan_satuan' => 'gram',
        ]);

        $med = Medicine::where('nama', 'Antracol 500 gram Super')->firstOrFail();

        $updateResponse = $this->put("/data-obat/{$med->id}", [
            'nama' => 'Antracol 500 gram Super Edit',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'harga' => 85000,
            'kemasan_jumlah' => 500,
            'kemasan_satuan' => 'gram',
        ]);

        $updateResponse->assertRedirect('/data-obat');
        $this->assertDatabaseHas('medicines', [
            'id' => $med->id,
            'nama' => 'Antracol 500 gram Super Edit',
            'harga' => 85000,
        ]);
    }

    public function test_kalender_hst_crop_page_provides_medicines_with_conversion(): void
    {
        $med = Medicine::create([
            'nama' => 'KALSIUM CALHA 1 kg',
            'jenis' => 'Pupuk',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 25000,
            'kemasan_jumlah' => 1.0,
            'kemasan_satuan' => 'kg',
        ]);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Merah',
            'tanggal_tanam' => now()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->get("/kalender-hst/tanaman/{$crop->id}");
        $response->assertOk();
        $response->assertSee('KALSIUM CALHA 1 kg');
        $response->assertViewHas('medicines', function ($medicines) use ($med) {
            $found = $medicines->firstWhere('id', $med->id);

            return $found && $found->harga_per_base_unit == 25.0 && $found->base_unit_label === 'gram';
        });
    }

    public function test_can_save_crop_activity_with_dosis_obat_and_render_price(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Keriting',
            'tanggal_tanam' => now()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Penyemprotan Rutin',
            'target_hst' => 0,
            'tanggal_kegiatan' => now()->toDateString(),
            'aplikasi_obat' => 'KALSIUM CALHA 1 kg: 5 gram (Rp 125)',
            'sasaran' => 'Mencegah rontok bunga',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Penyemprotan Rutin',
            'aplikasi_obat' => 'KALSIUM CALHA 1 kg: 5 gram (Rp 125)',
        ]);

        $showResponse = $this->get("/kalender-hst/tanaman/{$crop->id}");
        $showResponse->assertOk();
        $showResponse->assertSee('KALSIUM CALHA 1 kg: 5 gram');
        $showResponse->assertSee('Rp 125');
    }

    public function test_can_save_crop_activity_and_apply_to_multiple_other_crops(): void
    {
        $crop1 = Crop::create([
            'nama_tanaman' => 'Tomat Servo',
            'tanggal_tanam' => now()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $crop2 = Crop::create([
            'nama_tanaman' => 'Cabai Keriting',
            'tanggal_tanam' => now()->subDays(20)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $crop3 = Crop::create([
            'nama_tanaman' => 'Terong Ungu',
            'tanggal_tanam' => now()->subDays(5)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->post("/kalender-hst/tanaman/{$crop1->id}/kegiatan", [
            'nama_kegiatan' => 'Penyemprotan Fungisida Massal',
            'tanggal_kegiatan' => now()->toDateString(),
            'aplikasi_obat' => 'Antracol 500 gram: 15 gram (Rp 1.050)',
            'sasaran' => 'Patek & Jamur',
            'other_crop_ids' => [$crop2->id, $crop3->id],
        ]);

        $response->assertRedirect();

        // Crop 1 (Tomat) harus memiliki kegiatan dengan target_hst = 10
        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop1->id,
            'nama_kegiatan' => 'Penyemprotan Fungisida Massal',
            'target_hst' => 10,
            'aplikasi_obat' => 'Antracol 500 gram: 15 gram (Rp 1.050)',
        ]);

        // Crop 2 (Cabai) harus memiliki kegiatan dengan target_hst = 20
        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop2->id,
            'nama_kegiatan' => 'Penyemprotan Fungisida Massal',
            'target_hst' => 20,
            'aplikasi_obat' => 'Antracol 500 gram: 15 gram (Rp 1.050)',
        ]);

        // Crop 3 (Terong) harus memiliki kegiatan dengan target_hst = 5
        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop3->id,
            'nama_kegiatan' => 'Penyemprotan Fungisida Massal',
            'target_hst' => 5,
            'aplikasi_obat' => 'Antracol 500 gram: 15 gram (Rp 1.050)',
        ]);
    }

    public function test_can_save_crop_activity_only_for_current_crop_when_hanya_ini_selected(): void
    {
        $crop1 = Crop::create([
            'nama_tanaman' => 'Tomat Servo',
            'tanggal_tanam' => now()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $crop2 = Crop::create([
            'nama_tanaman' => 'Cabai Keriting',
            'tanggal_tanam' => now()->subDays(20)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->post("/kalender-hst/tanaman/{$crop1->id}/kegiatan", [
            'nama_kegiatan' => 'Pemangkasan Tunas Air',
            'tanggal_kegiatan' => now()->toDateString(),
            'target_hst' => 10,
            'other_crop_ids' => [], // Hanya Ini
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop1->id,
            'nama_kegiatan' => 'Pemangkasan Tunas Air',
        ]);

        $this->assertDatabaseMissing('crop_activities', [
            'crop_id' => $crop2->id,
            'nama_kegiatan' => 'Pemangkasan Tunas Air',
        ]);
    }
}
