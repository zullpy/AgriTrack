<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BottomNavTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_bottom_nav_renders_on_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Obat');
        $response->assertSee('Tahapan');
        $response->assertSee('Informasi Penting');
        $response->assertSee('Tanaman Aktif & Schedule');
        $response->assertSee('Keuangan');
        $response->assertSee('/dashboard');
        $response->assertSee('/data-obat');
        $response->assertSee('/steps');
        $response->assertSee('/steps/catatan');
        $response->assertSee('/tanaman-aktif');
        $response->assertSee('/keuangan');
    }

    public function test_mobile_bottom_nav_renders_on_tanaman_aktif(): void
    {
        $response = $this->get('/tanaman-aktif');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Obat');
        $response->assertSee('Tahapan');
        $response->assertSee('Informasi Penting');
        $response->assertSee('Tanaman Aktif & Schedule');
        $response->assertSee('Keuangan');
        $response->assertSee('/steps/catatan');
        $response->assertSee('/tanaman-aktif');
    }
}

