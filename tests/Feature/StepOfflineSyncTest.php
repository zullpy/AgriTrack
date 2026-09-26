<?php

namespace Tests\Feature;

use App\Models\LandPreparationStep;
use App\Models\PlantingSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StepOfflineSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_pengolahan_tanah_for_offline_cache(): void
    {
        LandPreparationStep::create([
            'nomor' => '1',
            'urutan' => 1,
            'judul' => 'Pembersihan Gulma',
            'waktu' => 'H-14',
            'deskripsi' => 'Bersihkan seluruh semak belukar',
        ]);

        $response = $this->getJson('/api/steps/pengolahan-tanah');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'judul' => 'Pembersihan Gulma',
        ]);
    }

    public function test_can_sync_offline_created_land_preparation_step(): void
    {
        $payload = [
            'mutations' => [
                [
                    'action' => 'create',
                    'local_id' => 'offline_land_123',
                    'data' => [
                        'nomor' => '9',
                        'judul' => 'Pemasangan Mulsa Offline',
                        'waktu' => 'H-3',
                        'deskripsi' => 'Pasang mulsa perak hitam saat terik',
                        'tips' => 'Tarik mulsa sekencang mungkin',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/steps/pengolahan-tanah/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_count' => 1,
        ]);

        $this->assertDatabaseHas('land_preparation_steps', [
            'nomor' => '9',
            'judul' => 'Pemasangan Mulsa Offline',
        ]);
    }

    public function test_can_sync_offline_updated_land_preparation_step(): void
    {
        $step = LandPreparationStep::create([
            'nomor' => '1',
            'urutan' => 1,
            'judul' => 'Judul Sebelum Edit',
            'waktu' => 'H-14',
            'deskripsi' => 'Deskripsi lama',
        ]);

        $payload = [
            'mutations' => [
                [
                    'action' => 'update',
                    'data' => [
                        'id' => $step->id,
                        'nomor' => '1',
                        'judul' => 'Judul Setelah Edit Offline',
                        'deskripsi' => 'Deskripsi baru yang diperbarui',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/steps/pengolahan-tanah/sync', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('land_preparation_steps', [
            'id' => $step->id,
            'judul' => 'Judul Setelah Edit Offline',
        ]);
    }

    public function test_can_sync_offline_deleted_land_preparation_step(): void
    {
        $step = LandPreparationStep::create([
            'nomor' => '99',
            'urutan' => 99,
            'judul' => 'Langkah Dihapus Offline',
            'deskripsi' => 'Akan dihapus',
        ]);

        $payload = [
            'mutations' => [
                [
                    'action' => 'delete',
                    'data' => [
                        'id' => $step->id,
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/steps/pengolahan-tanah/sync', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('land_preparation_steps', [
            'id' => $step->id,
        ]);
    }

    public function test_can_fetch_penanaman_bibit_for_offline_cache(): void
    {
        $seed = PlantingSeed::create([
            'nama_bibit' => 'Bibit Wortel Kuroda',
            'varietas' => 'Kuroda F1',
            'deskripsi' => 'Benih wortel manis unggulan',
            'urutan' => 1,
        ]);

        $seed->steps()->create([
            'nomor' => '1',
            'urutan' => 1,
            'judul' => 'Perendaman Benih',
            'deskripsi' => 'Rendam dalam air hangat 15 menit',
        ]);

        $response = $this->getJson('/api/steps/penanaman-bibit');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'nama_bibit' => 'Bibit Wortel Kuroda',
            'judul' => 'Perendaman Benih',
        ]);
    }

    public function test_can_sync_offline_created_planting_seed_and_step(): void
    {
        $payload = [
            'mutations' => [
                [
                    'action' => 'create_seed',
                    'local_id' => 'offline_seed_999',
                    'data' => [
                        'nama_bibit' => 'Bibit Tomat Servo',
                        'varietas' => 'Servo F1',
                        'deskripsi' => 'Tahan layu bakteri',
                    ],
                ],
                [
                    'action' => 'create_step',
                    'local_id' => 'offline_step_888',
                    'data' => [
                        'planting_seed_id' => 'offline_seed_999',
                        'nomor' => '1',
                        'judul' => 'Semai di Tray',
                        'waktu' => 'H-21',
                        'deskripsi' => 'Gunakan media cocopeat dan arang sekam',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/steps/penanaman-bibit/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_count' => 2,
        ]);

        $this->assertDatabaseHas('planting_seeds', [
            'nama_bibit' => 'Bibit Tomat Servo',
        ]);

        $this->assertDatabaseHas('planting_steps', [
            'judul' => 'Semai di Tray',
        ]);
    }

    public function test_can_sync_offline_updated_planting_step(): void
    {
        $seed = PlantingSeed::create([
            'nama_bibit' => 'Bibit Cabai Rawit',
            'urutan' => 1,
        ]);

        $step = $seed->steps()->create([
            'nomor' => '1',
            'urutan' => 1,
            'judul' => 'Judul Sebelum Edit',
            'deskripsi' => 'Lama',
        ]);

        $payload = [
            'mutations' => [
                [
                    'action' => 'update_step',
                    'data' => [
                        'id' => $step->id,
                        'nomor' => '1',
                        'judul' => 'Judul Setelah Edit Offline',
                        'deskripsi' => 'Baru',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/steps/penanaman-bibit/sync', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('planting_steps', [
            'id' => $step->id,
            'judul' => 'Judul Setelah Edit Offline',
        ]);
    }

    public function test_can_sync_offline_deleted_planting_seed(): void
    {
        $seed = PlantingSeed::create([
            'nama_bibit' => 'Bibit Jagung Manis',
            'urutan' => 1,
        ]);

        $step = $seed->steps()->create([
            'nomor' => '1',
            'urutan' => 1,
            'judul' => 'Tugal Lubang Tanam',
            'deskripsi' => 'Kedalaman 3-5 cm',
        ]);

        $payload = [
            'mutations' => [
                [
                    'action' => 'delete_seed',
                    'data' => [
                        'id' => $seed->id,
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/steps/penanaman-bibit/sync', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('planting_seeds', [
            'id' => $seed->id,
        ]);
        $this->assertDatabaseMissing('planting_steps', [
            'id' => $step->id,
        ]);
    }
}
