<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_keuangan_snapshot_for_offline_sync(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => Carbon::now()->subDays(30),
            'status' => 'Sedang Ditanam',
        ]);

        $trx = FinancialTransaction::create([
            'crop_id' => $crop->id,
            'tipe' => 'pengeluaran',
            'kategori' => 'pemeliharaan',
            'sub_kategori' => 'nyemprot (obat dan hok penyemprotan)',
            'judul' => 'Beli Pestisida & Upah Semprot',
            'nominal' => 150000,
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'keterangan' => 'Semprot ulat daun',
        ]);

        $category = FinancialCategory::create([
            'nama' => 'Peralatan Khusus',
            'tipe' => 'pengeluaran',
        ]);

        $response = $this->getJson('/api/keuangan');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $response->assertJsonFragment([
            'raw_id' => $trx->id,
            'judul' => 'Beli Pestisida & Upah Semprot',
            'kategori' => 'pemeliharaan',
            'sub_kategori' => 'nyemprot (obat dan hok penyemprotan)',
            'nominal' => 150000,
        ]);

        $response->assertJsonFragment([
            'nama' => 'Peralatan Khusus',
        ]);
    }

    public function test_can_sync_offline_created_and_updated_transactions(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Padi Inpari 32',
            'varietas' => 'Inpari',
            'tanggal_tanam' => Carbon::now()->subDays(40),
            'status' => 'Sedang Ditanam',
        ]);

        $mutations = [
            [
                'action' => 'create_transaction',
                'local_id' => 'offline-trx-1',
                'data' => [
                    'crop_id' => $crop->id,
                    'tipe' => 'pengeluaran',
                    'kategori' => 'penanaman',
                    'sub_kategori' => 'bibit',
                    'judul' => 'Bibit Padi Unggul',
                    'nominal' => 250000,
                    'tanggal' => Carbon::now()->format('Y-m-d'),
                    'keterangan' => '5 kantong bibit',
                ],
            ],
            [
                'action' => 'create_category',
                'local_id' => 'offline-cat-1',
                'data' => [
                    'nama' => 'Sewa Alsintan Traktor',
                    'tipe' => 'pengeluaran',
                ],
            ],
        ];

        $response = $this->postJson('/api/keuangan/sync', [
            'mutations' => $mutations,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'processed_count' => 2,
            ]);

        $this->assertDatabaseHas('financial_transactions', [
            'judul' => 'Bibit Padi Unggul',
            'kategori' => 'penanaman',
            'sub_kategori' => 'bibit',
            'nominal' => 250000,
        ]);

        $this->assertDatabaseHas('financial_categories', [
            'nama' => 'Sewa Alsintan Traktor',
            'tipe' => 'pengeluaran',
        ]);

        $createdTrx = FinancialTransaction::where('judul', 'Bibit Padi Unggul')->firstOrFail();
        $createdCat = FinancialCategory::where('nama', 'Sewa Alsintan Traktor')->firstOrFail();

        // Test update and delete sync
        $subsequentMutations = [
            [
                'action' => 'update_transaction',
                'data' => [
                    'id' => $createdTrx->id,
                    'judul' => 'Bibit Padi Unggul Sertifikasi',
                    'nominal' => 300000,
                ],
            ],
            [
                'action' => 'delete_category',
                'data' => [
                    'id' => $createdCat->id,
                ],
            ],
        ];

        $syncResponse2 = $this->postJson('/api/keuangan/sync', [
            'mutations' => $subsequentMutations,
        ]);

        $syncResponse2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'processed_count' => 2,
            ]);

        $this->assertDatabaseHas('financial_transactions', [
            'id' => $createdTrx->id,
            'judul' => 'Bibit Padi Unggul Sertifikasi',
            'nominal' => 300000,
        ]);

        $this->assertDatabaseMissing('financial_categories', [
            'id' => $createdCat->id,
        ]);

        // Test delete transaction sync
        $deleteTrxMutation = [
            [
                'action' => 'delete_transaction',
                'data' => [
                    'id' => $createdTrx->id,
                ],
            ],
        ];

        $syncResponse3 = $this->postJson('/api/keuangan/sync', [
            'mutations' => $deleteTrxMutation,
        ]);

        $syncResponse3->assertStatus(200);
        $this->assertDatabaseMissing('financial_transactions', [
            'id' => $createdTrx->id,
        ]);
    }
}
