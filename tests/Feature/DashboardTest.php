<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\PlantCatalog;
use Carbon\Carbon;
use Database\Seeders\PlantCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_url_redirects_to_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
    }

    public function test_dashboard_page_loads_successfully(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Katalog Tanaman');
    }

    public function test_dashboard_displays_three_plant_cards(): void
    {
        $this->seed(PlantCatalogSeeder::class);

        Crop::create(['nama_tanaman' => 'Timun', 'varietas' => 'Lokal', 'tanggal_tanam' => now(), 'status' => 'Sedang Ditanam']);
        Crop::create(['nama_tanaman' => 'Cabe', 'varietas' => 'Rawit', 'tanggal_tanam' => now(), 'status' => 'Sedang Ditanam']);
        Crop::create(['nama_tanaman' => 'Jagung', 'varietas' => 'Manis', 'tanggal_tanam' => now(), 'status' => 'Sedang Ditanam']);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);

        // 3 plant cards: Timun, Cabe, Jagung
        $response->assertSee('Timun');
        $response->assertSee('Cabe');
        $response->assertSee('Jagung');

        // Plant emojis
        $response->assertSee('🥒');
        $response->assertSee('🌶️');
        $response->assertSee('🌽');
    }

    public function test_dashboard_contains_two_menus_for_plants(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);

        // Assert 2 menus are present in modal
        $response->assertSee('1. Kalender HST');
        $response->assertSee('2. Panduan Pemupukan');
        $response->assertSee('/kalender-hst');
    }

    public function test_dashboard_renders_active_crop_badge_on_matching_card(): void
    {
        Crop::create([
            'nama_tanaman' => 'Cabai Rawit Merah',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => Carbon::now()->subDays(25)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('25 HST');
    }

    public function test_plant_catalog_edit_cancel_button_points_to_dashboard(): void
    {
        $this->seed(PlantCatalogSeeder::class);
        $catalog = PlantCatalog::first();

        $response = $this->get(route('tanaman-katalog.edit', $catalog));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee('Batal');
    }

    public function test_plant_catalog_index_redirects_to_dashboard(): void
    {
        $response = $this->get(route('tanaman-katalog.index'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_uses_crop_emoji_when_crop_not_in_plant_catalogs(): void
    {
        Crop::create([
            'nama_tanaman' => 'Tomat',
            'varietas' => 'Servo F1',
            'tanggal_tanam' => Carbon::now()->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Tomat');
        $response->assertSee('🍅');
    }

    public function test_plant_catalog_create_page_loads_with_prefilled_name(): void
    {
        $response = $this->get(route('tanaman-katalog.create', ['name' => 'Tomat']));

        $response->assertStatus(200);
        $response->assertSee('Tambah Panduan Tanaman');
        $response->assertSee('value="Tomat"', false);
        $response->assertSee('value="🍅"', false);
        $response->assertDontSee('value="PUT"', false);
    }

    public function test_plant_catalog_store_creates_catalog_and_guides(): void
    {
        $response = $this->post(route('tanaman-katalog.store'), [
            'name' => 'Tomat',
            'key' => 'tomat',
            'emoji' => '🍅',
            'cycle' => '60 – 80 HST',
            'theme' => 'rose',
            'urutan' => 10,
            'aktif' => '1',
            'guides' => [
                [
                    'phase' => 'Pemupukan Awal',
                    'hst' => '0 HST',
                    'focus' => 'Pondasi akar',
                    'nutrients' => "Kompos\nNPK 16-16-16",
                    'dosis' => '10 gram per lubang',
                    'metode' => 'Campur tanah lubang tanam',
                    'tips' => 'Jaga kelembaban tanah',
                ],
            ],
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('plant_catalogs', ['key' => 'tomat', 'name' => 'Tomat']);
        $this->assertDatabaseHas('plant_catalog_guides', ['phase' => 'Pemupukan Awal']);
    }

    public function test_plant_catalog_can_be_deleted(): void
    {
        $catalog = PlantCatalog::create([
            'name' => 'Semangka',
            'key' => 'semangka',
            'emoji' => '🍉',
            'cycle' => '65 – 75 HST',
            'theme' => 'rose',
            'urutan' => 5,
            'aktif' => true,
        ]);

        $response = $this->delete(route('tanaman-katalog.destroy', $catalog));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseMissing('plant_catalogs', ['id' => $catalog->id]);
    }

    public function test_ended_or_harvested_crops_do_not_appear_on_dashboard(): void
    {
        Crop::create([
            'nama_tanaman' => 'Tomat Lawas',
            'varietas' => 'Servo',
            'tanggal_tanam' => Carbon::now()->subDays(60)->toDateString(),
            'status' => 'Diakhiri',
        ]);

        Crop::create([
            'nama_tanaman' => 'Cabai Panen',
            'varietas' => 'Rawit',
            'tanggal_tanam' => Carbon::now()->subDays(80)->toDateString(),
            'status' => 'Sudah Dipanen',
        ]);

        Crop::create([
            'nama_tanaman' => 'Melon Aktif',
            'varietas' => 'Action 434',
            'tanggal_tanam' => Carbon::now()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Melon Aktif');
        $response->assertDontSee('Tomat Lawas');
        $response->assertDontSee('Cabai Panen');
    }

    public function test_dashboard_displays_empty_state_when_no_active_crops(): void
    {
        Crop::create([
            'nama_tanaman' => 'Bawang Merah Selesai',
            'varietas' => 'Tajuk',
            'tanggal_tanam' => Carbon::now()->subDays(70)->toDateString(),
            'status' => 'Diakhiri',
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Tanaman Aktif');
        $response->assertSee('Mulai Tanam di Kalender HST');
        $response->assertDontSee('Bawang Merah Selesai');
    }
}
