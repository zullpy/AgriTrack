<?php

namespace App\Http\Controllers;

use App\Services\FonnteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FonnteSettingController extends Controller
{
    public function __construct(
        protected FonnteService $fonnteService
    ) {}

    /**
     * Tampilkan halaman pengaturan Fonnte WhatsApp (/config-fonnte).
     */
    public function index(): View
    {
        $config = $this->fonnteService->getConfig();
        $webhookUrl = url('/api/webhook/fonnte');

        return view('config-fonnte.index', compact('config', 'webhookUrl'));
    }

    /**
     * Ambil pengaturan Fonnte saat ini dan URL webhook.
     */
    public function getSettings(): JsonResponse
    {
        $config = $this->fonnteService->getConfig();
        $token = $config['token'];
        $maskedToken = '';

        if (! empty($token)) {
            $len = strlen($token);
            $maskedToken = $len > 8
                ? substr($token, 0, 4).str_repeat('*', $len - 8).substr($token, -4)
                : '********';
        }

        return response()->json([
            'success' => true,
            'settings' => [
                'token' => $token,
                'token_masked' => $maskedToken,
                'target_phone' => $config['target_phone'],
                'enabled' => $config['enabled'],
                'webhook_url' => url('/api/webhook/fonnte'),
            ],
        ]);
    }

    /**
     * Simpan pengaturan Fonnte.
     */
    public function updateSettings(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'token' => 'nullable|string|max:255',
            'target_phone' => 'nullable|string|max:50',
            'enabled' => 'nullable',
        ]);

        $validated['enabled'] = $request->boolean('enabled');

        $success = $this->fonnteService->saveConfig($validated);

        if (! $success) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan konfigurasi WhatsApp Fonnte.',
                ], 500);
            }

            return back()->with('error', 'Gagal menyimpan konfigurasi WhatsApp Fonnte.')->withInput();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan notifikasi WhatsApp Fonnte berhasil disimpan.',
                'settings' => $this->fonnteService->getConfig(),
            ]);
        }

        return back()->with('success', 'Pengaturan notifikasi WhatsApp Fonnte berhasil disimpan.');
    }

    /**
     * Kirim pesan uji coba (test notification).
     */
    public function testSend(Request $request): JsonResponse
    {
        $target = $request->input('target_phone');
        $config = $this->fonnteService->getConfig();
        $targetPhone = $target ?: $config['target_phone'];

        if (empty($targetPhone)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp tujuan belum diisi.',
            ], 422);
        }

        $testMsg = "🌱 *TES NOTIFIKASI AGRI-TRACK*\n\nHalo! Integrasi WhatsApp via Fonnte telah aktif.\n\n⏰ *Jadwal Otomatis:*\n• *07:00 WIB:* Agenda Hari Ini (H-H) & Agenda Besok (H-1)\n• *14:00 WIB:* Pengingat Siang agenda yang belum selesai\n\n💡 Coba balas pesan ini dengan *SUDAH* atau *AGENDA* untuk menguji respons bot.";

        $result = $this->fonnteService->sendMessage($targetPhone, $testMsg);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan tes: '.($result['error'] ?? 'Terjadi kesalahan pada server Fonnte.'),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => "Pesan tes berhasil dikirim ke nomor {$targetPhone}!",
        ]);
    }

    /**
     * Trigger manual pengingat agenda (morning atau afternoon).
     */
    public function triggerReminder(Request $request): JsonResponse
    {
        $type = $request->input('type', 'morning');
        $targetPhone = $request->input('target_phone') ?: null;

        if ($type === 'afternoon') {
            $result = $this->fonnteService->sendAfternoonReminder($targetPhone);
        } else {
            $result = $this->fonnteService->sendMorningNotification($targetPhone);
        }

        return response()->json([
            'success' => $result['sent'],
            'message' => $result['message'],
            'result' => $result,
        ]);
    }
}
