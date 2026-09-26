<?php

namespace Tests\Feature;

use App\Models\LandPreparationStep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StepTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_steps_index(): void
    {
        $response = $this->get('/steps');

        $response->assertStatus(200);
        $response->assertSee('Tahapan Budidaya');
        $response->assertSee('Tahapan Pengolahan Tanah');
        $response->assertSee('Tahapan Penanaman Bibit');
    }

    public function test_can_view_tahapan_pengolahan_tanah(): void
    {
        $response = $this->get('/steps/pengolahan-tanah');

        $response->assertStatus(200);
        $response->assertSee('Tahapan Pengolahan Tanah');
        $response->assertSee('Sanitasi & Pembersihan Lahan', false);
        $response->assertSee('Pembalikan & Penggemburan Tanah', false);
        $response->assertSee('Aplikasi Kapur Dolomit', false);
        $response->assertSee('Aplikasi Pupuk Dasar', false);
        $response->assertSee('Pembuatan Bedengan', false);
        $response->assertSee('Pemasangan Mulsa', false);
    }

    public function test_can_view_tahapan_penanaman_bibit(): void
    {
        $response = $this->get('/steps/penanaman-bibit');

        $response->assertStatus(200);
        $response->assertSee('Tahapan Penanaman Bibit');
        $response->assertSee('Seleksi & Pemilihan Benih', false);
        $response->assertSee('Perlakuan Benih', false);
        $response->assertSee('Persiapan Media Semai', false);
        $response->assertSee('Pemeliharaan Bibit', false);
        $response->assertSee('Aklimatisasi', false);
        $response->assertSee('Transplanting', false);
    }

    public function test_can_create_new_land_preparation_step(): void
    {
        $response = $this->post('/steps/pengolahan-tanah', [
            'nomor' => '7',
            'judul' => 'Pemasangan Selang Irigasi Tetes',
            'waktu' => 'H-2 Sebelum Tanam',
            'deskripsi' => 'Gelar selang drip di atas bedengan sebelum bibit ditanam.',
            'tips' => 'Pastikan sambungan ke pipa utama tidak bocor.',
        ]);

        $response->assertRedirect('/steps/pengolahan-tanah');
        $this->assertDatabaseHas('land_preparation_steps', [
            'nomor' => '7',
            'judul' => 'Pemasangan Selang Irigasi Tetes',
        ]);
    }

    public function test_can_update_land_preparation_step(): void
    {
        $this->get('/steps/pengolahan-tanah'); // auto-seeds 6 steps
        $step = LandPreparationStep::where('nomor', '1')->first();

        $response = $this->put("/steps/pengolahan-tanah/{$step->id}", [
            'nomor' => '1',
            'judul' => 'Sanitasi Lahan & Pembersihan Total',
            'waktu' => 'H-30 Sebelum Tanam',
            'deskripsi' => 'Deskripsi yang sudah diperbarui dengan sangat detail.',
            'tips' => 'Bakar sisa gulma jika berpenyakit.',
        ]);

        $response->assertRedirect('/steps/pengolahan-tanah');
        $this->assertDatabaseHas('land_preparation_steps', [
            'id' => $step->id,
            'judul' => 'Sanitasi Lahan & Pembersihan Total',
        ]);
    }

    public function test_can_delete_land_preparation_step(): void
    {
        $this->get('/steps/pengolahan-tanah'); // auto-seeds 6 steps
        $step = LandPreparationStep::where('nomor', '6')->first();

        $response = $this->delete("/steps/pengolahan-tanah/{$step->id}");

        $response->assertRedirect('/steps/pengolahan-tanah');
        $this->assertDatabaseMissing('land_preparation_steps', [
            'id' => $step->id,
        ]);
    }

    public function test_can_upload_photo_to_land_preparation_step(): void
    {
        Storage::fake('public');
        $this->get('/steps/pengolahan-tanah');
        $step = LandPreparationStep::first();

        $file = UploadedFile::fake()->image('kamera_lahan.jpg');

        $response = $this->post("/steps/pengolahan-tanah/{$step->id}/foto", [
            'foto_kamera' => [$file],
        ]);

        $response->assertRedirect('/steps/pengolahan-tanah');
        $step->refresh();
        $this->assertNotEmpty($step->foto_urls);
    }

    public function test_can_delete_photo_and_file_from_land_preparation_step(): void
    {
        Storage::fake('public');
        $this->get('/steps/pengolahan-tanah');
        $step = LandPreparationStep::first();

        $file = UploadedFile::fake()->image('kamera_lahan.jpg');

        $this->post("/steps/pengolahan-tanah/{$step->id}/foto", [
            'foto_kamera' => [$file],
        ]);

        $step->refresh();
        $this->assertCount(1, $step->foto_urls);
        $photoUrl = $step->foto_urls[0];

        $deleteResponse = $this->delete("/steps/pengolahan-tanah/{$step->id}/foto", [
            'photo_url' => $photoUrl,
        ]);

        $deleteResponse->assertRedirect('/steps/pengolahan-tanah');
        $step->refresh();
        $this->assertEmpty($step->foto_urls);
    }
}
