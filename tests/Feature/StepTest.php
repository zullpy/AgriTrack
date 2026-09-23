<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StepTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_steps_index(): void
    {
        $response = $this->get('/steps');

        $response->assertStatus(200);
        $response->assertSee('Tahapan Budidaya');
        $response->assertSee('Tahapan Pengolahan Tanah');
        $response->assertSee('Tahapan Penanaman Bibit');
    }

    public function test_can_view_tahapan_pengolahan_tanah(): void
    {
        $response = $this->get('/steps/pengolahan-tanah');

        $response->assertStatus(200);
        $response->assertSee('Tahapan Pengolahan Tanah');
        $response->assertSee('Sanitasi & Pembersihan Lahan', false);
        $response->assertSee('Pembalikan & Penggemburan Tanah', false);
        $response->assertSee('Aplikasi Kapur Dolomit', false);
        $response->assertSee('Aplikasi Pupuk Dasar', false);
        $response->assertSee('Pembuatan Bedengan', false);
        $response->assertSee('Pemasangan Mulsa', false);
    }

    public function test_can_view_tahapan_penanaman_bibit(): void
    {
        $response = $this->get('/steps/penanaman-bibit');

        $response->assertStatus(200);
        $response->assertSee('Tahapan Penanaman Bibit');
        $response->assertSee('Seleksi & Pemilihan Benih', false);
        $response->assertSee('Perlakuan Benih', false);
        $response->assertSee('Persiapan Media Semai', false);
        $response->assertSee('Pemeliharaan Bibit', false);
        $response->assertSee('Aklimatisasi', false);
        $response->assertSee('Transplanting', false);
    }
}
