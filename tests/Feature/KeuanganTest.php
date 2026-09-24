<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\CropHarvest;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
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
        $response->assertSee('Aktivitas Transaksi');
        $response->assertSee('Belum Ada Catatan Transaksi');
    }

    public function test_keuangan_calculates_income_and_expenses_correctly_from_manual_transactions_only(): void
    {
        $crop = Crop::create([
            'nama_tanaman' => 'Tomat Servo',
            'varietas' => 'F1',
            'tanggal_tanam' => Carbon::now()->subDays(60),
            'status' => 'Sedang Ditanam',
        ]);

        // Catat transaksi keuangan manual (pemasukan & pengeluaran)
        FinancialTransaction::create([
            'crop_id' => $crop->id,
            'tipe' => 'pemasukan',
            'kategori' => 'Hasil Panen',
            'judul' => 'Penjualan Tomat Perdana',
            'nominal' => 500000,
            'tanggal' => Carbon::now()->subDays(2),
            'keterangan' => '50 kg @ 10.000',
        ]);

        FinancialTransaction::create([
            'crop_id' => $crop->id,
            'tipe' => 'pengeluaran',
            'kategori' => 'Obat & Pestisida',
            'judul' => 'Beli Antracol 70 WP',
            'nominal' => 85000,
            'tanggal' => Carbon::now()->subDays(5),
            'keterangan' => '1 bungkus 250gr',
        ]);

        // Data di CropHarvest dan MedicinePurchase TIDAK boleh masuk ke keuangan
        CropHarvest::create([
            'crop_id' => $crop->id,
            'panen_ke' => 1,
            'tanggal_panen' => Carbon::now()->subDays(1),
            'total_panen' => '100 kg',
            'harga_panen' => 'Rp 15.000 / kg',
            'total_harga_kotor' => '1500000',
        ]);

        $medicine = Medicine::create([
            'nama' => 'Score 250 EC',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Tomat',
        ]);

        MedicinePurchase::create([
            'medicine_id' => $medicine->id,
            'toko_obat' => 'Toko Tani',
            'harga' => 120000,
            'tanggal_beli' => Carbon::now()->subDays(3),
        ]);

        $response = $this->get('/keuangan');

        $response->assertStatus(200);
        $response->assertSee('Penjualan Tomat Perdana');
        $response->assertSee('Beli Antracol 70 WP');
        $response->assertSee('Rp 500.000');
        $response->assertSee('Rp 85.000');

        // Pastikan panen otomatis dan obat otomatis tidak muncul di keuangan
        $response->assertDontSee('Rp 1.500.000');
        $response->assertDontSee('Score 250 EC');
    }

    public function test_keuangan_supports_periode_filters(): void
    {
        // Transaksi bulan ini
        FinancialTransaction::create([
            'tipe' => 'pemasukan',
            'kategori' => 'Hasil Panen',
            'judul' => 'Penjualan Cabai Bulan Ini',
            'nominal' => 600000,
            'tanggal' => Carbon::now()->startOfMonth(),
        ]);

        // Transaksi 2 tahun lalu
        FinancialTransaction::create([
            'tipe' => 'pemasukan',
            'kategori' => 'Hasil Panen',
            'judul' => 'Penjualan Cabai Tahun Lalu',
            'nominal' => 200000,
            'tanggal' => Carbon::now()->subYears(2),
        ]);

        // Filter bulan_ini should only have this month's transaction
        $responseMonth = $this->get('/keuangan?periode=bulan_ini');
        $responseMonth->assertStatus(200);
        $responseMonth->assertSee('Rp 600.000');
        $responseMonth->assertDontSee('Rp 200.000');

        // Filter tahun_ini should contain this year's transaction
        $responseYear = $this->get('/keuangan?periode=tahun_ini');
        $responseYear->assertStatus(200);
        $responseYear->assertSee('Rp 600.000');
        $responseYear->assertDontSee('Rp 200.000');
    }

    public function test_can_create_manual_financial_transaction(): void
    {
        $response = $this->post('/keuangan', [
            'tipe' => 'pengeluaran',
            'kategori' => 'BBM & Mesin',
            'judul' => 'Beli Solar Pompa Air',
            'nominal' => '75000',
            'tanggal' => Carbon::now()->toDateString(),
            'keterangan' => '5 liter solar untuk pengairan',
        ]);

        $response->assertRedirect('/keuangan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('financial_transactions', [
            'tipe' => 'pengeluaran',
            'kategori' => 'BBM & Mesin',
            'judul' => 'Beli Solar Pompa Air',
            'nominal' => 75000,
        ]);

        $viewResponse = $this->get('/keuangan');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Beli Solar Pompa Air');
        $viewResponse->assertSee('Rp 75.000');
    }

    public function test_can_update_manual_financial_transaction(): void
    {
        $transaction = FinancialTransaction::create([
            'tipe' => 'pemasukan',
            'kategori' => 'Penjualan Bibit',
            'judul' => 'Penjualan Bibit Cabai',
            'nominal' => 150000,
            'tanggal' => Carbon::now()->toDateString(),
            'keterangan' => '30 polybag bibit',
        ]);

        $response = $this->put('/keuangan/'.$transaction->id, [
            'tipe' => 'pemasukan',
            'kategori' => 'Penjualan Bibit',
            'judul' => 'Penjualan Bibit Cabai Unggul',
            'nominal' => '200000',
            'tanggal' => Carbon::now()->toDateString(),
            'keterangan' => '40 polybag bibit',
        ]);

        $response->assertRedirect('/keuangan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('financial_transactions', [
            'id' => $transaction->id,
            'judul' => 'Penjualan Bibit Cabai Unggul',
            'nominal' => 200000,
        ]);
    }

    public function test_can_delete_manual_financial_transaction(): void
    {
        $transaction = FinancialTransaction::create([
            'tipe' => 'pengeluaran',
            'kategori' => 'Upah Tenaga Kerja',
            'judul' => 'Upah Cangkul',
            'nominal' => 100000,
            'tanggal' => Carbon::now()->toDateString(),
        ]);

        $response = $this->delete('/keuangan/'.$transaction->id);

        $response->assertRedirect('/keuangan');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('financial_transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_can_create_and_manage_categories_and_subcategories(): void
    {
        // 1. Create parent category
        $responseParent = $this->post('/keuangan/kategori', [
            'nama' => 'Olah Lahan Garapan',
            'tipe' => 'pengeluaran',
            'parent_id' => null,
        ]);
        $responseParent->assertRedirect('/keuangan');
        $responseParent->assertSessionHas('success');

        $this->assertDatabaseHas('financial_categories', [
            'nama' => 'Olah Lahan Garapan',
            'parent_id' => null,
            'tipe' => 'pengeluaran',
        ]);

        $parent = FinancialCategory::where('nama', 'Olah Lahan Garapan')->firstOrFail();

        // 2. Create sub-category
        $responseSub = $this->post('/keuangan/kategori', [
            'nama' => 'Pupuk Dasar - Organik (HOK pemberian pupuk dan mamin)',
            'tipe' => 'pengeluaran',
            'parent_id' => $parent->id,
        ]);
        $responseSub->assertRedirect('/keuangan');
        $responseSub->assertSessionHas('success');

        $this->assertDatabaseHas('financial_categories', [
            'nama' => 'Pupuk Dasar - Organik (HOK pemberian pupuk dan mamin)',
            'parent_id' => $parent->id,
        ]);

        $sub = FinancialCategory::where('nama', 'Pupuk Dasar - Organik (HOK pemberian pupuk dan mamin)')->firstOrFail();

        // 3. Update sub-category
        $responseUpdate = $this->put('/keuangan/kategori/'.$sub->id, [
            'nama' => 'Pupuk Dasar Organik & HOK Mamin',
            'tipe' => 'pengeluaran',
            'parent_id' => $parent->id,
        ]);
        $responseUpdate->assertRedirect('/keuangan');
        $responseUpdate->assertSessionHas('success');

        $this->assertDatabaseHas('financial_categories', [
            'id' => $sub->id,
            'nama' => 'Pupuk Dasar Organik & HOK Mamin',
        ]);

        // 4. Create transaction using category & sub-kategori
        $responseTrx = $this->post('/keuangan', [
            'tipe' => 'pengeluaran',
            'kategori' => 'Olah Lahan Garapan',
            'sub_kategori' => 'Pupuk Dasar Organik & HOK Mamin',
            'judul' => 'Beli Kohe Kambing & HOK 2 Orang',
            'nominal' => 350000,
            'tanggal' => Carbon::now()->toDateString(),
        ]);
        $responseTrx->assertRedirect('/keuangan');

        $this->assertDatabaseHas('financial_transactions', [
            'kategori' => 'Olah Lahan Garapan',
            'sub_kategori' => 'Pupuk Dasar Organik & HOK Mamin',
            'nominal' => 350000,
        ]);

        // 5. Delete category
        $responseDel = $this->delete('/keuangan/kategori/'.$parent->id);
        $responseDel->assertRedirect('/keuangan');
        $responseDel->assertSessionHas('success');

        $this->assertDatabaseMissing('financial_categories', ['id' => $parent->id]);
        $this->assertDatabaseMissing('financial_categories', ['id' => $sub->id]);
    }

    public function test_keuangan_supports_ajax_crud_operations_without_page_reload(): void
    {
        // 1. GET index via AJAX
        $responseIndex = $this->getJson('/keuangan');
        $responseIndex->assertStatus(200)
            ->assertJson([
                'success' => true,
                'totalTransaksi' => 0,
            ])
            ->assertJsonStructure([
                'success',
                'periode',
                'totalPemasukan',
                'formatted_total_pemasukan',
                'totalPengeluaran',
                'formatted_total_pengeluaran',
                'totalTransaksi',
                'jumlahPemasukan',
                'jumlahPengeluaran',
                'transaksiList',
                'categories',
            ]);

        // 2. POST create transaction via AJAX
        $responseCreate = $this->postJson('/keuangan', [
            'tipe' => 'pemasukan',
            'kategori' => 'Hasil Panen',
            'judul' => 'Penjualan Melon Manis',
            'nominal' => 750000,
            'tanggal' => Carbon::now()->toDateString(),
        ]);
        $responseCreate->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $trx = FinancialTransaction::where('judul', 'Penjualan Melon Manis')->firstOrFail();

        // 3. PUT update transaction via AJAX
        $responseUpdate = $this->putJson('/keuangan/'.$trx->id, [
            'tipe' => 'pemasukan',
            'kategori' => 'Hasil Panen',
            'judul' => 'Penjualan Melon Manis Grade A',
            'nominal' => 850000,
            'tanggal' => Carbon::now()->toDateString(),
        ]);
        $responseUpdate->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('financial_transactions', [
            'id' => $trx->id,
            'judul' => 'Penjualan Melon Manis Grade A',
            'nominal' => 850000,
        ]);

        // 4. POST create category via AJAX
        $responseCat = $this->postJson('/keuangan/kategori', [
            'nama' => 'Peralatan Modern',
            'tipe' => 'pengeluaran',
        ]);
        $responseCat->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $cat = FinancialCategory::where('nama', 'Peralatan Modern')->firstOrFail();

        // 5. PUT update category via AJAX
        $responseCatUpdate = $this->putJson('/keuangan/kategori/'.$cat->id, [
            'nama' => 'Peralatan & Mesin',
            'tipe' => 'pengeluaran',
        ]);
        $responseCatUpdate->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // 6. DELETE category via AJAX
        $responseCatDel = $this->deleteJson('/keuangan/kategori/'.$cat->id);
        $responseCatDel->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
        $this->assertDatabaseMissing('financial_categories', ['id' => $cat->id]);

        // 7. DELETE transaction via AJAX
        $responseDel = $this->deleteJson('/keuangan/'.$trx->id);
        $responseDel->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
        $this->assertDatabaseMissing('financial_transactions', ['id' => $trx->id]);
    }
}
