<?php

namespace Tests\Feature;

use App\Models\PlantCatalog;
use App\Models\PlantCatalogHama;
use Database\Seeders\PlantCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlantCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_katalog_tanaman_menu_page_renders_with_seeded_plants(): void
    {
        $this->seed(PlantCatalogSeeder::class);

        $response = $this->get(route('tanaman-katalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Katalog Tanaman');
        $response->assertSee('Tambah Tanaman');
        $response->assertSee('Timun');
        $response->assertSee('Cabe');
        $response->assertSee('Jagung');
        $response->assertSee('Tomat');
    }

    public function test_katalog_tanaman_show_page_displays_only_specific_plant_stages(): void
    {
        $timun = PlantCatalog::create([
            'key' => 'timun',
            'name' => 'Timun',
            'varietas' => 'Hercules F1',
            'emoji' => '🥒',
            'cycle' => '35 – 45 Hari',
            'theme' => 'emerald',
            'deskripsi' => 'Tanaman timun merambat segar.',
            'tahap_semai' => 'Langkah semai khusus timun: rendam 2 jam dalam air hangat.',
            'tahap_penanaman' => 'Langkah tanam khusus timun: jarak tanam 40x60 cm.',
            'tahap_pemeliharaan' => 'Langkah rawat khusus timun: rempel tunas ruas 1-5.',
            'tips_penting' => 'Tips khusus timun: petik 2 hari sekali agar produktif.',
            'hama_penyakit' => 'Solusi hama timun: kendalikan kutu kebul dengan Abamektin.',
            'urutan' => 1,
            'aktif' => true,
        ]);

        $cabe = PlantCatalog::create([
            'key' => 'cabe',
            'name' => 'Cabe',
            'varietas' => 'Ori 212',
            'emoji' => '🌶️',
            'cycle' => '80 Hari',
            'theme' => 'rose',
            'deskripsi' => 'Tanaman cabe pedas.',
            'tahap_semai' => 'Langkah semai khusus cabe: peraman 48 jam.',
            'tahap_penanaman' => 'Langkah tanam khusus cabe: jarak tanam 50x60 zig zag.',
            'tahap_pemeliharaan' => 'Langkah rawat khusus cabe: rempel di bawah cabang Y.',
            'tips_penting' => 'Tips khusus cabe: hindari kelebihan urea saat bunga.',
            'hama_penyakit' => 'Solusi hama cabe: atasi antraknosa patek dengan Azoksistrobin.',
            'urutan' => 2,
            'aktif' => true,
        ]);

        $response = $this->get(route('tanaman-katalog.show', $timun));

        $response->assertStatus(200);
        $response->assertSee('Panduan Budidaya Timun');
        $response->assertSee('Langkah semai khusus timun');
        $response->assertSee('Langkah tanam khusus timun');
        $response->assertSee('Langkah rawat khusus timun');
        $response->assertSee('Tips khusus timun');
        $response->assertSee('Solusi hama timun: kendalikan kutu kebul dengan Abamektin.');

        // Pastikan konten cabe TIDAK bocor ke halaman timun
        $response->assertDontSee('Langkah semai khusus cabe');
        $response->assertDontSee('Langkah tanam khusus cabe');
        $response->assertDontSee('Langkah rawat khusus cabe');
        $response->assertDontSee('Tips khusus cabe');
        $response->assertDontSee('Solusi hama cabe: atasi antraknosa patek dengan Azoksistrobin.');

        // Pastikan tombol Semua dan emoji tab/badge telah dihapus
        $response->assertDontSee('id="tab-btn-all"', false);
        $response->assertDontSee('Semua (5 Menu)');
        $response->assertDontSee('⏱️');
        $response->assertDontSee('🌱 1. Tahap Semai');
        $response->assertSee('1. Tahap Semai');
        $response->assertSee('2. Tahap Penanaman');
        $response->assertSee('3. Pemeliharaan');
        $response->assertSee('4. Tips &amp; Catatan', false);
        $response->assertSee('5. Problem &amp; Solusi Hama', false);

        // Akses via key/slug juga berhasil (tidak 404)
        $responseSlug = $this->get('/tanaman-katalog/timun');
        $responseSlug->assertStatus(200);
        $responseSlug->assertSee('Panduan Budidaya Timun');
    }

    public function test_can_create_new_plant_with_specific_stages(): void
    {
        $response = $this->post(route('tanaman-katalog.store'), [
            'name' => 'Semangka Inul',
            'varietas' => 'Inul Merah F1',
            'emoji' => '🍉',
            'cycle' => '60 Hari',
            'theme' => 'rose',
            'deskripsi' => 'Semangka lonjong berdaging manis renyah.',
            'tahap_semai' => 'Semai benih pada tray semai dengan suhu hangat selama 5 hari.',
            'tahap_penanaman' => 'Pindah tanam sore hari dengan jarak 80 cm x 2 m.',
            'tahap_pemeliharaan' => 'Pilih 1 atau 2 bakal buah terbaik per pohon.',
            'tips_penting' => 'Alasi buah semangka dengan jerami kering agar tidak busuk tanah.',
            'hama_penyakit' => 'Kendalikan lalat buah semangka dengan perangkap metil eugenol.',
            'redirect_to' => 'katalog',
        ]);

        $created = PlantCatalog::where('name', 'Semangka Inul')->first();
        $this->assertNotNull($created);
        $this->assertEquals('semangka_inul', $created->key);
        $this->assertEquals('Inul Merah F1', $created->varietas);
        $this->assertEquals('🍉', $created->emoji);
        $this->assertEquals('Semai benih pada tray semai dengan suhu hangat selama 5 hari.', $created->tahap_semai);
        $this->assertEquals('Pindah tanam sore hari dengan jarak 80 cm x 2 m.', $created->tahap_penanaman);
        $this->assertEquals('Pilih 1 atau 2 bakal buah terbaik per pohon.', $created->tahap_pemeliharaan);
        $this->assertEquals('Alasi buah semangka dengan jerami kering agar tidak busuk tanah.', $created->tips_penting);
        $this->assertEquals('Kendalikan lalat buah semangka dengan perangkap metil eugenol.', $created->hama_penyakit);

        $response->assertRedirect(route('tanaman-katalog.show', $created));
    }

    public function test_can_update_plant_stages(): void
    {
        $plant = PlantCatalog::create([
            'key' => 'melon',
            'name' => 'Melon',
            'emoji' => '🍈',
            'cycle' => '65 Hari',
            'theme' => 'lime',
            'urutan' => 10,
            'aktif' => true,
        ]);

        $response = $this->put(route('tanaman-katalog.update', $plant), [
            'key' => 'melon',
            'name' => 'Melon Madu',
            'varietas' => 'Action 434',
            'emoji' => '🍈',
            'cycle' => '70 Hari',
            'theme' => 'lime',
            'tahap_semai' => 'Rendam air hangat 3 jam',
            'tahap_penanaman' => 'Tanam jarak 50 cm',
            'tahap_pemeliharaan' => 'Gantung buah saat seukuran kepalan tangan',
            'tips_penting' => 'Stop pengairan 5 hari sebelum panen untuk tingkatkan brix manis',
            'redirect_to' => 'katalog',
        ]);

        $plant->refresh();
        $this->assertEquals('Melon Madu', $plant->name);
        $this->assertEquals('Action 434', $plant->varietas);
        $this->assertEquals('Rendam air hangat 3 jam', $plant->tahap_semai);
        $this->assertEquals('Stop pengairan 5 hari sebelum panen untuk tingkatkan brix manis', $plant->tips_penting);

        $response->assertRedirect(route('tanaman-katalog.show', $plant));
    }

    public function test_can_delete_plant_from_catalog(): void
    {
        $plant = PlantCatalog::create([
            'key' => 'terong',
            'name' => 'Terong Ungu',
            'emoji' => '🍆',
            'cycle' => '50 Hari',
            'theme' => 'teal',
            'urutan' => 15,
            'aktif' => true,
        ]);

        $response = $this->delete(route('tanaman-katalog.destroy', $plant), [
            'redirect_to' => 'index',
        ]);

        $response->assertRedirect(route('tanaman-katalog.index'));
        $this->assertDatabaseMissing('plant_catalogs', ['id' => $plant->id]);
    }

    public function test_search_filter_in_catalog(): void
    {
        PlantCatalog::create([
            'key' => 'timun',
            'name' => 'Timun',
            'emoji' => '🥒',
            'cycle' => '35 Hari',
            'theme' => 'emerald',
            'urutan' => 1,
            'aktif' => true,
        ]);

        PlantCatalog::create([
            'key' => 'cabe',
            'name' => 'Cabe Rawit',
            'emoji' => '🌶️',
            'cycle' => '80 Hari',
            'theme' => 'rose',
            'urutan' => 2,
            'aktif' => true,
        ]);

        $response = $this->get(route('tanaman-katalog.index', ['q' => 'Timun']));

        $response->assertStatus(200);
        $response->assertSee('Timun');
        $response->assertDontSee('Cabe Rawit');
    }

    public function test_user_can_update_stage_content_via_ajax(): void
    {
        $plant = PlantCatalog::create([
            'key' => 'timun',
            'name' => 'Timun',
            'emoji' => '🥒',
            'cycle' => '35 – 45 Hari',
            'theme' => 'emerald',
            'tahap_semai' => 'Semai lama',
            'tahap_penanaman' => 'Tanam lama',
            'tahap_pemeliharaan' => 'Rawat lama',
            'tips_penting' => 'Tips lama',
            'urutan' => 1,
            'aktif' => true,
        ]);

        $response = $this->postJson(route('tanaman-katalog.update-stage'), [
            'catalog_id' => $plant->id,
            'stage_type' => 'semai',
            'content' => 'Langkah semai timun terbaru: rendam 3 jam air hangat.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'catalog_id' => $plant->id,
            'stage_type' => 'semai',
            'content' => 'Langkah semai timun terbaru: rendam 3 jam air hangat.',
        ]);

        $this->assertEquals(
            'Langkah semai timun terbaru: rendam 3 jam air hangat.',
            $plant->fresh()->tahap_semai
        );
    }

    public function test_user_can_update_hama_penyakit_via_ajax(): void
    {
        $plant = PlantCatalog::create([
            'key' => 'timun',
            'name' => 'Timun',
            'emoji' => '🥒',
            'cycle' => '35 – 45 Hari',
            'theme' => 'emerald',
            'hama_penyakit' => 'Data hama lama',
            'urutan' => 1,
            'aktif' => true,
        ]);

        $response = $this->postJson(route('tanaman-katalog.update-stage'), [
            'catalog_id' => $plant->id,
            'stage_type' => 'hama',
            'content' => 'Hama trips dan kutu kebul: semprot abamektin 0.5 ml/l selang 3 hari.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'catalog_id' => $plant->id,
            'stage_type' => 'hama',
            'content' => 'Hama trips dan kutu kebul: semprot abamektin 0.5 ml/l selang 3 hari.',
            'hama_penyakit' => 'Hama trips dan kutu kebul: semprot abamektin 0.5 ml/l selang 3 hari.',
        ]);

        $this->assertEquals(
            'Hama trips dan kutu kebul: semprot abamektin 0.5 ml/l selang 3 hari.',
            $plant->fresh()->hama_penyakit
        );
    }

    public function test_user_can_update_stage_content_for_crop_without_existing_catalog(): void
    {
        $response = $this->postJson(route('tanaman-katalog.update-stage'), [
            'plant_name' => 'Tanaman Baru Unik',
            'emoji' => '🥦',
            'stage_type' => 'pemeliharaan',
            'content' => 'Panduan pemeliharaan untuk tanaman baru.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'stage_type' => 'pemeliharaan',
            'content' => 'Panduan pemeliharaan untuk tanaman baru.',
        ]);

        $catalog = PlantCatalog::where('name', 'Tanaman Baru Unik')->first();
        $this->assertNotNull($catalog);
        $this->assertEquals('Panduan pemeliharaan untuk tanaman baru.', $catalog->tahap_pemeliharaan);
    }

    public function test_update_stage_validates_stage_type(): void
    {
        $response = $this->postJson(route('tanaman-katalog.update-stage'), [
            'plant_name' => 'Timun',
            'stage_type' => 'invalid_stage',
            'content' => 'Test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['stage_type']);
    }

    public function test_catalog_parses_stage_and_note_content_into_structured_cards(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'buncis',
            'name' => 'Buncis',
            'varietas' => 'Perancis Super',
            'emoji' => '🫛',
            'tahap_semai' => "1. Perendaman Benih:\n• Rendam benih 2-3 jam dalam air hangat.\n\n2. Media Semai:\n• Gunakan tray semai berpori.",
            'tahap_penanaman' => "1. Jarak Tanam:\n• Buat jarak 30x40 cm.\n\n2. Penanaman:\n• Masukkan 1 bibit per lubang.",
            'tahap_pemeliharaan' => "1. Pasang Ajir:\n• Pasang ajir setinggi 1.8 meter.",
            'tips_penting' => '• Rutin panen 2 hari sekali agar polong renyah dan tidak berserat.',
            'hama_penyakit' => "1. Hama Penggerek Polong:\n• Semprot insektisida sistemik saat bunga mekar.",
            'urutan' => 10,
            'aktif' => true,
        ]);

        // Test structured steps for semai
        $semaiSteps = $catalog->structured_semai;
        $this->assertCount(2, $semaiSteps);
        $this->assertEquals('1', $semaiSteps[0]['nomor']);
        $this->assertEquals('Perendaman Benih', $semaiSteps[0]['judul']);
        $this->assertStringContainsString('Rendam benih 2-3 jam', $semaiSteps[0]['deskripsi']);

        // Test structured steps for penanaman
        $tanamSteps = $catalog->structured_penanaman;
        $this->assertCount(2, $tanamSteps);
        $this->assertEquals('Jarak Tanam', $tanamSteps[0]['judul']);

        // Test structured tips
        $tipsCards = $catalog->structured_tips;
        $this->assertNotEmpty($tipsCards);
        $this->assertStringContainsString('Rutin panen', $tipsCards[0]['content']);

        // Test structured hama
        $hamaCards = $catalog->structured_hama;
        $this->assertNotEmpty($hamaCards);
        $this->assertEquals('Hama Penggerek Polong', $hamaCards[0]['title']);

        // Test rendering on show page
        $response = $this->get(route('tanaman-katalog.show', $catalog));
        $response->assertStatus(200);
        $response->assertSee('Perendaman Benih');
        $response->assertSee('Media Semai');
        $response->assertSee('Jarak Tanam');
        $response->assertSee('Pasang Ajir');
        $response->assertSee('Hama Penggerek Polong');
    }

    public function test_can_store_hama_problem_and_solution_with_photos(): void
    {
        Storage::fake('public');

        $catalog = PlantCatalog::create([
            'key' => 'cabai_rawit',
            'name' => 'Cabai Rawit',
            'varietas' => 'Ori 212',
            'emoji' => '🌶️',
            'aktif' => true,
        ]);

        $photo1 = UploadedFile::fake()->image('ulat_grayak.jpg', 800, 600);
        $photo2 = UploadedFile::fake()->image('kamera_ulat.jpg', 800, 600);

        $response = $this->post(route('tanaman-katalog.hama.store', $catalog), [
            'judul' => 'Ulat Grayak & Penggerek Daun',
            'kategori' => 'Hama Utama',
            'gejala' => "Daun berlubang hingga menyisakan tulang daun.\nBanyak kotoran ulat di sekitar pucuk.",
            'solusi' => "Semprot insektisida bahan aktif Emamektin benzoat atau Klorantraniliprol pada sore hari.\nJaga kebersihan lahan.",
            'foto' => [$photo1],
            'foto_kamera' => [$photo2],
        ]);

        $response->assertRedirect(route('tanaman-katalog.show', [$catalog, 'tab' => 'hama']));

        $this->assertDatabaseHas('plant_catalog_hamas', [
            'plant_catalog_id' => $catalog->id,
            'judul' => 'Ulat Grayak & Penggerek Daun',
            'kategori' => 'Hama Utama',
        ]);

        $hama = PlantCatalogHama::where('plant_catalog_id', $catalog->id)->first();
        $this->assertNotNull($hama);
        $this->assertEquals("Daun berlubang hingga menyisakan tulang daun.\nBanyak kotoran ulat di sekitar pucuk.", $hama->gejala);
        $this->assertEquals(2, $hama->foto_count);
        $this->assertCount(2, $hama->foto_urls);
    }

    public function test_can_update_hama_problem_and_solution(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'tomat_servo',
            'name' => 'Tomat Servo',
            'varietas' => 'Servo F1',
            'emoji' => '🍅',
            'aktif' => true,
        ]);

        $hama = PlantCatalogHama::create([
            'plant_catalog_id' => $catalog->id,
            'judul' => 'Busuk Daun Phytophthora',
            'kategori' => 'Penyakit Utama',
            'gejala' => 'Bercak basah coklat kehitaman pada daun.',
            'solusi' => 'Semprot fungisida Mankozeb.',
            'urutan' => 1,
        ]);

        $response = $this->put(route('tanaman-katalog.hama.update', $hama), [
            'judul' => 'Busuk Daun & Batang Phytophthora Infestans',
            'kategori' => 'Penyakit Utama',
            'gejala' => 'Bercak basah coklat kehitaman meluas cepat saat musim hujan.',
            'solusi' => 'Semprot fungisida sistemik Simoksanil + Mankozeb selang 3 hari sekali.',
        ]);

        $response->assertRedirect(route('tanaman-katalog.show', [$catalog, 'tab' => 'hama']));

        $hama->refresh();
        $this->assertEquals('Busuk Daun & Batang Phytophthora Infestans', $hama->judul);
        $this->assertEquals('Bercak basah coklat kehitaman meluas cepat saat musim hujan.', $hama->gejala);
        $this->assertEquals('Semprot fungisida sistemik Simoksanil + Mankozeb selang 3 hari sekali.', $hama->solusi);
    }

    public function test_can_destroy_hama_problem_and_solution(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'jagung_manis',
            'name' => 'Jagung Manis',
            'varietas' => 'Talenta',
            'emoji' => '🌽',
            'aktif' => true,
        ]);

        $hama = PlantCatalogHama::create([
            'plant_catalog_id' => $catalog->id,
            'judul' => 'Bulai Jagung (Peronosclerospora)',
            'kategori' => 'Penyakit Utama',
            'gejala' => 'Daun bergaris klorotik putih kekuningan memanjang sejajar tulang daun.',
            'solusi' => 'Perlakuan benih dengan fungisida Metalaksil sebelum tanam.',
            'urutan' => 1,
        ]);

        $response = $this->delete(route('tanaman-katalog.hama.destroy', $hama));
        $response->assertRedirect(route('tanaman-katalog.show', [$catalog, 'tab' => 'hama']));

        $this->assertDatabaseMissing('plant_catalog_hamas', [
            'id' => $hama->id,
        ]);
    }

    public function test_can_upload_and_destroy_photo_hama(): void
    {
        Storage::fake('public');

        $catalog = PlantCatalog::create([
            'key' => 'melon_action',
            'name' => 'Melon Action',
            'varietas' => 'Action 434',
            'emoji' => '🍈',
            'aktif' => true,
        ]);

        $hama = PlantCatalogHama::create([
            'plant_catalog_id' => $catalog->id,
            'judul' => 'Embun Tepung (Powdery Mildew)',
            'kategori' => 'Penyakit Utama',
            'gejala' => 'Lapisan serbuk putih seperti tepung menutupi permukaan daun melon.',
            'solusi' => 'Semprot fungisida Difenokonazol atau Tembaga Hidroksida.',
            'foto' => [],
            'urutan' => 1,
        ]);

        $photo = UploadedFile::fake()->image('powdery_mildew.jpg', 800, 600);

        // Upload photo
        $uploadResponse = $this->post(route('tanaman-katalog.hama.foto.upload', $hama), [
            'foto' => [$photo],
        ]);
        $uploadResponse->assertRedirect(route('tanaman-katalog.show', [$catalog, 'tab' => 'hama']));

        $hama->refresh();
        $this->assertEquals(1, $hama->foto_count);
        $savedUrl = $hama->foto_urls[0];

        // Delete photo
        $deleteResponse = $this->delete(route('tanaman-katalog.hama.foto.destroy', $hama), [
            'photo_url' => $savedUrl,
        ]);
        $deleteResponse->assertRedirect(route('tanaman-katalog.show', [$catalog, 'tab' => 'hama']));

        $hama->refresh();
        $this->assertEquals(0, $hama->foto_count);
    }

    public function test_show_page_renders_structured_hama_with_gejala_and_solusi_boxes(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'terong_ungu',
            'name' => 'Terong Ungu',
            'varietas' => 'Yuvita F1',
            'emoji' => '🍆',
            'aktif' => true,
        ]);

        PlantCatalogHama::create([
            'plant_catalog_id' => $catalog->id,
            'judul' => 'Kepik Renda Terong',
            'kategori' => 'Hama Utama',
            'gejala' => 'Daun tampak bercak klorotik abu-abu dan mengering dari tepi.',
            'solusi' => 'Kendalikan dengan semprotan insektisida nabati mimba atau klorpirifos.',
            'urutan' => 1,
        ]);

        $response = $this->get(route('tanaman-katalog.show', $catalog));

        $response->assertStatus(200);
        $response->assertSee('5. Problem &amp; Solusi Hama', false);
        $response->assertSee('Problem &amp; Solusi Hama Terong Ungu', false);
        $response->assertSee('Kepik Renda Terong');
        $response->assertSee('Gejala Serangan:');
        $response->assertSee('Daun tampak bercak klorotik abu-abu');
        $response->assertSee('Solusi &amp; Pengendalian:', false);
        $response->assertSee('Kendalikan dengan semprotan insektisida nabati mimba');
    }

    public function test_edit_page_renders_stages_per_nomor_tips_per_judul_and_problem_per_judul(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'buncis_lebat',
            'name' => 'Buncis Lebat',
            'emoji' => '🫘',
            'tahap_semai' => "1. Rendam Benih:\nRendam benih selama 3 jam.\n\n2. Siapkan Tray:\nMasukkan media semai ke tray.",
            'tahap_penanaman' => "1. Buat Lubang Tanam:\nKedalaman 4 cm jarak 30x40 cm.",
            'tahap_pemeliharaan' => "1. Pasang Ajir Rambat:\nAjir setinggi 2 meter.",
            'tips_penting' => "1. Panen Pagi Hari:\nPetik saat polong masih renyah sebelum biji menonjol.",
            'hama_penyakit' => "1. Lalat Kacang:\nGejala:\nBintik putih pada daun pertama.\n\nSolusi:\nPerlakuan benih dengan insektisida sistemik.",
            'aktif' => true,
        ]);

        $response = $this->get(route('tanaman-katalog.edit', $catalog));

        $response->assertStatus(200);
        $response->assertSee('2. Tahap Semai &amp; Pembibitan (Per Nomor)', false);
        $response->assertSee('3. Tahap Penanaman &amp; Jarak Tanam (Per Nomor)', false);
        $response->assertSee('4. Tahap Pemeliharaan &amp; Nutrisi (Per Nomor)', false);
        $response->assertSee('5. Tips Penting &amp; Pantangan (Per Judul)', false);
        $response->assertSee('6. Problem &amp; Solusi Hama / Penyakit (Per Judul)', false);

        $response->assertSee('Tambah Nomor Semai');
        $response->assertSee('Tambah Nomor Penanaman');
        $response->assertSee('Tambah Nomor Pemeliharaan');
        $response->assertSee('Tambah Judul Tips');
        $response->assertSee('Tambah Judul Problem');

        $response->assertSee('semai_steps[0][nomor]', false);
        $response->assertSee('Rendam Benih');
        $response->assertSee('penanaman_steps[0][nomor]', false);
        $response->assertSee('Buat Lubang Tanam');
        $response->assertSee('pemeliharaan_steps[0][nomor]', false);
        $response->assertSee('Pasang Ajir Rambat');
        $response->assertSee('tips_items[0][judul]', false);
        $response->assertSee('Panen Pagi Hari');
        $response->assertSee('hama_items[0][judul]', false);
        $response->assertSee('Lalat Kacang');
    }

    public function test_update_catalog_with_structured_steps_tips_and_hama(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'pare_hijau',
            'name' => 'Pare Hijau',
            'emoji' => '🥒',
            'aktif' => true,
        ]);

        $response = $this->put(route('tanaman-katalog.update', $catalog), [
            'key' => 'pare_hijau',
            'name' => 'Pare Hijau Super',
            'emoji' => '🥒',
            'semai_steps' => [
                ['nomor' => '1', 'judul' => 'Perendaman Benih', 'deskripsi' => 'Rendam dalam air hangat 2 jam.'],
                ['nomor' => '2', 'judul' => 'Penyemaian di Polybag', 'deskripsi' => 'Gunakan media tanah dan pupuk kandang 1:1.'],
            ],
            'penanaman_steps' => [
                ['nomor' => '1', 'judul' => 'Transplanting Sore', 'deskripsi' => 'Pindahkan bibit umur 10 HSS pada sore hari.'],
            ],
            'pemeliharaan_steps' => [
                ['nomor' => '1', 'judul' => 'Para-para Bambu', 'deskripsi' => 'Buat rambatan para-para setinggi 2 meter.'],
            ],
            'tips_items' => [
                ['judul' => 'Bungkus Buah', 'content' => 'Bungkus buah muda dengan plastik/kertas agar tidak disengat lalat buah.'],
            ],
            'hama_items' => [
                [
                    'judul' => 'Lalat Buah (Bactrocera)',
                    'kategori' => 'Hama Utama',
                    'gejala' => 'Buah berulat dan membusuk sebelum besar.',
                    'solusi' => 'Pasang perangkap petrogenol metil eugenol dan bungkus buah.',
                ],
            ],
        ]);

        $response->assertRedirect(route('dashboard'));

        $catalog->refresh();
        $this->assertEquals('Pare Hijau Super', $catalog->name);

        // Verify Semai steps per nomor
        $semai = $catalog->structured_semai;
        $this->assertCount(2, $semai);
        $this->assertEquals('1', $semai[0]['nomor']);
        $this->assertEquals('Perendaman Benih', $semai[0]['judul']);
        $this->assertEquals('2', $semai[1]['nomor']);
        $this->assertEquals('Penyemaian di Polybag', $semai[1]['judul']);

        // Verify Penanaman step per nomor
        $penanaman = $catalog->structured_penanaman;
        $this->assertCount(1, $penanaman);
        $this->assertEquals('1', $penanaman[0]['nomor']);
        $this->assertEquals('Transplanting Sore', $penanaman[0]['judul']);

        // Verify Pemeliharaan step per nomor
        $pemeliharaan = $catalog->structured_pemeliharaan;
        $this->assertCount(1, $pemeliharaan);
        $this->assertEquals('1', $pemeliharaan[0]['nomor']);
        $this->assertEquals('Para-para Bambu', $pemeliharaan[0]['judul']);

        // Verify Tips per judul
        $tips = $catalog->structured_tips;
        $this->assertCount(1, $tips);
        $this->assertEquals('Bungkus Buah', $tips[0]['title']);
        $this->assertStringContainsString('Bungkus buah muda', $tips[0]['content']);

        // Verify Problem & Solusi per judul
        $hamas = $catalog->structured_hama;
        $this->assertCount(1, $hamas);
        $this->assertEquals('Lalat Buah (Bactrocera)', $hamas[0]['judul']);
        $this->assertStringContainsString('Buah berulat dan membusuk', $hamas[0]['gejala']);
        $this->assertStringContainsString('Pasang perangkap petrogenol', $hamas[0]['solusi']);

        // Verify synced into PlantCatalogHama model
        $this->assertDatabaseHas('plant_catalog_hamas', [
            'plant_catalog_id' => $catalog->id,
            'judul' => 'Lalat Buah (Bactrocera)',
        ]);
    }

    public function test_save_step_adds_and_updates_individual_step(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'cabai_rawit',
            'name' => 'Cabai Rawit',
            'emoji' => '🌶️',
            'cycle' => '90 Hari',
            'tahap_semai' => "1. Rendam Benih\nWaktu: 2 jam\nRendam benih dengan air hangat kuku.\n\n2. Pemeraman\nWaktu: 24 jam\nPeram benih di kain basah.",
            'urutan' => 1,
            'aktif' => true,
        ]);

        // Add new step to semai (index = null)
        $addResponse = $this->postJson(route('tanaman-katalog.step.save', $catalog), [
            'stage' => 'semai',
            'nomor' => '3',
            'judul' => 'Semai di Tray',
            'waktu' => '7 hari',
            'deskripsi' => 'Pindahkan benih berkecambah ke tray semai.',
        ]);

        $addResponse->assertStatus(200);
        $addResponse->assertJson(['success' => true]);

        $catalog->refresh();
        $steps = $catalog->structured_semai;
        $this->assertCount(3, $steps);
        $this->assertEquals('Semai di Tray', $steps[2]['judul']);
        $this->assertEquals('3', $steps[2]['nomor']);

        // Update step index 1 (Pemeraman -> Peram di Kain Lembab)
        $updateResponse = $this->postJson(route('tanaman-katalog.step.save', $catalog), [
            'stage' => 'semai',
            'index' => 1,
            'nomor' => '2',
            'judul' => 'Peram di Kain Lembab',
            'waktu' => '36 jam',
            'deskripsi' => 'Peram benih di tempat gelap dan lembab.',
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson(['success' => true]);

        $catalog->refresh();
        $steps = $catalog->structured_semai;
        $this->assertCount(3, $steps);
        $this->assertEquals('Peram di Kain Lembab', $steps[1]['judul']);
        $this->assertEquals('36 jam', $steps[1]['waktu']);
    }

    public function test_delete_step_removes_and_renumbers_remaining_steps(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'tomat_ceri',
            'name' => 'Tomat Ceri',
            'emoji' => '🍅',
            'cycle' => '65 Hari',
            'tahap_penanaman' => "1. Buat Lubang Tanam\nBuat lubang tanam dengan jarak 50 cm.\n\n2. Beri Pupuk Dasar\nTaburkan pupuk kompos dan SP-36.\n\n3. Transplanting\nPindahkan bibit sore hari.",
            'urutan' => 1,
            'aktif' => true,
        ]);

        // Delete index 1 (Beri Pupuk Dasar)
        $deleteResponse = $this->deleteJson(route('tanaman-katalog.step.delete', $catalog), [
            'stage' => 'penanaman',
            'index' => 1,
        ]);

        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson(['success' => true]);

        $catalog->refresh();
        $steps = $catalog->structured_penanaman;
        $this->assertCount(2, $steps);
        $this->assertEquals('Buat Lubang Tanam', $steps[0]['judul']);
        $this->assertEquals('1', $steps[0]['nomor']);
        $this->assertEquals('Transplanting', $steps[1]['judul']);
        $this->assertEquals('2', $steps[1]['nomor']); // Renumbered from 3 to 2
    }

    public function test_save_and_delete_tip_per_card(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'bayam_hijau',
            'name' => 'Bayam Hijau',
            'emoji' => '🥬',
            'cycle' => '25 Hari',
            'tips_penting' => "1. Pengairan Rutin:\nSiram 2 kali sehari di pagi dan sore.\n\n2. Panen Tepat Waktu:\nCabut bayam sebelum berbunga.",
            'urutan' => 1,
            'aktif' => true,
        ]);

        // Add a new tip card
        $addResponse = $this->postJson(route('tanaman-katalog.tip.save', $catalog), [
            'title' => 'Cegah Rebah Semai',
            'subtitle' => 'Penyakit Jamur',
            'content' => 'Gunakan media tanam steril dan jangan terlalu basah.',
        ]);

        $addResponse->assertStatus(200);
        $addResponse->assertJson(['success' => true]);

        $catalog->refresh();
        $tips = $catalog->structured_tips;
        $this->assertCount(3, $tips);
        $this->assertEquals('Cegah Rebah Semai', $tips[2]['title']);

        // Update tip index 0
        $updateResponse = $this->postJson(route('tanaman-katalog.tip.save', $catalog), [
            'index' => 0,
            'title' => 'Pengairan Pagi & Sore',
            'subtitle' => 'Kebutuhan Air',
            'content' => 'Penyiraman intensif pada minggu pertama.',
        ]);

        $updateResponse->assertStatus(200);
        $catalog->refresh();
        $tips = $catalog->structured_tips;
        $this->assertEquals('Pengairan Pagi & Sore', $tips[0]['title']);

        // Delete tip index 1 (Panen Tepat Waktu)
        $deleteResponse = $this->deleteJson(route('tanaman-katalog.tip.delete', $catalog), [
            'index' => 1,
        ]);

        $deleteResponse->assertStatus(200);
        $catalog->refresh();
        $tips = $catalog->structured_tips;
        $this->assertCount(2, $tips);
        $this->assertEquals('Pengairan Pagi & Sore', $tips[0]['title']);
        $this->assertEquals('Cegah Rebah Semai', $tips[1]['title']);
    }

    public function test_show_page_renders_granular_edit_actions_and_sanitized_hama_data(): void
    {
        $catalog = PlantCatalog::create([
            'key' => 'melon_madu',
            'name' => 'Melon Madu',
            'emoji' => '🍈',
            'cycle' => '70 Hari',
            'tahap_semai' => "1. Peram Benih\nWaktu: 24 jam\nPeram dalam kain.",
            'tips_penting' => "Seleksi Buah\nPelihara 1 buah per tanaman di daun ke 9-13.",
            'urutan' => 1,
            'aktif' => true,
        ]);

        PlantCatalogHama::create([
            'plant_catalog_id' => $catalog->id,
            'judul' => 'Embun Tepung (Powdery Mildew)',
            'kategori' => 'Penyakit Daun',
            'gejala' => 'Bercak putih seperti tepung pada permukaan daun.',
            'solusi' => 'Semprot fungisida berbahan aktif belerang atau tembaga.',
            'urutan' => 1,
        ]);

        $response = $this->get(route('tanaman-katalog.show', $catalog));

        $response->assertStatus(200);

        // Granular step actions
        $response->assertSee('Tambah Nomor Semai');
        $response->assertSee('openEditStepFromBtn');
        $response->assertSee('confirmDeleteStep');

        // Granular tip actions
        $response->assertSee('Tambah Tips Penting');
        $response->assertSee('openEditTipFromBtn');
        $response->assertSee('confirmDeleteTip');

        // Clean Hama data attribute for problem cards
        $response->assertSee('openEditHamaFromBtn(this)');
        $response->assertSee('data-hama=', false);
        $response->assertSee('Embun Tepung (Powdery Mildew)');
    }
}
