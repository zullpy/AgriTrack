<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FonnteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FonnteWebhookController extends Controller
{
    public function __construct(
        protected FonnteService $fonnteService
    ) {}

    /**
     * Handle incoming webhook dari Fonnte ketika user mengirim pesan / membalas di WhatsApp.
     */
    public function handle(Request $request): JsonResponse
    {
        // Respon validasi jika Fonnte melakukan ping / GET check
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => true,
                'message' => 'Fonnte webhook endpoint is active and ready.',
            ]);
        }

        $payload = $request->all();

        Log::info('Fonnte Webhook Received at Controller', $payload);

        $replyMessage = $this->fonnteService->handleIncomingMessage($payload);

        $sender = trim((string) ($request->input('sender') ?: $request->input('phone') ?: ''));

        // Kirim juga secara proaktif via API Fonnte untuk memastikan pesan terkirim
        if (! empty($sender) && ! empty($replyMessage)) {
            $this->fonnteService->sendMessage($sender, $replyMessage);
        }

        return response()->json([
            'status' => true,
            'reply' => $replyMessage,
            'message' => $replyMessage,
        ]);
    }
}
