<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\CropActivity;
use App\Models\Medicine;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FonnteNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        clearstatcache();
        @unlink(storage_path('app/fonnte_testing.json'));
        @unlink(storage_path('app/fonnte.json'));

        // Configure test fonnte settings
        config([
            'services.fonnte.token' => 'test-fonnte-token-12345',
            'services.fonnte.target_phone' => '08123456789',
            'services.fonnte.enabled' => true,
        ]);
    }

    protected function tearDown(): void
    {
        clearstatcache();
        @unlink(storage_path('app/fonnte_testing.json'));
        @unlink(storage_path('app/fonnte.json'));

        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_can_render_config_fonnte_page(): void
    {
        $response = $this->get('/config-fonnte');
        $response->assertStatus(200)
            ->assertViewIs('config-fonnte.index')
            ->assertSee('Konfigurasi Fonnte')
            ->assertSee('08123456789');
    }

    public function test_can_update_settings_via_config_fonnte_post(): void
    {
        $response = $this->post('/config-fonnte', [
            'token' => 'token-via-page-post',
            'target_phone' => '081299998888',
            'enabled' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fonnteService = app(FonnteService::class);
        $config = $fonnteService->getConfig();
        $this->assertEquals('token-via-page-post', $config['token']);
        $this->assertEquals('081299998888', $config['target_phone']);
        $this->assertTrue($config['enabled']);
    }

    public function test_can_get_and_update_fonnte_settings(): void
    {
        // 1. Get settings
        $response = $this->getJson('/pengaturan/fonnte');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'settings' => [
                    'target_phone' => '08123456789',
                    'enabled' => true,
                ],
            ]);

        // 2. Update settings
        $updateResponse = $this->postJson('/pengaturan/fonnte', [
            'token' => 'new-secret-token',
            'target_phone' => '08987654321',
            'enabled' => true,
        ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Pengaturan notifikasi WhatsApp Fonnte berhasil disimpan.',
            ]);

        $fonnteService = app(FonnteService::class);
        $config = $fonnteService->getConfig();
        $this->assertEquals('new-secret-token', $config['token']);
        $this->assertEquals('08987654321', $config['target_phone']);
    }

    public function test_can_send_test_message(): void
    {
        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'message' => 'Pesan terkirim',
            ], 200),
        ]);

        $response = $this->postJson('/pengaturan/fonnte/test', [
            'target_phone' => '08123456789',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request['target'] === '08123456789'
                && str_contains($request['message'], 'TES NOTIFIKASI AGRI-TRACK');
        });
    }

    public function test_morning_notification_sends_h_minus_1_and_h_h_agendas(): void
    {
        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'message' => 'Sent',
            ], 200),
        ]);

        $now = Carbon::now('Asia/Jakarta');
        Carbon::setTestNow($now);

        // Crop planted 10 days ago. Today is 10 HST, tomorrow is 11 HST.
        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit Merah',
            'varietas' => 'Ori 212',
            'tanggal_tanam' => $now->copy()->subDays(10)->toDateString(),
            'status' => 'Sedang Ditanam',
            'tipe' => 'hst',
        ]);

        // Activity today: H-H (target_hst = 10)
        CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Penyemprotan Fungisida Antracol',
            'target_hst' => 10,
            'status' => 'Belum',
            'metode_aplikasi' => 'semprot',
            'aplikasi_obat' => 'Antracol 70WP: 2.5 gram',
        ]);

        // Activity tomorrow: H-1 (target_hst = 11)
        CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Pemupukan Susulan Kocor NPK',
            'target_hst' => 11,
            'status' => 'Belum',
            'metode_aplikasi' => 'kocor',
        ]);

        // Far future activity (target_hst = 15)
        CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Penyiangan Gulma',
            'target_hst' => 15,
            'status' => 'Belum',
        ]);

        $this->artisan('agri:send-agenda-reminders', ['--type' => 'morning'])
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            $msg = $request['message'];

            return str_contains($msg, 'AGENDA HARI INI')
                && str_contains($msg, 'Penyemprotan Fungisida Antracol')
                && str_contains($msg, 'Antracol 70WP')
                && str_contains($msg, 'AGENDA BESOK (H-1)')
                && str_contains($msg, 'Pemupukan Susulan Kocor NPK')
                && ! str_contains($msg, 'Penyiangan Gulma')
                && str_contains($msg, '*SUDAH*');
        });
    }

    public function test_afternoon_reminder_sends_alert_for_uncompleted_agendas_today(): void
    {
        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'message' => 'Sent',
            ], 200),
        ]);

        $now = Carbon::now('Asia/Jakarta');
        Carbon::setTestNow($now);

        $crop = Crop::create([
            'nama_tanaman' => 'Tomat Servo F1',
            'varietas' => 'Servo',
            'tanggal_tanam' => $now->copy()->subDays(20)->toDateString(),
            'status' => 'Sedang Ditanam',
            'tipe' => 'hst',
        ]);

        CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Pengocoran Kalsium & Boron',
            'target_hst' => 20,
            'status' => 'Belum',
        ]);

        $this->artisan('agri:send-agenda-reminders', ['--type' => 'afternoon'])
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            $msg = $request['message'];

            return str_contains($msg, 'PENGINGAT SIANG')
                && str_contains($msg, 'Tomat Servo F1')
                && str_contains($msg, 'Pengocoran Kalsium & Boron')
                && (str_contains($msg, 'Balas *SUDAH*') || str_contains($msg, 'balas *SUDAH*'));
        });
    }

    public function test_webhook_can_mark_activity_completed_and_deduct_medicine_stock_on_sudah_reply(): void
    {
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $now = Carbon::now('Asia/Jakarta');
        Carbon::setTestNow($now);

        $crop = Crop::create([
            'nama_tanaman' => 'Bawang Merah Tajuk',
            'varietas' => 'Tajuk',
            'tanggal_tanam' => $now->copy()->subDays(15)->toDateString(),
            'status' => 'Sedang Ditanam',
            'tipe' => 'hst',
        ]);

        $medicine = Medicine::create([
            'nama' => 'Score 250 EC',
            'jenis' => 'Fungisida',
            'tanaman_sasaran' => 'Bawang',
            'harga' => 75000,
            'stok' => 4,
            'stok_volume' => 1000.0,
            'kemasan_jumlah' => 250,
            'kemasan_satuan' => 'ml',
        ]);

        $activity = CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Penyemprotan Fungisida Score',
            'target_hst' => 15,
            'status' => 'Belum',
            'metode_aplikasi' => 'semprot',
            'jumlah_tank' => 1,
            'aplikasi_obat' => 'Score 250 EC: 50 ml',
        ]);

        // User sends inbound WhatsApp reply: "SUDAH"
        $webhookPayload = [
            'sender' => '08123456789',
            'message' => 'sudah',
            'name' => 'Petani Maju',
        ];

        $response = $this->postJson('/api/webhook/fonnte', $webhookPayload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        // Activity should now be 'Selesai'
        $activity->refresh();
        $this->assertEquals('Selesai', $activity->status);
        $this->assertNotNull($activity->tanggal_selesai);

        // Medicine stock volume should be deducted (1000 - 50 = 950)
        $medicine->refresh();
        $this->assertEquals(950.0, (float) $medicine->stok_volume);
    }

    public function test_webhook_can_mark_specific_activity_with_id(): void
    {
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $now = Carbon::now('Asia/Jakarta');
        Carbon::setTestNow($now);

        $crop = Crop::create([
            'nama_tanaman' => 'Cabai Rawit',
            'varietas' => 'Ori',
            'tanggal_tanam' => $now->copy()->subDays(5)->toDateString(),
            'status' => 'Sedang Ditanam',
            'tipe' => 'hst',
        ]);

        $act1 = CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Kegiatan 1',
            'target_hst' => 5,
            'status' => 'Belum',
        ]);

        $act2 = CropActivity::create([
            'crop_id' => $crop->id,
            'nama_kegiatan' => 'Kegiatan 2',
            'target_hst' => 5,
            'status' => 'Belum',
        ]);

        // Reply specifying ID of act2: "SUDAH {$act2->id}"
        $webhookPayload = [
            'sender' => '08123456789',
            'message' => "SUDAH {$act2->id}",
            'name' => 'Pak Tani',
        ];

        $response = $this->postJson('/api/webhook/fonnte', $webhookPayload);
        $response->assertStatus(200);

        $act1->refresh();
        $act2->refresh();

        $this->assertEquals('Belum', $act1->status);
        $this->assertEquals('Selesai', $act2->status);
    }

    public function test_webhook_handles_agenda_inquiry(): void
    {
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $webhookPayload = [
            'sender' => '08123456789',
            'message' => 'AGENDA',
            'name' => 'Petani',
        ];

        $response = $this->postJson('/api/webhook/fonnte', $webhookPayload);
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        $reply = $response->json('reply');
        $this->assertStringContainsString('AGENDA', $reply);
    }
}
