<?php

namespace Tests\Feature;

use App\Models\FinancialTransaction;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        Storage::fake('public');

        $file = UploadedFile::fake()->image('nota_obat.jpg', 600, 600);

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
        Storage::disk('public')->assertExists($medicine->foto_paths[0]);
        $this->assertNotNull($medicine->foto_url);
        $this->assertCount(1, $medicine->foto_urls);
    }

    public function test_multiple_photo_upload_and_storage(): void
    {
        Storage::fake('public');

        $file1 = UploadedFile::fake()->image('nota_1.jpg', 600, 600);
        $file2 = UploadedFile::fake()->image('nota_2.png', 800, 800);

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
            Storage::disk('public')->assertExists($storedPath);
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

        Storage::disk('public')->assertMissing($deletedPhoto);
        Storage::disk('public')->assertExists($remainingPhoto);

        $medicine->refresh();
        $this->assertCount(1, $medicine->foto_paths);
        $this->assertEquals($remainingPhoto, $medicine->foto_paths[0]);
    }

    public function test_merging_photos_when_submitting_existing_medicine(): void
    {
        Storage::fake('public');

        $file1 = UploadedFile::fake()->image('nota_lama.jpg', 600, 600);
        $this->post('/data-obat', [
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat, Cabai',
            'foto_nota' => [$file1],
        ])->assertRedirect('/data-obat');

        $medicine = Medicine::where('nama', 'Antracol 70 WP')->first();
        $this->assertCount(1, $medicine->foto_paths);
        $oldPath = $medicine->foto_paths[0];

        $file2 = UploadedFile::fake()->image('nota_baru.jpg', 600, 600);
        $this->post('/data-obat', [
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat, Cabai',
            'foto_nota' => [$file2],
        ])->assertRedirect('/data-obat');

        $medicine->refresh();
        $this->assertCount(2, $medicine->foto_paths);
        Storage::disk('public')->assertExists($oldPath);
        Storage::disk('public')->assertExists($medicine->foto_paths[1]);
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
        Storage::fake('public');

        $file1 = UploadedFile::fake()->image('foto_a.jpg', 600, 600);
        $file2 = UploadedFile::fake()->image('foto_b.jpg', 600, 600);

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

        Storage::disk('public')->assertExists($photoToDelete);
        Storage::disk('public')->assertExists($photoToKeep);

        // Panggil endpoint hapus foto
        $response = $this->deleteJson("/data-obat/{$medicine->id}/foto", [
            'path' => $photoToDelete,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifikasi file fisik terhapus dari storage
        Storage::disk('public')->assertMissing($photoToDelete);
        Storage::disk('public')->assertExists($photoToKeep);

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
                ],
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

    public function test_can_get_medicine_categories(): void
    {
        $response = $this->getJson('/data-obat/kategori');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'nama', 'warna', 'deskripsi', 'urutan', 'theme_config'],
                ],
                'color_presets',
            ]);
    }

    public function test_can_create_new_medicine_category(): void
    {
        $response = $this->postJson('/data-obat/kategori', [
            'nama' => 'Herbisida',
            'warna' => 'orange',
            'deskripsi' => 'Racun pembasmi gulma dan rumput liar',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nama' => 'Herbisida',
                    'warna' => 'orange',
                    'deskripsi' => 'Racun pembasmi gulma dan rumput liar',
                ],
            ]);

        $this->assertDatabaseHas('medicine_categories', [
            'nama' => 'Herbisida',
            'warna' => 'orange',
        ]);
    }

    public function test_cannot_create_duplicate_medicine_category_name(): void
    {
        $this->postJson('/data-obat/kategori', [
            'nama' => 'Herbisida',
            'warna' => 'orange',
        ])->assertStatus(200);

        $response = $this->postJson('/data-obat/kategori', [
            'nama' => 'Herbisida',
            'warna' => 'cyan',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama']);
    }

    public function test_can_update_medicine_category_and_sync_medicine_jenis(): void
    {
        $category = MedicineCategory::create([
            'nama' => 'Herbisida Awal',
            'warna' => 'orange',
            'urutan' => 8,
        ]);

        $medicine = Medicine::create([
            'nama' => 'Gramoxone 276 SL',
            'jenis' => 'Herbisida Awal',
            'tanaman_sasaran' => 'Lahan Tanpa Tanaman',
            'harga' => 95000,
        ]);

        $response = $this->putJson("/data-obat/kategori/{$category->id}", [
            'nama' => 'Herbisida Kontak',
            'warna' => 'red',
            'deskripsi' => 'Herbisida kontak purna tumbuh',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $category->refresh();
        $this->assertEquals('Herbisida Kontak', $category->nama);
        $this->assertEquals('red', $category->warna);

        $medicine->refresh();
        $this->assertEquals('Herbisida Kontak', $medicine->jenis);
    }

    public function test_cannot_delete_category_that_has_medicines(): void
    {
        $category = MedicineCategory::create([
            'nama' => 'Akarisida',
            'warna' => 'teal',
            'urutan' => 9,
        ]);

        Medicine::create([
            'nama' => 'Samite 135 EC',
            'jenis' => 'Akarisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 70000,
        ]);

        $response = $this->deleteJson("/data-obat/kategori/{$category->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('medicine_categories', [
            'id' => $category->id,
            'nama' => 'Akarisida',
        ]);
    }

    public function test_can_delete_unused_category(): void
    {
        $category = MedicineCategory::create([
            'nama' => 'Kategori Kosong',
            'warna' => 'slate',
            'urutan' => 20,
        ]);

        $response = $this->deleteJson("/data-obat/kategori/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('medicine_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_can_create_medicine_with_custom_category(): void
    {
        MedicineCategory::create([
            'nama' => 'ZPT',
            'warna' => 'cyan',
            'urutan' => 15,
        ]);

        $payload = [
            'nama' => 'Atonik 6.0 L',
            'jenis' => 'ZPT',
            'tanaman_sasaran' => 'Padi, Cabai',
            'harga' => 45000,
        ];

        $response = $this->post('/data-obat', $payload);
        $response->assertRedirect('/data-obat');

        $this->assertDatabaseHas('medicines', [
            'nama' => 'Atonik 6.0 L',
            'jenis' => 'ZPT',
        ]);
    }

    public function test_creating_multiple_categories_without_specifying_color_assigns_distinct_automatic_colors(): void
    {
        $response1 = $this->postJson('/data-obat/kategori', [
            'nama' => 'Kategori Auto 1',
        ]);
        $response1->assertStatus(200);
        $color1 = $response1->json('data.warna');

        $response2 = $this->postJson('/data-obat/kategori', [
            'nama' => 'Kategori Auto 2',
        ]);
        $response2->assertStatus(200);
        $color2 = $response2->json('data.warna');

        $response3 = $this->postJson('/data-obat/kategori', [
            'nama' => 'Kategori Auto 3',
        ]);
        $response3->assertStatus(200);
        $color3 = $response3->json('data.warna');

        $this->assertNotEmpty($color1);
        $this->assertNotEmpty($color2);
        $this->assertNotEmpty($color3);

        // Pastikan setiap kategori baru mendapatkan warna yang berbeda
        $this->assertNotEquals($color1, $color2);
        $this->assertNotEquals($color2, $color3);
        $this->assertNotEquals($color1, $color3);
    }

    public function test_can_create_and_update_medicine_with_formulasi_pestisida(): void
    {
        $payload = [
            'nama' => 'Score 250 EC',
            'jenis' => 'Fungisida',
            'cara_kerja' => 'Sistemik',
            'formulasi' => 'EC',
            'tanaman_sasaran' => 'Bawang Merah',
            'dosis_anjuran' => '1 ml/l',
        ];

        $response = $this->post('/data-obat', $payload);
        $response->assertRedirect('/data-obat');

        $this->assertDatabaseHas('medicines', [
            'nama' => 'Score 250 EC',
            'formulasi' => 'EC',
        ]);

        $medicine = Medicine::where('nama', 'Score 250 EC')->first();
        $this->assertEquals('EC', $medicine->formulasi);
        $this->assertEquals('cairan pekat seperti minyak', $medicine->formulasi_ciri_fisik);
        $this->assertEquals('EC • cairan pekat seperti minyak', $medicine->formulasi_detail);

        // Update to SC
        $updatePayload = [
            'nama' => 'Score 250 EC',
            'jenis' => 'Fungisida',
            'cara_kerja' => 'Sistemik',
            'formulasi' => 'SC',
            'tanaman_sasaran' => 'Bawang Merah',
            'dosis_anjuran' => '1 ml/l',
        ];

        $updateResponse = $this->put("/data-obat/{$medicine->id}", $updatePayload);
        $updateResponse->assertRedirect('/data-obat');

        $medicine->refresh();
        $this->assertEquals('SC', $medicine->formulasi);
        $this->assertEquals('suspensi pekat', $medicine->formulasi_ciri_fisik);
        $this->assertEquals('SC • suspensi pekat', $medicine->formulasi_detail);
    }

    public function test_can_filter_medicines_by_formulasi(): void
    {
        Medicine::create([
            'nama' => 'Amistar Top 325 SC',
            'jenis' => 'Fungisida',
            'formulasi' => 'SC',
            'tanaman_sasaran' => 'Padi',
        ]);

        Medicine::create([
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'formulasi' => 'WP',
            'tanaman_sasaran' => 'Cabai',
        ]);

        $response = $this->get('/data-obat?formulasi=SC');
        $response->assertStatus(200);
        $response->assertSee('Amistar Top 325 SC');
        $response->assertDontSee('Antracol 70 WP');

        $responseWp = $this->get('/data-obat?formulasi=WP');
        $responseWp->assertStatus(200);
        $responseWp->assertSee('Antracol 70 WP');
        $responseWp->assertDontSee('Amistar Top 325 SC');
    }

    public function test_can_update_category_via_post_method_spoofing(): void
    {
        $category = MedicineCategory::create([
            'nama' => 'Bakterisida Test',
            'warna' => 'emerald',
            'deskripsi' => 'Pengendali bakteri',
            'urutan' => 30,
        ]);

        $response = $this->postJson("/data-obat/kategori/{$category->id}", [
            'nama' => 'Bakterisida Update',
            'warna' => 'blue',
            'deskripsi' => 'Pengendali bakteri update',
            '_method' => 'PUT',
        ], [
            'X-HTTP-Method-Override' => 'PUT',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('medicine_categories', [
            'id' => $category->id,
            'nama' => 'Bakterisida Update',
            'deskripsi' => 'Pengendali bakteri update',
        ]);
    }

    public function test_can_delete_category_via_post_method_spoofing(): void
    {
        $category = MedicineCategory::create([
            'nama' => 'Kategori Delete Test',
            'warna' => 'amber',
            'urutan' => 31,
        ]);

        $response = $this->postJson("/data-obat/kategori/{$category->id}", [
            '_method' => 'DELETE',
        ], [
            'X-HTTP-Method-Override' => 'DELETE',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('medicine_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_medicine_category_ensure_table_exists(): void
    {
        MedicineCategory::ensureTableExists();
        $this->assertTrue(MedicineCategory::where('nama', 'Fungisida')->exists());
        $this->assertTrue(MedicineCategory::where('nama', 'Insektisida')->exists());
    }

    public function test_can_create_medicine_with_stok(): void
    {
        $response = $this->post('/data-obat', [
            'nama' => 'Score 250 EC',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Bawang Merah',
            'dosis_anjuran' => '1 ml/liter',
            'harga' => 75000,
            'stok' => 8,
            'kemasan_satuan' => 'botol',
            'toko_obat' => 'Toko Subur',
            'tanggal_beli' => '2026-10-10',
        ]);

        $response->assertRedirect('/data-obat');

        $this->assertDatabaseHas('medicines', [
            'nama' => 'Score 250 EC',
            'stok' => 8,
            'harga' => 75000,
        ]);

        $medicine = Medicine::where('nama', 'Score 250 EC')->first();
        $this->assertEquals(8, $medicine->stok);
        $this->assertEquals('8 botol', $medicine->stok_label);
    }

    public function test_purchasing_existing_medicine_updates_price_and_increments_stock(): void
    {
        $existing = Medicine::create([
            'nama' => 'Amistar Top',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi, Jagung',
            'harga' => 120000,
            'stok' => 3,
            'toko_obat' => 'Toko Lama',
        ]);

        // Submit form with existing medicine name, new price, and purchase qty 4
        $response = $this->post('/data-obat', [
            'nama' => 'amistar top ', // slightly different case and whitespace
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi, Jagung',
            'harga' => 135000,
            'toko_obat' => 'Toko Tani Baru',
            'tanggal_beli' => '2026-10-19',
            'purchases' => [
                [
                    'toko_obat' => 'Toko Tani Baru',
                    'harga' => 135000,
                    'jumlah' => 4,
                    'tanggal_beli' => '2026-10-19',
                ],
            ],
        ]);

        $response->assertRedirect('/data-obat');

        // Should not create a new medicine row
        $this->assertEquals(1, Medicine::where('nama', 'LIKE', '%Amistar Top%')->count());

        $existing->refresh();
        $this->assertEquals(135000, $existing->harga);
        $this->assertEquals(7, $existing->stok); // 3 + 4 = 7
        $this->assertEquals('Toko Tani Baru', $existing->toko_obat);

        $this->assertDatabaseHas('medicine_purchases', [
            'medicine_id' => $existing->id,
            'toko_obat' => 'Toko Tani Baru',
            'harga' => 135000,
            'jumlah' => 4,
        ]);
    }

    public function test_store_purchase_route_updates_price_and_increments_stock(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Gramoxone 276 SL',
            'jenis' => 'Herbisida',
            'tanaman_sasaran' => 'Gulma Umum',
            'harga' => 80000,
            'stok' => 2,
            'toko_obat' => 'Toko Rejeki',
        ]);

        $response = $this->post("/data-obat/{$medicine->id}/pembelian", [
            'toko_obat' => 'Toko Sumber Makmur',
            'harga' => 'Rp 88.000',
            'jumlah' => 5,
            'tanggal_beli' => '2026-10-19',
            'catatan' => 'Restock musim tanam',
        ]);

        $response->assertRedirect();

        $medicine->refresh();
        $this->assertEquals(88000, $medicine->harga);
        $this->assertEquals(7, $medicine->stok); // 2 + 5 = 7
        $this->assertEquals('Toko Sumber Makmur', $medicine->toko_obat);

        $this->assertDatabaseHas('medicine_purchases', [
            'medicine_id' => $medicine->id,
            'toko_obat' => 'Toko Sumber Makmur',
            'harga' => 88000,
            'jumlah' => 5,
            'catatan' => 'Restock musim tanam',
        ]);
    }

    public function test_stok_obat_page_loads_and_filters_correctly(): void
    {
        Medicine::create([
            'nama' => 'Obat Aman 1',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 50000,
            'stok' => 10,
        ]);

        Medicine::create([
            'nama' => 'Obat Menipis 1',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat',
            'harga' => 60000,
            'stok' => 2,
        ]);

        Medicine::create([
            'nama' => 'Obat Habis 1',
            'jenis' => 'Pupuk',
            'tanaman_sasaran' => 'Padi',
            'harga' => 40000,
            'stok' => 0,
        ]);

        $response = $this->get('/stok-obat');
        $response->assertStatus(200);
        $response->assertSee('Stok & Pembelian Obat');
        $response->assertSee('Obat Aman 1');
        $response->assertSee('Obat Menipis 1');
        $response->assertSee('Obat Habis 1');

        // Filter status aman
        $resAman = $this->get('/stok-obat?status=aman');
        $resAman->assertStatus(200);
        $this->assertTrue($resAman->viewData('medicines')->pluck('nama')->contains('Obat Aman 1'));
        $this->assertFalse($resAman->viewData('medicines')->pluck('nama')->contains('Obat Menipis 1'));
        $this->assertFalse($resAman->viewData('medicines')->pluck('nama')->contains('Obat Habis 1'));

        // Filter status menipis
        $resMenipis = $this->get('/stok-obat?status=menipis');
        $resMenipis->assertStatus(200);
        $this->assertTrue($resMenipis->viewData('medicines')->pluck('nama')->contains('Obat Menipis 1'));
        $this->assertFalse($resMenipis->viewData('medicines')->pluck('nama')->contains('Obat Aman 1'));
        $this->assertFalse($resMenipis->viewData('medicines')->pluck('nama')->contains('Obat Habis 1'));

        // Filter status habis
        $resHabis = $this->get('/stok-obat?status=habis');
        $resHabis->assertStatus(200);
        $this->assertTrue($resHabis->viewData('medicines')->pluck('nama')->contains('Obat Habis 1'));
        $this->assertFalse($resHabis->viewData('medicines')->pluck('nama')->contains('Obat Aman 1'));
    }

    public function test_store_general_purchase_updates_existing_medicine_or_creates_new(): void
    {
        $existing = Medicine::create([
            'nama' => 'Bion M 1/48 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 90000,
            'stok' => 1,
            'toko_obat' => 'Toko Awal',
            'tanggal_beli' => '2026-09-01',
        ]);

        // 1. Purchase existing medicine with DIFFERENT price (90.000 -> 95.000)
        $resp1 = $this->post('/stok-obat/pembelian', [
            'medicine_id' => $existing->id,
            'toko_obat' => 'Toko Maju',
            'harga' => 'Rp 95.000',
            'jumlah' => 3,
            'tanggal_beli' => '2026-10-19',
        ]);

        $resp1->assertRedirect();
        $existing->refresh();
        $this->assertEquals(95000, $existing->harga); // Price updated because different
        $this->assertEquals(4, $existing->stok); // 1 + 3 = 4
        $this->assertEquals('2026-10-19', $existing->tanggal_beli->toDateString());
        $this->assertEquals('Toko Maju', $existing->toko_obat);

        // 2. Purchase existing medicine with SAME price (95.000 -> 95.000): price remains, date updated
        $respSamePrice = $this->post('/stok-obat/pembelian', [
            'medicine_id' => $existing->id,
            'toko_obat' => 'Toko Maju',
            'harga' => 'Rp 95.000',
            'jumlah' => 2,
            'tanggal_beli' => '2026-10-25',
        ]);

        $respSamePrice->assertRedirect();
        $existing->refresh();
        $this->assertEquals(95000, $existing->harga); // Price kept same
        $this->assertEquals(6, $existing->stok); // 4 + 2 = 6
        $this->assertEquals('2026-10-25', $existing->tanggal_beli->toDateString()); // Date updated!

        // 3. Purchase a brand new medicine for the first time: directly saved to data obat & stok
        $resp2 = $this->post('/stok-obat/pembelian', [
            'nama' => 'Antracol Baru 70 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Semua Sayuran',
            'toko_obat' => 'Toko Barokah Tani',
            'harga' => 'Rp 65.000',
            'jumlah' => 6,
            'tanggal_beli' => '2026-10-19',
        ]);

        $resp2->assertRedirect();
        $this->assertDatabaseHas('medicines', [
            'nama' => 'Antracol Baru 70 WP',
            'harga' => 65000,
            'stok' => 6,
            'toko_obat' => 'Toko Barokah Tani',
        ]);

        $newMed = Medicine::where('nama', 'Antracol Baru 70 WP')->first();
        $this->assertNotNull($newMed);
        $this->assertEquals(6, $newMed->stok);
        $this->assertDatabaseHas('medicine_purchases', [
            'medicine_id' => $newMed->id,
            'harga' => 65000,
            'jumlah' => 6,
            'toko_obat' => 'Toko Barokah Tani',
        ]);
    }

    public function test_record_purchase_stores_and_updates_kemasan_satuan_and_ukuran(): void
    {
        // 1. Test quick purchase updating kemasan
        $medicine = Medicine::create([
            'nama' => 'Curacron 500 EC Test',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 70000,
            'stok' => 1,
            'kemasan_jumlah' => 100,
            'kemasan_satuan' => 'ml',
        ]);

        $response = $this->post("/data-obat/{$medicine->id}/pembelian", [
            'toko_obat' => 'Toko Makmur Tani',
            'harga' => 'Rp 75.000',
            'jumlah' => 3,
            'kemasan_jumlah' => 250,
            'kemasan_satuan' => 'ml',
            'tanggal_beli' => '2026-10-20',
            'catatan' => 'Beli botol 250ml',
        ]);

        $response->assertRedirect();
        $medicine->refresh();

        $this->assertEquals(250, $medicine->kemasan_jumlah);
        $this->assertEquals('ml', $medicine->kemasan_satuan);
        $this->assertEquals(4, $medicine->stok); // 1 + 3

        $this->assertDatabaseHas('medicine_purchases', [
            'medicine_id' => $medicine->id,
            'toko_obat' => 'Toko Makmur Tani',
            'kemasan_jumlah' => 250,
            'kemasan_satuan' => 'ml',
            'jumlah' => 3,
        ]);

        $latestPurchase = $medicine->purchases()->first();
        $this->assertEquals('250 ml', $latestPurchase->kemasan_label);

        // 2. Test general purchase for new medicine with kemasan
        $responseNew = $this->post('/stok-obat/pembelian', [
            'nama' => 'Daconil 75 WP Super',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Bawang',
            'toko_obat' => 'Toko Agro Lestari',
            'harga' => 'Rp 50.000',
            'jumlah' => 2,
            'kemasan_jumlah' => 500,
            'kemasan_satuan' => 'gram',
            'tanggal_beli' => '2026-10-20',
        ]);

        $responseNew->assertRedirect();

        $newMed = Medicine::where('nama', 'Daconil 75 WP Super')->first();
        $this->assertNotNull($newMed);
        $this->assertEquals(500, $newMed->kemasan_jumlah);
        $this->assertEquals('gram', $newMed->kemasan_satuan);
        $this->assertEquals('500 gram', $newMed->kemasan_label);
        $this->assertEquals(2, $newMed->stok);
        $this->assertEquals(1000.0, (float) $newMed->stok_volume);

        $this->assertDatabaseHas('medicine_purchases', [
            'medicine_id' => $newMed->id,
            'kemasan_jumlah' => 500,
            'kemasan_satuan' => 'gram',
            'jumlah' => 2,
            'toko_obat' => 'Toko Agro Lestari',
        ]);
    }

    public function test_recording_purchase_creates_expense_transaction_automatically(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Gramoxone 276 SL',
            'jenis' => 'Herbisida',
            'tanaman_sasaran' => 'Gulma Umum',
            'harga' => 80000,
            'stok' => 2,
        ]);

        $this->post("/data-obat/{$medicine->id}/pembelian", [
            'toko_obat' => 'Toko Sumber Makmur',
            'harga' => 'Rp 88.000',
            'jumlah' => 5,
            'tanggal_beli' => '2026-10-19',
        ])->assertRedirect();

        $this->assertDatabaseHas('financial_transactions', [
            'tipe' => 'pengeluaran',
            'kategori' => 'Pembelian Obat/Pupuk',
            'judul' => 'Pembelian Gramoxone 276 SL',
            'nominal' => 440000,
            'tanggal' => '2026-10-19 00:00:00',
        ]);

        $this->assertDatabaseHas('financial_categories', [
            'parent_id' => null,
            'nama' => 'Pembelian Obat/Pupuk',
            'tipe' => 'pengeluaran',
        ]);

        $expense = FinancialTransaction::latest('id')->first();
        $this->assertStringContainsString('Toko Sumber Makmur', (string) $expense->keterangan);
    }

    public function test_purchase_recorded_from_medicine_form_creates_expense_transaction(): void
    {
        $this->post('/data-obat', [
            'nama' => 'Antracol Baru 70 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Semua Sayuran',
            'purchases' => [
                [
                    'toko_obat' => 'Toko Barokah Tani',
                    'harga' => 65000,
                    'jumlah' => 2,
                    'tanggal_beli' => '2026-10-20',
                ],
            ],
        ])->assertRedirect('/data-obat');

        $this->assertDatabaseHas('financial_transactions', [
            'tipe' => 'pengeluaran',
            'kategori' => 'Pembelian Obat/Pupuk',
            'judul' => 'Pembelian Antracol Baru 70 WP',
            'nominal' => 130000,
            'tanggal' => '2026-10-20 00:00:00',
        ]);
    }

    public function test_editing_existing_purchase_history_does_not_duplicate_expense(): void
    {
        $medicine = Medicine::create([
            'nama' => 'Dithane M-45',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 85000,
            'stok' => 3,
        ]);

        $this->post("/data-obat/{$medicine->id}/pembelian", [
            'toko_obat' => 'Toko Tani Makmur',
            'harga' => 85000,
            'jumlah' => 1,
            'tanggal_beli' => '2026-10-19',
        ])->assertRedirect();

        $purchase = $medicine->purchases()->first();
        $this->assertNotNull($purchase);
        $this->assertEquals(1, FinancialTransaction::count());

        $this->put("/data-obat/{$medicine->id}", [
            'nama' => 'Dithane M-45',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 90000,
            'purchases' => [
                [
                    'id' => $purchase->id,
                    'toko_obat' => 'Toko Tani Makmur',
                    'harga' => 90000,
                    'tanggal_beli' => '2026-10-20',
                ],
            ],
        ])->assertRedirect('/data-obat');

        $this->assertEquals(1, FinancialTransaction::count());
        $this->assertDatabaseHas('financial_transactions', [
            'kategori' => 'Pembelian Obat/Pupuk',
            'nominal' => 85000,
        ]);
    }

    public function test_stok_label_and_stok_volume_label_formatting(): void
    {
        $solidMedicine = Medicine::create([
            'nama' => 'Pupuk Organik Super',
            'jenis' => 'Pupuk',
            'tanaman_sasaran' => 'Semua Tanaman',
            'kemasan_jumlah' => 2,
            'kemasan_satuan' => 'kg',
            'stok' => 32,
            'stok_volume' => 64000,
        ]);

        $this->assertEquals('32 kemasan', $solidMedicine->stok_label);
        $this->assertEquals('64 kg (64.000 gram)', $solidMedicine->stok_volume_label);

        $liquidMedicine = Medicine::create([
            'nama' => 'Pestisida Cair',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai',
            'kemasan_jumlah' => 1,
            'kemasan_satuan' => 'liter',
            'stok' => 5,
            'stok_volume' => 5000,
        ]);

        $this->assertEquals('5 botol', $liquidMedicine->stok_label);
        $this->assertEquals('5 liter (5.000 ml)', $liquidMedicine->stok_volume_label);
    }

    public function test_record_purchase_with_foto_nota_uploads_and_stores_correctly(): void
    {
        Storage::fake('public');

        $medicine = Medicine::create([
            'nama' => 'Score 250 EC Nota Test',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Padi, Bawang',
            'harga' => 95000,
            'stok' => 2,
        ]);

        $file = UploadedFile::fake()->image('nota_pembelian.jpg', 600, 600);

        $response = $this->post("/data-obat/{$medicine->id}/pembelian", [
            'toko_obat' => 'Toko Tani Berkah',
            'harga' => 95000,
            'jumlah' => 2,
            'tanggal_beli' => '2026-10-22',
            'foto_nota' => $file,
        ]);

        $response->assertRedirect();

        $purchase = $medicine->purchases()->first();
        $this->assertNotNull($purchase);
        $this->assertNotNull($purchase->foto_nota);
        $this->assertNotNull($purchase->foto_url);
        Storage::disk('public')->assertExists($purchase->foto_nota);

        $medicine->refresh();
        $this->assertContains($purchase->foto_nota, $medicine->foto_paths);
        $this->assertContains($purchase->foto_url, $medicine->foto_urls);

        // Keuangan transaction also gets foto_nota
        $this->assertDatabaseHas('financial_transactions', [
            'kategori' => 'Pembelian Obat/Pupuk',
            'foto_nota' => $purchase->foto_nota,
        ]);
    }

    public function test_general_purchase_with_foto_nota_uploads_and_merges_photos(): void
    {
        Storage::fake('public');

        // 1. Existing medicine
        $medicine = Medicine::create([
            'nama' => 'Dithane M-45 Nota Test',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Cabai',
            'harga' => 80000,
            'stok' => 1,
        ]);

        $file = UploadedFile::fake()->image('nota_dithane.jpg', 600, 600);

        $response = $this->post('/stok-obat/pembelian', [
            'medicine_id' => $medicine->id,
            'toko_obat' => 'Toko Subur',
            'harga' => 85000,
            'jumlah' => 3,
            'tanggal_beli' => '2026-10-22',
            'foto_nota' => $file,
        ]);

        $response->assertRedirect();

        $purchase = $medicine->purchases()->first();
        $this->assertNotNull($purchase);
        $this->assertNotNull($purchase->foto_nota);
        Storage::disk('public')->assertExists($purchase->foto_nota);

        $medicine->refresh();
        $this->assertContains($purchase->foto_url, $medicine->foto_urls);

        // 2. New medicine
        $fileNew = UploadedFile::fake()->image('nota_antracol_baru.jpg', 600, 600);

        $responseNew = $this->post('/stok-obat/pembelian', [
            'nama' => 'Antracol 70 WP Baru Nota Test',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat',
            'toko_obat' => 'Toko Tani Jaya',
            'harga' => 70000,
            'jumlah' => 2,
            'tanggal_beli' => '2026-10-22',
            'foto_nota' => $fileNew,
        ]);

        $responseNew->assertRedirect();

        $newMed = Medicine::where('nama', 'Antracol 70 WP Baru Nota Test')->first();
        $this->assertNotNull($newMed);
        $this->assertNotEmpty($newMed->foto_paths);
        $this->assertNotEmpty($newMed->foto_urls);
        $newPurchase = $newMed->purchases()->first();
        $this->assertNotNull($newPurchase);
        $this->assertNotNull($newPurchase->foto_nota);
        Storage::disk('public')->assertExists($newPurchase->foto_nota);
    }
}
