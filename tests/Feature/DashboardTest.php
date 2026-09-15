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
}
