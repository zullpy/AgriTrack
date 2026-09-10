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
}
