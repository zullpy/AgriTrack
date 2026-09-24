<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\CropActivity;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class KalenderHstOfflineSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_sync_offline_created_activity_without_photos(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => now()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $payload = [
            'mutations' => [
                [
                    'type' => 'crop_activity',
                    'action' => 'create',
                    'crop_id' => $crop->id,
                    'local_id' => 'offline_act_12345',
                    'data' => [
                        'nama_kegiatan' => 'Penyemprotan Fungisida Offline',
                        'target_hst' => 10,
                        'aplikasi_obat' => 'Dithane 2 sendok / tangki',
                        'sasaran' => 'Pencegahan Antraknosa',
                        'keterangan' => 'Disemprot pagi hari jam 06.30',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/kalender-hst/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_count' => 1,
        ]);

        $this->assertDatabaseHas('crop_activities', [
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Penyemprotan Fungisida Offline',
            'target_hst' => 10,
            'aplikasi_obat' => 'Dithane 2 sendok / tangki',
        ]);
    }

    public function test_can_sync_offline_created_activity_with_base64_photos(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Tomat Servvo',
            'tanggal_tanam' => now()->subDays(5)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        // Mock CloudinaryService
        $mockCloudinary = Mockery::mock(CloudinaryService::class);
        $mockCloudinary->shouldReceive('upload')
            ->once()
            ->andReturn([
                'url' => 'https://res.cloudinary.com/demo/image/upload/v1234/agri_hst/sample.webp',
                'public_id' => 'agri_hst/sample',
                'storage_type' => 'cloudinary',
            ]);
        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        // 1x1 transparent PNG data url
        $dummyBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $payload = [
            'mutations' => [
                [
                    'type' => 'crop_activity',
                    'action' => 'create',
                    'crop_id' => $crop->id,
                    'local_id' => 'offline_act_photo_1',
                    'data' => [
                        'nama_kegiatan' => 'Dokumentasi Kocor Pupuk',
                        'target_hst' => 5,
                        'photos_base64' => [$dummyBase64],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/kalender-hst/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_count' => 1,
        ]);

        $act = CropActivity::where('nama_kegiatan', 'Dokumentasi Kocor Pupuk')->first();
        $this->assertNotNull($act);
        $this->assertNotEmpty($act->foto_kegiatan);
        $this->assertEquals('https://res.cloudinary.com/demo/image/upload/v1234/agri_hst/sample.webp', $act->foto_urls[0]);
    }

    public function test_can_sync_offline_updated_activity(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Bawang Merah',
            'tanggal_tanam' => now()->subDays(15)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $act = $crop->activities()->create([
            'nama_kegiatan' => 'Kegiatan Lama',
            'target_hst' => 15,
            'status' => 'Belum',
        ]);

        $payload = [
            'mutations' => [
                [
                    'type' => 'crop_activity',
                    'action' => 'update',
                    'crop_id' => $crop->id,
                    'data' => [
                        'id' => $act->id,
                        'nama_kegiatan' => 'Kegiatan Baru Diedit Offline',
                        'aplikasi_obat' => 'Gandasil B',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/kalender-hst/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_count' => 1,
        ]);

        $this->assertDatabaseHas('crop_activities', [
            'id' => $act->id,
            'nama_kegiatan' => 'Kegiatan Baru Diedit Offline',
            'aplikasi_obat' => 'Gandasil B',
        ]);
    }

    public function test_can_sync_offline_deleted_activity(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Jagung Manis',
            'tanggal_tanam' => now()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $act = $crop->activities()->create([
            'nama_kegiatan' => 'Kegiatan Akan Dihapus',
            'target_hst' => 1,
            'status' => 'Belum',
        ]);

        $payload = [
            'mutations' => [
                [
                    'type' => 'crop_activity',
                    'action' => 'delete',
                    'data' => [
                        'id' => $act->id,
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/kalender-hst/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_count' => 1,
        ]);

        $this->assertDatabaseMissing('crop_activities', [
            'id' => $act->id,
        ]);
    }

    public function test_can_sync_offline_harvest(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Timun Suri',
            'tanggal_tanam' => now()->subDays(40)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $payload = [
            'mutations' => [
                [
                    'type' => 'crop_activity',
                    'action' => 'record_harvest',
                    'crop_id' => $crop->id,
                    'data' => [
                        'tanggal_panen' => now()->toDateString(),
                        'total_panen' => '50 kg',
                        'harga_panen' => '10000',
                        'catatan' => 'Petikan perdana',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/kalender-hst/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_count' => 1,
        ]);

        $this->assertDatabaseHas('crop_harvests', [
            'crop_id' => $crop->id,
            'total_panen' => '50 kg',
            'catatan' => 'Petikan perdana',
        ]);
    }
}
