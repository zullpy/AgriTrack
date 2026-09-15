<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_root_redirects_to_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
    }

    public function test_data_obat_page_loads_successfully(): void
    {
        $response = $this->get('/data-obat');

        $response->assertStatus(200);
        $response->assertDontSee('Stok');
    }

    public function test_can_create_and_update_medicine_without_stok(): void
    {
        $storeResponse = $this->post('/data-obat', [
            'nama' => 'Insektisida Organik',
            'jenis' => 'Insektisida',
            'tanaman_sasaran' => 'Cabai',
            'dosis_anjuran' => '5 ml / liter',
            'interval_aplikasi' => '7 hari',
            'catatan_keamanan' => 'Aman untuk lingkungan',
        ]);

        $storeResponse->assertRedirect('/data-obat');
        $this->assertDatabaseHas('medicines', [
            'nama' => 'Insektisida Organik',
            'jenis' => 'Insektisida',
        ]);
    }
}
