<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\CropActivity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $storagePath;

    public function __construct(
        protected MedicineStockService $stockService
    ) {
        $this->storagePath = (app()->runningUnitTests() || app()->environment('testing'))
            ? storage_path('app/fonnte_testing.json')
            : storage_path('app/fonnte.json');
    }

    /**
     * Set path penyimpanan konfigurasi Fonnte (berguna untuk testing).
     */
    public function setStoragePath(string $path): self
    {
        $this->storagePath = $path;

        return $this;
    }

    /**
     * Ambil path penyimpanan konfigurasi Fonnte saat ini.
     */
    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    /**
     * Ambil konfigurasi Fonnte saat ini (dari .env, config, atau file storage lokal).
     *
     * @return array{token: string, target_phone: string, enabled: bool, url: string}
     */
    public function getConfig(): array
    {
        $saved = [];
        if (File::exists($this->storagePath)) {
            try {
                $decoded = json_decode(File::get($this->storagePath), true);
                if (is_array($decoded)) {
                    $saved = $decoded;
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal membaca fonnte.json: '.$e->getMessage());
            }
        }

        $isTesting = app()->runningUnitTests() || app()->environment('testing');
        $envToken = ! $isTesting ? env('FONNTE_TOKEN') : null;
        $envPhone = ! $isTesting ? env('FONNTE_TARGET_PHONE') : null;
        $envEnabled = ! $isTesting ? env('FONNTE_ENABLED') : null;

        $token = ! empty($envToken) ? $envToken : ($saved['token'] ?? config('services.fonnte.token') ?? '');
        $phone = ! empty($envPhone) ? $envPhone : ($saved['target_phone'] ?? config('services.fonnte.target_phone') ?? '');
        $enabled = $envEnabled !== null
            ? filter_var($envEnabled, FILTER_VALIDATE_BOOLEAN)
            : (isset($saved['enabled']) ? (bool) $saved['enabled'] : (! empty($token) && ! empty($phone)));
        $url = $saved['url'] ?? config('services.fonnte.url') ?? 'https://api.fonnte.com/send';

        return [
            'token' => trim((string) $token),
            'target_phone' => trim((string) $phone),
            'enabled' => (bool) $enabled,
            'url' => trim((string) $url),
        ];
    }

    /**
     * Simpan konfigurasi Fonnte ke file storage lokal dan file .env.
     *
     * @param  array{token?: string, target_phone?: string, enabled?: bool}  $data
     */
    public function saveConfig(array $data): bool
    {
        try {
            $current = $this->getConfig();
            $newConfig = array_merge($current, array_filter([
                'token' => isset($data['token']) ? trim((string) $data['token']) : null,
                'target_phone' => isset($data['target_phone']) ? trim((string) $data['target_phone']) : null,
                'enabled' => isset($data['enabled']) ? (bool) $data['enabled'] : null,
            ], fn ($v) => $v !== null));

            File::ensureDirectoryExists(dirname($this->storagePath));
            File::put($this->storagePath, json_encode($newConfig, JSON_PRETTY_PRINT));

            // Simpan juga ke file .env agar selalu tersimpan permanen
            $this->writeToEnv([
                'FONNTE_TOKEN' => $newConfig['token'],
                'FONNTE_TARGET_PHONE' => $newConfig['target_phone'],
                'FONNTE_ENABLED' => $newConfig['enabled'] ? 'true' : 'false',
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan konfigurasi Fonnte: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Tulis atau perbarui variabel di file .env.
     */
    public function writeToEnv(array $values): bool
    {
        if (app()->runningUnitTests() || app()->environment('testing')) {
            return true;
        }

        $envPath = base_path('.env');
        if (! File::exists($envPath)) {
            return false;
        }

        try {
            $content = File::get($envPath);

            foreach ($values as $key => $val) {
                $valStr = (string) $val;
                if (preg_match('/\s/', $valStr)) {
                    $valStr = '"'.$valStr.'"';
                }

                $pattern = "/^{$key}=.*/m";
                if (preg_match($pattern, $content)) {
                    $content = preg_replace($pattern, "{$key}={$valStr}", $content);
                } else {
                    $content = rtrim($content)."\n{$key}={$valStr}\n";
                }
            }

            File::put($envPath, $content);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Gagal memperbarui file .env: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Kirim pesan WhatsApp melalui Fonnte API.
     *
     * @return array{success: bool, response?: array, error?: string}
     */
    public function sendMessage(string $targetPhone, string $message): array
    {
        $config = $this->getConfig();
        $token = $config['token'];
        $url = $config['url'] ?: 'https://api.fonnte.com/send';

        if (empty($token)) {
            return [
                'success' => false,
                'error' => 'Fonnte Token belum diatur di aplikasi.',
            ];
        }

        $cleanPhone = preg_replace('/[^\d]/', '', $targetPhone);
        if (empty($cleanPhone)) {
            return [
                'success' => false,
                'error' => 'Nomor WhatsApp tujuan tidak valid.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(20)->post($url, [
                'target' => $cleanPhone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $json = $response->json();
            $isSuccess = $response->successful() && (($json['status'] ?? false) === true || isset($json['id']));

            if (! $isSuccess) {
                $err = $json['reason'] ?? $json['message'] ?? 'Fonnte merespons status error';
                Log::warning('Fonnte API send failed: '.$err, ['response' => $json, 'target' => $cleanPhone]);

                return [
                    'success' => false,
                    'response' => is_array($json) ? $json : [],
                    'error' => (string) $err,
                ];
            }

            return [
                'success' => true,
                'response' => is_array($json) ? $json : [],
            ];
        } catch (\Throwable $e) {
            Log::error('Fonnte API Exception: '.$e->getMessage(), ['target' => $cleanPhone]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Ambil daftar agenda pada tanggal target untuk tanaman aktif yang berstatus Belum.
     *
     * @return Collection<int, CropActivity>
     */
    public function getPendingActivitiesForDate(Carbon $targetDate): Collection
    {
        $dateStr = $targetDate->toDateString();
        $activeCrops = Crop::active()->with(['activities' => function ($q) {
            $q->where('status', 'Belum')->orderBy('target_hst');
        }])->get();

        $matchedActivities = collect();

        foreach ($activeCrops as $crop) {
            $plantDate = $crop->tanggal_tanam ? Carbon::parse($crop->tanggal_tanam)->startOfDay() : null;
            if (! $plantDate) {
                continue;
            }

            foreach ($crop->activities as $act) {
                $act->setRelation('crop', $crop);
                $actTargetDate = $plantDate->copy()->addDays($act->target_hst);
                if ($actTargetDate->toDateString() === $dateStr) {
                    $matchedActivities->push($act);
                }
            }
        }

        return $matchedActivities;
    }

    /**
     * Kirim notifikasi pagi jam 07:00 (Agenda H-H hari ini dan H-1 besok).
     *
     * @return array{sent: bool, message: string, today_count: int, tomorrow_count: int}
     */
    public function sendMorningNotification(?string $targetPhone = null): array
    {
        $config = $this->getConfig();
        $target = $targetPhone ?: $config['target_phone'];

        if (empty($target)) {
            return [
                'sent' => false,
                'message' => 'Nomor target WhatsApp belum diatur.',
                'today_count' => 0,
                'tomorrow_count' => 0,
            ];
        }

        $tz = 'Asia/Jakarta';
        $today = Carbon::now($tz)->startOfDay();
        $tomorrow = $today->copy()->addDay();

        $todayActivities = $this->getPendingActivitiesForDate($today);
        $tomorrowActivities = $this->getPendingActivitiesForDate($tomorrow);

        if ($todayActivities->isEmpty() && $tomorrowActivities->isEmpty()) {
            return [
                'sent' => false,
                'message' => 'Tidak ada agenda untuk hari ini maupun besok.',
                'today_count' => 0,
                'tomorrow_count' => 0,
            ];
        }

        $lines = [];
        $lines[] = '🌱 *PENGINGAT AGENDA PERTANIAN (Pagi)* 🌱';
        $lines[] = '🗓️ *'.Carbon::now($tz)->translatedFormat('l, d F Y').'*';
        $lines[] = '';

        $counter = 1;
        $orderMapping = [];

        if ($todayActivities->isNotEmpty()) {
            $lines[] = '📌 *AGENDA HARI INI (H-H):*';
            foreach ($todayActivities as $act) {
                $crop = $act->crop;
                $labelCode = $crop?->label_hst_or_hss ?? 'HST';
                $lines[] = "{$counter}. *{$crop?->nama_tanaman}* ({$labelCode} {$act->target_hst})";
                $lines[] = "   📋 Kegiatan: *{$act->nama_kegiatan}*";
                if ($act->metode_aplikasi) {
                    $lines[] = "   🏷️ Metode: {$act->metode_aplikasi}";
                }
                if ($act->aplikasi_obat) {
                    $lines[] = "   💊 Obat: {$act->aplikasi_obat}";
                }
                if ($act->sasaran) {
                    $lines[] = "   🎯 Sasaran: {$act->sasaran}";
                }
                $lines[] = "   🆔 Kode: #{$act->id}";
                $lines[] = '';

                $orderMapping[$counter] = $act->id;
                $counter++;
            }
        }

        if ($tomorrowActivities->isNotEmpty()) {
            $lines[] = '⏰ *AGENDA BESOK (H-1):*';
            foreach ($tomorrowActivities as $act) {
                $crop = $act->crop;
                $labelCode = $crop?->label_hst_or_hss ?? 'HST';
                $lines[] = "• *{$crop?->nama_tanaman}* ({$labelCode} {$act->target_hst})";
                $lines[] = "  📋 Kegiatan: *{$act->nama_kegiatan}*";
                if ($act->aplikasi_obat) {
                    $lines[] = "  💊 Obat: {$act->aplikasi_obat}";
                }
                if ($act->sasaran) {
                    $lines[] = "  🎯 Sasaran: {$act->sasaran}";
                }
                $lines[] = "  🆔 Kode: #{$act->id}";
                $lines[] = '';
            }
        }

        if ($todayActivities->isNotEmpty()) {
            $lines[] = '────────────────────';
            $lines[] = '💡 *Konfirmasi Selesai:*';
            $lines[] = 'Balas pesan ini dengan:';
            $lines[] = '👉 *SUDAH* _(untuk menyelesaikan semua agenda hari ini)_';
            if ($todayActivities->count() > 1) {
                $lines[] = '👉 *SUDAH [Nomor]* _(misal: *SUDAH 1* atau *SUDAH #'.$todayActivities->first()->id.'*)_';
            }
        } else {
            $lines[] = '────────────────────';
            $lines[] = '💡 Agenda besok sudah disiapkan. Siapkan obat & peralatan yang diperlukan!';
        }

        $message = implode("\n", $lines);
        $res = $this->sendMessage($target, $message);

        return [
            'sent' => $res['success'],
            'message' => $res['success'] ? 'Notifikasi pagi berhasil dikirim ke WhatsApp.' : ($res['error'] ?? 'Gagal mengirim.'),
            'today_count' => $todayActivities->count(),
            'tomorrow_count' => $tomorrowActivities->count(),
        ];
    }

    /**
     * Kirim pengingat siang jam 14:00 (Agenda H-H yang masih Belum selesai).
     *
     * @return array{sent: bool, message: string, pending_count: int}
     */
    public function sendAfternoonReminder(?string $targetPhone = null): array
    {
        $config = $this->getConfig();
        $target = $targetPhone ?: $config['target_phone'];

        if (empty($target)) {
            return [
                'sent' => false,
                'message' => 'Nomor target WhatsApp belum diatur.',
                'pending_count' => 0,
            ];
        }

        $tz = 'Asia/Jakarta';
        $today = Carbon::now($tz)->startOfDay();
        $todayActivities = $this->getPendingActivitiesForDate($today);

        if ($todayActivities->isEmpty()) {
            return [
                'sent' => false,
                'message' => 'Semua agenda hari ini telah selesai atau tidak ada agenda.',
                'pending_count' => 0,
            ];
        }

        $lines = [];
        $lines[] = '☀️ *PENGINGAT SIANG AGENDA HARI INI (14:00 WIB)* ☀️';
        $lines[] = 'Agenda berikut masih belum diselesaikan:';
        $lines[] = '';

        $counter = 1;
        foreach ($todayActivities as $act) {
            $crop = $act->crop;
            $labelCode = $crop?->label_hst_or_hss ?? 'HST';
            $lines[] = "{$counter}. *{$crop?->nama_tanaman}* ({$labelCode} {$act->target_hst})";
            $lines[] = "   📋 Kegiatan: *{$act->nama_kegiatan}*";
            if ($act->aplikasi_obat) {
                $lines[] = "   💊 Obat: {$act->aplikasi_obat}";
            }
            $lines[] = "   🆔 Kode: #{$act->id}";
            $lines[] = '';
            $counter++;
        }

        $lines[] = '────────────────────';
        $lines[] = '💡 Jangan lupa balas *SUDAH* jika kegiatan di atas telah selesai dikerjakan agar stok obat otomatis dipotong dan pengeluaran tercatat.';

        $message = implode("\n", $lines);
        $res = $this->sendMessage($target, $message);

        return [
            'sent' => $res['success'],
            'message' => $res['success'] ? 'Pengingat siang berhasil dikirim ke WhatsApp.' : ($res['error'] ?? 'Gagal mengirim.'),
            'pending_count' => $todayActivities->count(),
        ];
    }

    /**
     * Proses pesan masuk dari WhatsApp Webhook Fonnte.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleIncomingMessage(array $payload): string
    {
        $sender = trim((string) ($payload['sender'] ?? ''));
        $rawMessage = trim((string) ($payload['message'] ?? $payload['text'] ?? ''));
        $lower = strtolower($rawMessage);

        Log::info('Fonnte Webhook Received', [
            'sender' => $sender,
            'message' => $rawMessage,
        ]);

        if (empty($lower)) {
            return 'Pesan kosong diterima.';
        }

        // 1. Tangani perintah SUDAH / SELESAI
        if (preg_match('/^(sudah|selesai)(\s+.*)?$/i', $lower, $matches)) {
            $param = trim($matches[2] ?? '');

            return $this->processMarkAsDone($param);
        }

        // 2. Tangani perintah CEK AGENDA
        if (preg_match('/^(agenda|cek|jadwal|list)(\s+.*)?$/i', $lower)) {
            return $this->buildAgendaCheckReply();
        }

        // 3. Tangani BANTUAN / HELP
        return $this->buildHelpReply();
    }

    /**
     * Eksekusi penandaan kegiatan selesai dari pesan balasan WhatsApp.
     */
    protected function processMarkAsDone(string $param): string
    {
        $tz = 'Asia/Jakarta';
        $today = Carbon::now($tz)->startOfDay();
        $todayActivities = $this->getPendingActivitiesForDate($today);

        // Kasus A: User menyertakan ID atau nomor urut, misal: "sudah 1" atau "sudah #45" atau "sudah 45"
        if (! empty($param) && $param !== 'semua' && $param !== 'all') {
            $numOnly = preg_replace('/[^\d]/', '', $param);
            $num = (int) $numOnly;

            if ($num > 0) {
                // Cek apakah nomor tersebut adalah ID activity langsung
                $activity = CropActivity::where('id', $num)->first();

                // Jika bukan ID langsung atau tidak ketemu, cek nomor urut dari daftar hari ini (1, 2, ...)
                if (! $activity && $num <= $todayActivities->count()) {
                    $activity = $todayActivities->values()->get($num - 1);
                }

                if ($activity) {
                    if ($activity->status === 'Selesai') {
                        return "ℹ️ Kegiatan *{$activity->nama_kegiatan}* pada tanaman *{$activity->crop?->nama_tanaman}* sudah berstatus SELESAI sebelumnya.";
                    }

                    $this->completeActivity($activity);

                    return "✅ *Alhamdulillah!*\nKegiatan *{$activity->nama_kegiatan}* pada tanaman *{$activity->crop?->nama_tanaman}* (#{$activity->id}) berhasil ditandai *SELESAI*.\n📦 Stok obat terkait telah dipotong otomatis. Terima kasih! 🌾";
                }

                return "⚠️ Kegiatan dengan kode / nomor *{$param}* tidak ditemukan atau sudah selesai.";
            }
        }

        // Kasus B: User mengetik "SUDAH" saja (atau "sudah semua")
        if ($todayActivities->isEmpty()) {
            // Cek apakah ada kegiatan terlewat (overdue) yang masih Belum
            $overdueActivities = $this->getOverdueActivities();
            if ($overdueActivities->isNotEmpty()) {
                $first = $overdueActivities->first();
                $this->completeActivity($first);

                return "✅ Kegiatan terlewat *{$first->nama_kegiatan}* pada tanaman *{$first->crop?->nama_tanaman}* berhasil ditandai *SELESAI*.\n📦 Stok obat telah dipotong otomatis.";
            }

            return 'ℹ️ Tidak ada agenda pending untuk hari ini yang perlu diselesaikan. Ketik *AGENDA* untuk melihat agenda mendatang.';
        }

        // Jika hanya ada 1 kegiatan hari ini
        if ($todayActivities->count() === 1) {
            $activity = $todayActivities->first();
            $this->completeActivity($activity);

            return "✅ *Alhamdulillah!*\nKegiatan *{$activity->nama_kegiatan}* pada tanaman *{$activity->crop?->nama_tanaman}* berhasil ditandai *SELESAI*.\n📦 Stok obat telah dipotong otomatis. Semangat bertani! 🌾";
        }

        // Jika ada lebih dari 1 kegiatan hari ini, tandai semua selesai dan rangkum
        $completedNames = [];
        foreach ($todayActivities as $act) {
            $this->completeActivity($act);
            $completedNames[] = "• *{$act->nama_kegiatan}* ({$act->crop?->nama_tanaman})";
        }

        $summaryList = implode("\n", $completedNames);

        return "✅ *Alhamdulillah!*\nSebanyak *{$todayActivities->count()} kegiatan hari ini* berhasil ditandai *SELESAI*:\n{$summaryList}\n\n📦 Seluruh stok obat terkait telah dipotong otomatis. Luar biasa! 🎉";
    }

    /**
     * Selesaikan kegiatan dan potong stok obat.
     */
    protected function completeActivity(CropActivity $activity): void
    {
        $activity->update([
            'status' => 'Selesai',
            'tanggal_selesai' => now(),
        ]);

        $this->stockService->deductForActivity($activity);
    }

    /**
     * Ambil kegiatan yang terlewat (HST sudah lewat dari hari ini dan status masih Belum).
     *
     * @return Collection<int, CropActivity>
     */
    protected function getOverdueActivities(): Collection
    {
        $activeCrops = Crop::active()->with(['activities' => function ($q) {
            $q->where('status', 'Belum')->orderBy('target_hst');
        }])->get();

        $overdue = collect();
        $today = Carbon::now('Asia/Jakarta')->startOfDay();

        foreach ($activeCrops as $crop) {
            $plantDate = $crop->tanggal_tanam ? Carbon::parse($crop->tanggal_tanam)->startOfDay() : null;
            if (! $plantDate) {
                continue;
            }

            foreach ($crop->activities as $act) {
                $act->setRelation('crop', $crop);
                $actTargetDate = $plantDate->copy()->addDays($act->target_hst);
                if ($actTargetDate->lt($today)) {
                    $overdue->push($act);
                }
            }
        }

        return $overdue;
    }

    /**
     * Susun teks balasan untuk perintah AGENDA / CEK.
     */
    protected function buildAgendaCheckReply(): string
    {
        $tz = 'Asia/Jakarta';
        $today = Carbon::now($tz)->startOfDay();
        $tomorrow = $today->copy()->addDay();

        $todayActs = $this->getPendingActivitiesForDate($today);
        $tomorrowActs = $this->getPendingActivitiesForDate($tomorrow);

        $lines = [];
        $lines[] = '📋 *STATUS AGENDA PERTANIAN* 📋';
        $lines[] = '';

        if ($todayActs->isNotEmpty()) {
            $lines[] = '📌 *Hari Ini:*';
            $idx = 1;
            foreach ($todayActs as $act) {
                $lines[] = "{$idx}. *{$act->nama_kegiatan}* ({$act->crop?->nama_tanaman}) - #{$act->id}";
                $idx++;
            }
            $lines[] = '';
        } else {
            $lines[] = '📌 *Hari Ini:* Tidak ada agenda pending (semua selesai).';
            $lines[] = '';
        }

        if ($tomorrowActs->isNotEmpty()) {
            $lines[] = '⏰ *Besok:*';
            foreach ($tomorrowActs as $act) {
                $lines[] = "• *{$act->nama_kegiatan}* ({$act->crop?->nama_tanaman})";
            }
            $lines[] = '';
        }

        $lines[] = '💡 Balas *SUDAH* jika agenda hari ini telah selesai dikerjakan.';

        return implode("\n", $lines);
    }

    /**
     * Teks panduan bantuan interaksi WA.
     */
    protected function buildHelpReply(): string
    {
        return "🤖 *ASISTEN WHATSAPP AGRI-TRACK*\n\nPerintah yang bisa Anda gunakan:\n👉 *SUDAH* : Tandai agenda hari ini selesai & potong stok obat otomatis\n👉 *SUDAH 1* : Tandai nomor urut agenda tertentu selesai\n👉 *AGENDA* : Cek agenda hari ini & besok\n👉 *BANTUAN* : Menampilkan pesan panduan ini";
    }
}
