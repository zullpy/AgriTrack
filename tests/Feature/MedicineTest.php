<?php

namespace Tests\Feature;

use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicineTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_medicine_with_priority_and_secondary_fields(): void
    {
        $payload = [
            'nama' => 'Curacron 500 EC',
            'jenis' => 'Insektisida',
            'cara_kerja' => 'Kontak',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'dosis_anjuran' => '15 ml / 16 liter air',
            'harga' => 68000,
            'unsur_bahan' => 'Profilofos 500 g/l',
            'interval_aplikasi' => 'Setiap 7 hari',
            'toko_obat' => 'Toko Tani Makmur',
            'tanggal_beli' => '2026-08-15',
            'keterangan' => 'Efektif membasmi ulat dan kutu',
            'catatan_keamanan' => 'Gunakan APD lengkap',
        ];

        $response = $this->post('/data-obat', $payload);

        $response->assertRedirect('/data-obat');
        $this->assertDatabaseHas('medicines', [
            'nama' => 'Curacron 500 EC',
            'jenis' => 'Insektisida',
            'cara_kerja' => 'Kontak',
            'harga' => 68000,
            'unsur_bahan' => 'Profilofos 500 g/l',
            'toko_obat' => 'Toko Tani Makmur',
        ]);

        $medicine = Medicine::where('nama', 'Curacron 500 EC')->first();
        $this->assertEquals('Rp 68.000', $medicine->formatted_harga);
    }

    public function test_can_filter_medicines_by_jenis_and_cara_kerja(): void
    {
        Medicine::create([
            'nama' => 'Gandasil D',
            'jenis' => 'Pupuk',
            'cara_kerja' => 'Sistemik',
            'tanaman_sasaran' => 'Semua Tanaman',
            'harga' => 25000,
        ]);

        Medicine::create([
            'nama' => 'Dithane M-45',
            'jenis' => 'Fungisida',
            'cara_kerja' => 'Kontak',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 85000,
        ]);

        $response = $this->get('/data-obat?cara_kerja=Sistemik');
        $response->assertStatus(200);
        $response->assertSee('Gandasil D');
        $response->assertDontSee('Dithane M-45');

        $response2 = $this->get('/data-obat?jenis=Fungisida');
        $response2->assertStatus(200);
        $response2->assertSee('Dithane M-45');
        $response2->assertDontSee('Gandasil D');
    }

    public function test_can_filter_medicines_by_fase(): void
    {
        Medicine::create([
            'nama' => 'Pupuk Vegetatif Nitrea',
            'jenis' => 'Pupuk',
            'cara_kerja' => 'Sistemik',
            'tanaman_sasaran' => 'Padi',
            'fase' => 'Vegetatif',
        ]);

        Medicine::create([
            'nama' => 'Pupuk Generatif MKP',
            'jenis' => 'Pupuk',
            'cara_kerja' => 'Sistemik',
            'tanaman_sasaran' => 'Cabai',
            'fase' => 'Generatif',
        ]);

        $response = $this->get('/data-obat?fase=Vegetatif');
        $response->assertStatus(200);
        $response->assertSee('Pupuk Vegetatif Nitrea');
        $response->assertDontSee('Pupuk Generatif MKP');

        $response2 = $this->get('/data-obat?fase=Generatif');
        $response2->assertStatus(200);
        $response2->assertSee('Pupuk Generatif MKP');
        $response2->assertDontSee('Pupuk Vegetatif Nitrea');
    }

    public function test_mobile_view_renders_priority_fields_and_no_gradient(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'cara_kerja' => 'Kontak',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'dosis_anjuran' => '2 gram / liter air',
            'harga' => 52000,
            'unsur_bahan' => 'Propineb 70%',
            'toko_obat' => 'Kios Tani Subur',
        ]);

        $response = $this->get('/data-obat');
        $response->assertStatus(200);

        // 6 Yellow priority fields must be present
        $response->assertSee('Antracol 70 WP');
        $response->assertSee('Fungisida');
        $response->assertSee('Kontak');
        $response->assertSee('Cabai, Tomat');
        $response->assertSee('2 gram / liter air');
        $response->assertSee('Rp 52.000');

        // Verify no gradient classes
        $content = $response->getContent();
        $this->assertStringNotContainsString('bg-gradient', $content);
        $this->assertStringNotContainsString('from-green', $content);
        $this->assertStringNotContainsString('from-emerald', $content);
    }

    public function test_can_save_and_update_medicine_with_formatted_rupiah_price(): void
    {
        // 1. Create with dot-formatted price "85.000"
        $payload = [
            'nama' => 'Score 250 EC',
            'jenis' => 'Fungisida',
            'cara_kerja' => 'Sistemik',
            'tanaman_sasaran' => 'Padi, Bawang Merah',
            'harga' => '85.000',
        ];

        $response = $this->post('/data-obat', $payload);
        $response->assertRedirect('/data-obat');

        $medicine = Medicine::where('nama', 'Score 250 EC')->first();
        $this->assertNotNull($medicine);
        $this->assertSame(85000, $medicine->harga);
        $this->assertSame('Rp 85.000', $medicine->formatted_harga);

        // 2. Update with dot-formatted price "125.000"
        $updatePayload = [
            'nama' => 'Score 250 EC',
            'jenis' => 'Fungisida',
            'cara_kerja' => 'Sistemik',
            'tanaman_sasaran' => 'Padi, Bawang Merah',
            'harga' => '125.000',
        ];

        $updateResponse = $this->put("/data-obat/{$medicine->id}", $updatePayload);
        $updateResponse->assertRedirect('/data-obat');

        $medicine->refresh();
        $this->assertSame(125000, $medicine->harga);
        $this->assertSame('Rp 125.000', $medicine->formatted_harga);
    }

    public function test_edit_form_renders_formatted_harga_with_dots(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Amistartop 325 SC',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi, Cabai',
            'harga' => 150000,
        ]);

        $response = $this->get("/data-obat/{$medicine->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('value="150.000"', false);
    }

    public function test_can_save_and_update_sasaran_obat_and_fase(): void
    {
        $payload = [
            'nama' => 'Root Most 100 EC',
            'jenis' => 'Pupuk',
            'cara_kerja' => 'Sistemik',
            'sasaran_obat' => 'Untuk menguatkan akar',
            'tanaman_sasaran' => 'Semua Tanaman',
            'fase' => 'Vegetatif',
        ];

        $response = $this->post('/data-obat', $payload);
        $response->assertRedirect('/data-obat');

        $this->assertDatabaseHas('medicines', [
            'nama' => 'Root Most 100 EC',
            'sasaran_obat' => 'Untuk menguatkan akar',
            'fase' => 'Vegetatif',
        ]);

        $medicine = Medicine::where('nama', 'Root Most 100 EC')->first();

        $updatePayload = [
            'nama' => 'Root Most 100 EC',
            'jenis' => 'Pupuk',
            'cara_kerja' => 'Sistemik',
            'sasaran_obat' => 'Untuk menguatkan akar & batang',
            'tanaman_sasaran' => 'Semua Tanaman',
            'fase' => 'Generatif',
        ];

        $updateResponse = $this->put("/data-obat/{$medicine->id}", $updatePayload);
        $updateResponse->assertRedirect('/data-obat');

        $medicine->refresh();
        $this->assertSame('Untuk menguatkan akar & batang', $medicine->sasaran_obat);
        $this->assertSame('Generatif', $medicine->fase);
    }

    public function test_can_create_and_filter_new_categories(): void
    {
        $bibit = Medicine::create([
            'nama' => 'Benih Cabai Rawit Ori 212',
            'jenis' => 'Bibit',
            'tanaman_sasaran' => 'Cabai Rawit',
            'harga' => 45000,
        ]);

        $perlengkapan = Medicine::create([
            'nama' => 'Mulsa Hitam Perak 80cm x 500m',
            'jenis' => 'Perlengkapan',
            'tanaman_sasaran' => 'Semua Tanaman',
            'harga' => 320000,
        ]);

        $peralatan = Medicine::create([
            'nama' => 'Sprayer Elektrik CBA 16 Liter',
            'jenis' => 'Peralatan',
            'tanaman_sasaran' => 'Semua Tanaman',
            'harga' => 480000,
        ]);

        $this->assertDatabaseHas('medicines', ['nama' => 'Benih Cabai Rawit Ori 212', 'jenis' => 'Bibit']);
        $this->assertDatabaseHas('medicines', ['nama' => 'Mulsa Hitam Perak 80cm x 500m', 'jenis' => 'Perlengkapan']);
        $this->assertDatabaseHas('medicines', ['nama' => 'Sprayer Elektrik CBA 16 Liter', 'jenis' => 'Peralatan']);

        $resBibit = $this->get('/data-obat?jenis=Bibit');
        $resBibit->assertStatus(200);
        $resBibit->assertSee('Benih Cabai Rawit Ori 212');
        $resBibit->assertDontSee('Sprayer Elektrik CBA 16 Liter');

        $resPerlengkapan = $this->get('/data-obat?jenis=Perlengkapan');
        $resPerlengkapan->assertStatus(200);
        $resPerlengkapan->assertSee('Mulsa Hitam Perak 80cm x 500m');
        $resPerlengkapan->assertDontSee('Benih Cabai Rawit Ori 212');

        $resPeralatan = $this->get('/data-obat?jenis=Peralatan');
        $resPeralatan->assertStatus(200);
        $resPeralatan->assertSee('Sprayer Elektrik CBA 16 Liter');
        $resPeralatan->assertDontSee('Mulsa Hitam Perak 80cm x 500m');
    }

    public function test_same_medicine_different_store_does_not_duplicate_row(): void
    {
        // 1. First purchase from Toko Tani Subur
        $payload1 = [
            'nama' => 'Curacron 500 EC',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'toko_obat' => 'Toko Tani Subur',
            'harga' => '68.000',
            'tanggal_beli' => '2026-09-01',
            'sasaran_obat' => 'Membasmi ulat grayak',
        ];
        $this->post('/data-obat', $payload1)->assertRedirect('/data-obat');

        $this->assertEquals(1, Medicine::where('nama', 'Curacron 500 EC')->count());

        // 2. Second purchase of same medicine from different store: Toko Berkah Tani with different price
        $payload2 = [
            'nama' => 'Curacron 500 EC',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'toko_obat' => 'Toko Berkah Tani',
            'harga' => '72.000',
            'tanggal_beli' => '2026-09-10',
        ];
        $this->post('/data-obat', $payload2)->assertRedirect('/data-obat');

        // Verify: Still exactly 1 row in medicines table (no duplicate row!)
        $this->assertEquals(1, Medicine::where('nama', 'Curacron 500 EC')->count());

        $medicine = Medicine::with('purchases')->where('nama', 'Curacron 500 EC')->first();
        $this->assertCount(2, $medicine->purchases);

        // Check desktop & mobile index view renders both store names and both prices on the same page
        $response = $this->get('/data-obat');
        $response->assertStatus(200);
        $response->assertSee('Curacron 500 EC');
        $response->assertSee('Toko Tani Subur');
        $response->assertSee('Rp 68.000');
        $response->assertSee('Toko Berkah Tani');
        $response->assertSee('Rp 72.000');
        $response->assertSee('Membasmi ulat grayak');
    }

    public function test_photo_upload_and_storage(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('nota_obat.jpg', 600, 600);

        $payload = [
            'nama' => 'Score 250 EC',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi, Bawang',
            'harga' => '95.000',
            'foto_nota' => $file,
        ];

        $this->post('/data-obat', $payload)->assertRedirect('/data-obat');

        $medicine = Medicine::where('nama', 'Score 250 EC')->first();
        $this->assertNotNull($medicine);
        $this->assertNotNull($medicine->foto_nota);
        $this->assertNotEmpty($medicine->foto_paths);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($medicine->foto_paths[0]);
        $this->assertNotNull($medicine->foto_url);
        $this->assertCount(1, $medicine->foto_urls);
    }

    public function test_multiple_photo_upload_and_storage(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file1 = \Illuminate\Http\UploadedFile::fake()->image('nota_1.jpg', 600, 600);
        $file2 = \Illuminate\Http\UploadedFile::fake()->image('nota_2.png', 800, 800);

        $payload = [
            'nama' => 'Amistartop 325 SC',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi, Bawang Merah',
            'harga' => '145.000',
            'foto_nota' => [$file1, $file2],
        ];

        $this->post('/data-obat', $payload)->assertRedirect('/data-obat');

        $medicine = Medicine::where('nama', 'Amistartop 325 SC')->first();
        $this->assertNotNull($medicine);
        $this->assertCount(2, $medicine->foto_paths);
        $this->assertCount(2, $medicine->foto_urls);

        foreach ($medicine->foto_paths as $storedPath) {
            \Illuminate\Support\Facades\Storage::disk('public')->assertExists($storedPath);
        }

        // Test delete on update: delete first photo
        $deletedPhoto = $medicine->foto_paths[0];
        $remainingPhoto = $medicine->foto_paths[1];

        $updatePayload = [
            'nama' => 'Amistartop 325 SC',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi, Bawang Merah',
            'deleted_foto_paths' => [$deletedPhoto],
        ];

        $this->put("/data-obat/{$medicine->id}", $updatePayload)->assertRedirect('/data-obat');

        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($deletedPhoto);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($remainingPhoto);

        $medicine->refresh();
        $this->assertCount(1, $medicine->foto_paths);
        $this->assertEquals($remainingPhoto, $medicine->foto_paths[0]);
    }

    public function test_merging_photos_when_submitting_existing_medicine(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file1 = \Illuminate\Http\UploadedFile::fake()->image('nota_lama.jpg', 600, 600);
        $this->post('/data-obat', [
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat, Cabai',
            'foto_nota' => [$file1],
        ])->assertRedirect('/data-obat');

        $medicine = Medicine::where('nama', 'Antracol 70 WP')->first();
        $this->assertCount(1, $medicine->foto_paths);
        $oldPath = $medicine->foto_paths[0];

        $file2 = \Illuminate\Http\UploadedFile::fake()->image('nota_baru.jpg', 600, 600);
        $this->post('/data-obat', [
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat, Cabai',
            'foto_nota' => [$file2],
        ])->assertRedirect('/data-obat');

        $medicine->refresh();
        $this->assertCount(2, $medicine->foto_paths);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($oldPath);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($medicine->foto_paths[1]);
    }

    public function test_same_store_updates_price_and_date_instead_of_adding_duplicate_purchase(): void
    {
        // 1. Simpan pertama obat dari Toko Tani Subur dengan harga 65.000 tanggal 2026-09-01
        $payload1 = [
            'nama' => 'Dithane M-45',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai, Kentang',
            'toko_obat' => 'Toko Tani Subur',
            'harga' => '65.000',
            'tanggal_beli' => '2026-09-01',
        ];
        $this->post('/data-obat', $payload1)->assertRedirect('/data-obat');

        $medicine = Medicine::with('purchases')->where('nama', 'Dithane M-45')->first();
        $this->assertNotNull($medicine);
        $this->assertCount(1, $medicine->purchases);
        $this->assertEquals(65000, $medicine->purchases->first()->harga);
        $this->assertEquals('2026-09-01', $medicine->purchases->first()->tanggal_beli->format('Y-m-d'));

        // 2. Simpan kedua untuk obat yang sama dari toko yang sama "Toko Tani Subur" tapi harga baru 75.000 dan tanggal 2026-09-12
        $payload2 = [
            'nama' => 'Dithane M-45',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai, Kentang',
            'toko_obat' => 'Toko Tani Subur',
            'harga' => '75.000',
            'tanggal_beli' => '2026-09-12',
        ];
        $this->post('/data-obat', $payload2)->assertRedirect('/data-obat');

        // Pastikan baris obat tetap 1 dan baris purchase tetap 1 (tidak duplikat toko)
        $this->assertEquals(1, Medicine::where('nama', 'Dithane M-45')->count());
        $medicine->refresh();
        $this->assertCount(1, $medicine->purchases);

        // Pastikan harga terupdate menjadi 75.000 dan tanggal terupdate 2026-09-12
        $updatedPurchase = $medicine->purchases->first();
        $this->assertEquals(75000, $updatedPurchase->harga);
        $this->assertEquals('2026-09-12', $updatedPurchase->tanggal_beli->format('Y-m-d'));

        // Pastikan master obat juga merefleksikan harga dan tanggal terbaru
        $this->assertEquals(75000, $medicine->harga);
        $this->assertEquals('2026-09-12', $medicine->tanggal_beli->format('Y-m-d'));
    }

    public function test_destroy_photo_removes_physical_file_and_database_record(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file1 = \Illuminate\Http\UploadedFile::fake()->image('foto_a.jpg', 600, 600);
        $file2 = \Illuminate\Http\UploadedFile::fake()->image('foto_b.jpg', 600, 600);

        $this->post('/data-obat', [
            'nama' => 'Starban 585 EC',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Bawang Merah',
            'foto_nota' => [$file1, $file2],
        ])->assertRedirect('/data-obat');

        $medicine = Medicine::where('nama', 'Starban 585 EC')->first();
        $this->assertCount(2, $medicine->foto_paths);

        $photoToDelete = $medicine->foto_paths[0];
        $photoToKeep = $medicine->foto_paths[1];

        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($photoToDelete);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($photoToKeep);

        // Panggil endpoint hapus foto
        $response = $this->deleteJson("/data-obat/{$medicine->id}/foto", [
            'path' => $photoToDelete,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifikasi file fisik terhapus dari storage
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($photoToDelete);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($photoToKeep);

        // Verifikasi database record diperbarui
        $medicine->refresh();
        $this->assertCount(1, $medicine->foto_paths);
        $this->assertEquals($photoToKeep, $medicine->foto_paths[0]);
    }

    public function test_price_can_be_cleared_or_deleted_on_update(): void
    {
        // Buat obat dengan harga awal 68.000 dan toko
        $this->post('/data-obat', [
            'nama' => 'Curacron 500 EC Test',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'harga' => '68.000',
            'toko_obat' => 'Toko Tani Makmur',
            'tanggal_beli' => '2026-09-01',
        ])->assertRedirect('/data-obat');

        $medicine = Medicine::with('purchases')->where('nama', 'Curacron 500 EC Test')->first();
        $this->assertNotNull($medicine);
        $this->assertEquals(68000, $medicine->harga);
        $this->assertCount(1, $medicine->purchases);
        $this->assertEquals(68000, $medicine->purchases->first()->harga);

        $purchaseId = $medicine->purchases->first()->id;

        // 1. Simpan update dengan harga dikosongkan (string kosong "")
        $updatePayload = [
            'nama' => 'Curacron 500 EC Test',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai, Tomat',
            'harga' => '', // Dihapus/dikosongkan oleh user
            'purchases' => [
                [
                    'id' => $purchaseId,
                    'toko_obat' => 'Toko Tani Makmur',
                    'harga' => '', // Dihapus/dikosongkan di baris pembelian juga
                    'tanggal_beli' => '2026-09-01',
                ]
            ],
        ];

        $this->put("/data-obat/{$medicine->id}", $updatePayload)->assertRedirect('/data-obat');

        $medicine->refresh();
        $this->assertNull($medicine->harga);
        $this->assertNull($medicine->formatted_harga);

        $purchase = $medicine->purchases()->find($purchaseId);
        $this->assertNotNull($purchase);
        $this->assertNull($purchase->harga);
    }
}




