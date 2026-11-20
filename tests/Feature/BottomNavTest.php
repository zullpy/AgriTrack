<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BottomNavTest extends TestCase
{
    use RefreshDatabase;

    public function test_bottom_nav_component_renders_on_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Navigasi Utama Bawah');
        $response->assertSee('Dashboard');
        $response->assertSee('Obat');
        $response->assertSee('Tanaman');
        $response->assertSee('Keuangan');
        $response->assertSee('Buka Menu Aksi Cepat');
        $response->assertSee('Aksi Cepat');
    }

    public function test_bottom_nav_component_renders_on_tanaman_aktif(): void
    {
        $response = $this->get('/tanaman-aktif');

        $response->assertStatus(200);
        $response->assertSee('Navigasi Utama Bawah');
        $response->assertSee('bg-[#111317]', false);
        $response->assertSee('bg-[#2F6BFF]', false);
    }

    public function test_bottom_nav_standalone_blade_component_renders_properly(): void
    {
        $view = $this->blade('<x-bottom-nav />');

        $view->assertSee('Navigasi Utama Bawah');
        $view->assertSee('Dashboard');
        $view->assertSee('Obat');
        $view->assertSee('Tanaman');
        $view->assertSee('Keuangan');
        $view->assertSee('Aksi Cepat');
    }
}
