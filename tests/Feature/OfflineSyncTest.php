<?php

namespace Tests\Feature;

use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflineSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_medicines_for_offline_cache(): void
    {
        Medicine::create([
            'nama' => 'Fungisida Test',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi',
        ]);

        $response = $this->getJson('/api/medicines');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'nama' => 'Fungisida Test',
        ]);
    }

    public function test_can_sync_offline_created_medicine(): void
    {
        $payload = [
            'mutations' => [
                [
                    'action' => 'create',
                    'local_id' => 'offline_123',
                    'data' => [
                        'nama' => 'Pupuk Organik Offline',
                        'jenis' => 'Pupuk',
                        'tanaman_sasaran' => 'Sayuran',
                        'dosis_anjuran' => '10 gram / liter',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/medicines/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_count' => 1,
        ]);

        $this->assertDatabaseHas('medicines', [
            'nama' => 'Pupuk Organik Offline',
            'jenis' => 'Pupuk',
        ]);
    }

    public function test_can_sync_offline_updated_medicine(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Sebelum Edit',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai',
        ]);

        $payload = [
            'mutations' => [
                [
                    'action' => 'update',
                    'data' => [
                        'id' => $medicine->id,
                        'nama' => 'Sesudah Edit Offline',
                        'tanaman_sasaran' => 'Semua Tanaman',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/medicines/sync', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'nama' => 'Sesudah Edit Offline',
        ]);
    }

    public function test_can_sync_offline_deleted_medicine(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Obat Dihapus Offline',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat',
        ]);

        $payload = [
            'mutations' => [
                [
                    'action' => 'delete',
                    'data' => [
                        'id' => $medicine->id,
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/medicines/sync', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('medicines', [
            'id' => $medicine->id,
        ]);
    }

    public function test_can_sync_offline_created_medicine_with_photo(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $fakeBase64 = 'data:image/jpeg;base64,' . base64_encode('fake-image-content');

        $payload = [
            'mutations' => [
                [
                    'action' => 'create',
                    'local_id' => 'offline_photo_1',
                    'data' => [
                        'nama' => 'Insektisida Foto Offline',
                        'jenis' => 'Insektisida',
                        'tanaman_sasaran' => 'Bawang Merah',
                        'toko_obat' => 'Toko Tani Berkah',
                        'harga' => 75000,
                        'photos_base64' => [$fakeBase64],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/medicines/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_count' => 1,
        ]);

        $created = Medicine::where('nama', 'Insektisida Foto Offline')->first();
        $this->assertNotNull($created);
        $this->assertNotNull($created->foto_nota);
        $paths = $created->foto_paths;
        $this->assertCount(1, $paths);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($paths[0]);

        $this->assertDatabaseHas('medicine_purchases', [
            'medicine_id' => $created->id,
            'toko_obat' => 'Toko Tani Berkah',
            'harga' => 75000,
            'foto_nota' => $paths[0],
        ]);
    }
}

