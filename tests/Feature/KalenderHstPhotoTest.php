<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\CropActivity;
use App\Services\CloudinaryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KalenderHstPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        config([
            'services.cloudinary.cloud_name' => null,
            'services.cloudinary.api_key' => null,
            'services.cloudinary.api_secret' => null,
        ]);
    }

    public function test_can_upload_up_to_5_photos_for_activity_with_compression_and_storage(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Merah',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // Buat 3 foto dengan dimensi besar (>1600px) untuk memverifikasi kompresi dan resize
        $files = [
            UploadedFile::fake()->image('foto_daun_1.jpg', 2400, 1800),
            UploadedFile::fake()->image('foto_daun_2.png', 2000, 2000),
            UploadedFile::fake()->image('foto_daun_3.jpg', 1920, 1080),
        ];

        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Penyemprotan Kutu Kebul',
            'target_hst' => 5,
            'foto_kegiatan' => $files,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Penyemprotan Kutu Kebul',
            'target_hst' => 5,
        ]);

        $activity = CropActivity::where('nama_kegiatan', 'Penyemprotan Kutu Kebul')->first();
        $this->assertNotNull($activity);
        $this->assertCount(3, $activity->foto_urls);
        $this->assertEquals(3, $activity->foto_count);

        // Verifikasi file fisik tersimpan di disk public lokal (mode fallback saat kredensial Cloudinary kosong)
        foreach ($activity->foto_urls as $url) {
            $relativePath = ltrim(str_replace('/storage/', '', $url), '/');
            Storage::disk('public')->assertExists($relativePath);
        }
    }

    public function test_cannot_upload_more_than_5_photos_for_activity(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Tomat Mawar',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // Buat 6 foto (melebihi batas maksimal 5 foto)
        $files = [
            UploadedFile::fake()->image('f1.jpg', 800, 600),
            UploadedFile::fake()->image('f2.jpg', 800, 600),
            UploadedFile::fake()->image('f3.jpg', 800, 600),
            UploadedFile::fake()->image('f4.jpg', 800, 600),
            UploadedFile::fake()->image('f5.jpg', 800, 600),
            UploadedFile::fake()->image('f6.jpg', 800, 600),
        ];

        $response = $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Pemangkasan Tunas Air',
            'target_hst' => 10,
            'foto_kegiatan' => $files,
        ]);

        $response->assertSessionHasErrors('foto_kegiatan');
        $this->assertDatabaseMissing('crop_activities', [
            'nama_kegiatan' => 'Pemangkasan Tunas Air',
        ]);
    }

    public function test_can_delete_single_photo_and_remove_physical_file(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Bawang Daun',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $files = [
            UploadedFile::fake()->image('foto_a.jpg', 800, 600),
            UploadedFile::fake()->image('foto_b.jpg', 800, 600),
        ];

        $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Pembersihan Gulma',
            'target_hst' => 3,
            'foto_kegiatan' => $files,
        ]);

        $activity = CropActivity::where('nama_kegiatan', 'Pembersihan Gulma')->first();
        $this->assertCount(2, $activity->foto_urls);

        $firstPhotoUrl = $activity->foto_urls[0];
        $secondPhotoUrl = $activity->foto_urls[1];

        $firstPath = ltrim(str_replace('/storage/', '', $firstPhotoUrl), '/');
        $secondPath = ltrim(str_replace('/storage/', '', $secondPhotoUrl), '/');

        Storage::disk('public')->assertExists($firstPath);
        Storage::disk('public')->assertExists($secondPath);

        // Hapus foto pertama melalui endpoint AJAX
        $deleteResponse = $this->deleteJson("/kalender-hst/kegiatan/{$activity->id}/foto", [
            'target' => $firstPhotoUrl,
        ]);

        $deleteResponse->assertJson([
            'success' => true,
            'remaining_count' => 1,
        ]);

        // Verifikasi file fisik foto pertama sudah BENAR-BENAR TERHAPUS
        Storage::disk('public')->assertMissing($firstPath);
        // Foto kedua masih ada
        Storage::disk('public')->assertExists($secondPath);

        // Verifikasi database hanya menyisakan 1 foto
        $activity->refresh();
        $this->assertCount(1, $activity->foto_urls);
        $this->assertEquals($secondPhotoUrl, $activity->foto_urls[0]);
    }

    public function test_can_update_activity_with_new_photos_and_respect_max_5_limit(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Semangka',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // 1. Simpan kegiatan dengan 2 foto awal
        $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Kocor NPK',
            'target_hst' => 7,
            'foto_kegiatan' => [
                UploadedFile::fake()->image('awal1.jpg', 800, 600),
                UploadedFile::fake()->image('awal2.jpg', 800, 600),
            ],
        ]);

        $activity = CropActivity::where('nama_kegiatan', 'Kocor NPK')->first();
        $this->assertCount(2, $activity->foto_urls);

        // 2. Update dengan menambah 2 foto lagi (total 4 <= 5)
        $updateResponse = $this->put("/kalender-hst/kegiatan/{$activity->id}", [
            'nama_kegiatan' => 'Kocor NPK Dosis Tinggi',
            'target_hst' => 7,
            'foto_kegiatan' => [
                UploadedFile::fake()->image('tambah1.jpg', 800, 600),
                UploadedFile::fake()->image('tambah2.jpg', 800, 600),
            ],
        ]);

        $updateResponse->assertRedirect();
        $activity->refresh();
        $this->assertEquals('Kocor NPK Dosis Tinggi', $activity->nama_kegiatan);
        $this->assertCount(4, $activity->foto_urls);

        // 3. Update dengan menambah 3 foto lagi (total 4 + 3 = 7 > 5) -> harus gagal
        $failResponse = $this->put("/kalender-hst/kegiatan/{$activity->id}", [
            'nama_kegiatan' => 'Kocor NPK Dosis Tinggi',
            'target_hst' => 7,
            'foto_kegiatan' => [
                UploadedFile::fake()->image('over1.jpg', 800, 600),
                UploadedFile::fake()->image('over2.jpg', 800, 600),
                UploadedFile::fake()->image('over3.jpg', 800, 600),
            ],
        ]);

        $failResponse->assertSessionHasErrors('foto_kegiatan');
        $activity->refresh();
        // Jumlah foto tetap 4
        $this->assertCount(4, $activity->foto_urls);
    }

    public function test_destroy_activity_deletes_all_associated_physical_files(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Jagung Manis',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Pembumbunan',
            'target_hst' => 20,
            'foto_kegiatan' => [
                UploadedFile::fake()->image('bumbun1.jpg', 800, 600),
                UploadedFile::fake()->image('bumbun2.jpg', 800, 600),
            ],
        ]);

        $activity = CropActivity::where('nama_kegiatan', 'Pembumbunan')->first();
        $paths = array_map(fn ($url) => ltrim(str_replace('/storage/', '', $url), '/'), $activity->foto_urls);

        foreach ($paths as $p) {
            Storage::disk('public')->assertExists($p);
        }

        // Hapus kegiatan
        $response = $this->delete("/kalender-hst/kegiatan/{$activity->id}");
        $response->assertRedirect();

        $this->assertDatabaseMissing('crop_activities', ['id' => $activity->id]);

        // Verifikasi semua file fisik ikut terhapus
        foreach ($paths as $p) {
            Storage::disk('public')->assertMissing($p);
        }
    }

    public function test_destroy_crop_deletes_all_associated_physical_photos(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Timun Suri',
            'tanggal_tanam' => Carbon::today()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $this->post("/kalender-hst/tanaman/{$crop->id}/kegiatan", [
            'nama_kegiatan' => 'Pengairan',
            'target_hst' => 2,
            'foto_kegiatan' => [
                UploadedFile::fake()->image('air1.jpg', 800, 600),
            ],
        ]);

        $activity = CropActivity::where('nama_kegiatan', 'Pengairan')->first();
        $path = ltrim(str_replace('/storage/', '', $activity->foto_urls[0]), '/');
        Storage::disk('public')->assertExists($path);

        // Hapus tanaman
        $this->delete("/kalender-hst/tanaman/{$crop->id}");

        $this->assertDatabaseMissing('crops', ['id' => $crop->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_cloudinary_service_extract_public_id(): void
    {
        $service = new CloudinaryService;

        $url = 'https://res.cloudinary.com/demo/image/upload/v1570979139/pertanian/kegiatan_hst/sample_photo.webp';
        $publicId = $service->extractPublicId($url);

        $this->assertEquals('pertanian/kegiatan_hst/sample_photo', $publicId);
    }

    public function test_cloudinary_upload_and_destroy_with_mock(): void
    {
        config([
            'services.cloudinary.cloud_name' => 'demo_cloud',
            'services.cloudinary.api_key' => '1234567890',
            'services.cloudinary.api_secret' => 'abcdefsecret',
        ]);

        Http::fake([
            'https://api.cloudinary.com/v1_1/demo_cloud/image/upload' => Http::response([
                'secure_url' => 'https://res.cloudinary.com/demo_cloud/image/upload/v123456/pertanian/kegiatan_hst/test.webp',
                'public_id' => 'pertanian/kegiatan_hst/test',
            ], 200),
            'https://api.cloudinary.com/v1_1/demo_cloud/image/destroy' => Http::response([
                'result' => 'ok',
            ], 200),
        ]);

        $service = new CloudinaryService;
        $this->assertTrue($service->isConfigured());

        $file = UploadedFile::fake()->image('test_cloud.jpg', 800, 600);
        $result = $service->upload($file);

        $this->assertEquals('cloudinary', $result['storage_type']);
        $this->assertEquals('https://res.cloudinary.com/demo_cloud/image/upload/v123456/pertanian/kegiatan_hst/test.webp', $result['url']);
        $this->assertEquals('pertanian/kegiatan_hst/test', $result['public_id']);

        // Test delete from Cloudinary
        $deleted = $service->deleteImage($result['url']);
        $this->assertTrue($deleted);
    }
}
