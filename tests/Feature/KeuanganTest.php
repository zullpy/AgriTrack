<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\CropHarvest;
use App\Models\Medicine;
use App\Models\MedicinePurchase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_keuangan_page_when_empty(): void
    {
        $response = $this->get('/keuangan');

        $response->assertStatus(200);
        $response->assertSee('Keuangan Pertanian');
        $response->assertSee('Total Pemasukan');
        $response->assertSee('Total Pengeluaran');
        $response->assertSee('Laba / Saldo Bersih');
        $response->assertSee('Belum Ada Catatan Transaksi');
    }

    public function test_keuangan_calculates_income_and_expenses_correctly(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Tomat Servo',
            'varietas' => 'F1',
            'tanggal_tanam' => Carbon::now()->subDays(60),
            'status' => 'Sedang Ditanam',
        ]);

        CropHarvest::create([
            'crop_id' => $crop->id,
            'panen_ke' => 1,
            'tanggal_panen' => Carbon::now()->subDays(2),
            'total_panen' => '50 kg',
            'harga_panen' => 'Rp 10.000 / kg',
            'total_harga_kotor' => '500000',
            'catatan' => 'Panen perdana segar',
        ]);

        $medicine = Medicine::create([
            'nama' => 'Antracol 70 WP',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat',
        ]);

        MedicinePurchase::create([
            'medicine_id' => $medicine->id,
            'toko_obat' => 'Toko Tani Makmur',
            'harga' => 85000,
            'tanggal_beli' => Carbon::now()->subDays(5),
            'catatan' => 'Beli 1 bungkus 250gr',
        ]);

        $response = $this->get('/keuangan');

        $response->assertStatus(200);
        $response->assertSee('Tomat Servo');
        $response->assertSee('Antracol 70 WP');
        $response->assertSee('Rp 500.000');
        $response->assertSee('Rp 85.000');
        $response->assertSee('Rp 415.000');
        $response->assertSee('Surplus / Untung');
    }

    public function test_keuangan_supports_periode_filters(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit',
            'tanggal_tanam' => Carbon::now()->subMonths(6),
            'status' => 'Sedang Ditanam',
        ]);

        // Harvest this month
        CropHarvest::create([
            'crop_id' => $crop->id,
            'panen_ke' => 1,
            'tanggal_panen' => Carbon::now()->startOfMonth(),
            'total_panen' => '20 kg',
            'harga_panen' => 'Rp 30.000 / kg',
            'total_harga_kotor' => '600000',
        ]);

        // Harvest last year
        CropHarvest::create([
            'crop_id' => $crop->id,
            'panen_ke' => 2,
            'tanggal_panen' => Carbon::now()->subYears(2),
            'total_panen' => '10 kg',
            'harga_panen' => 'Rp 20.000 / kg',
            'total_harga_kotor' => '200000',
        ]);

        // Filter bulan_ini should only have this month's harvest
        $responseMonth = $this->get('/keuangan?periode=bulan_ini');
        $responseMonth->assertStatus(200);
        $responseMonth->assertSee('Rp 600.000');
        $responseMonth->assertDontSee('Rp 200.000');

        // Filter tahun_ini should contain this year's harvest
        $responseYear = $this->get('/keuangan?periode=tahun_ini');
        $responseYear->assertStatus(200);
        $responseYear->assertSee('Rp 600.000');
        $responseYear->assertDontSee('Rp 200.000');
    }
}
