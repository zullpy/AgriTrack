<?php

namespace App\Console\Commands;

use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAgendaRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agri:send-agenda-reminders
                            {--type=auto : morning|afternoon|auto}
                            {--target= : Optional target phone number}
                            {--test : Kirim pesan tes koneksi WhatsApp via Fonnte}
                            {--force : Tetap kirim meskipun notifikasi dimatikan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat agenda pertanian ke WhatsApp via Fonnte (H-1 jam 07:00, H-H jam 07:00 dan 14:00)';

    /**
     * Execute the console command.
     */
    public function handle(FonnteService $fonnteService): int
    {
        $config = $fonnteService->getConfig();
        $isForce = $this->option('force');
        $targetPhone = $this->option('target') ?: null;

        // Mode Pengujian Koneksi WhatsApp
        if ($this->option('test')) {
            $target = $targetPhone ?: $config['target_phone'];
            if (empty($target)) {
                $this->error('Nomor tujuan WhatsApp belum diatur di .env (FONNTE_TARGET_PHONE).');

                return self::FAILURE;
            }

            $this->info("Mengirim pesan tes WhatsApp ke nomor {$target}...");
            $msg = "🌱 *TES NOTIFIKASI AGRI-TRACK*\n\nHalo! Integrasi WhatsApp via Fonnte telah aktif dan berhasil terhubung.\n\n⏰ *Jadwal Otomatis:*\n• 07:00 WIB: Agenda Hari Ini (H-H) & Besok (H-1)\n• 14:00 WIB: Pengingat Siang\n\n💡 Coba balas pesan ini dengan *SUDAH* atau *AGENDA*.";
            $res = $fonnteService->sendMessage($target, $msg);

            if ($res['success']) {
                $this->info("✓ Berhasil! Pesan tes telah terkirim ke nomor {$target}.");

                return self::SUCCESS;
            }

            $this->error('✗ Gagal mengirim pesan: '.($res['error'] ?? 'Terjadi kesalahan pada server Fonnte.'));

            return self::FAILURE;
        }

        if (! $config['enabled'] && ! $isForce) {
            $this->info('Notifikasi WhatsApp Fonnte saat ini dinonaktifkan di pengaturan.');

            return self::SUCCESS;
        }

        $type = strtolower((string) $this->option('type'));

        if ($type === 'auto') {
            $tz = 'Asia/Jakarta';
            $hour = (int) Carbon::now($tz)->format('H');
            // Jika jam sebelum 12 siang (misal jam 07:00) -> morning
            // Jika jam 12 ke atas (misal jam 14:00) -> afternoon
            $type = $hour < 12 ? 'morning' : 'afternoon';
        }

        $this->info("Menjalankan pengingat agenda mode [{$type}]...");

        if ($type === 'morning') {
            $result = $fonnteService->sendMorningNotification($targetPhone);
            if ($result['sent']) {
                $this->info("Notifikasi pagi terkirim! (Hari ini: {$result['today_count']}, Besok: {$result['tomorrow_count']})");
            } else {
                $this->line("Info: {$result['message']}");
            }
        } elseif ($type === 'afternoon') {
            $result = $fonnteService->sendAfternoonReminder($targetPhone);
            if ($result['sent']) {
                $this->info("Pengingat siang terkirim! (Pending: {$result['pending_count']})");
            } else {
                $this->line("Info: {$result['message']}");
            }
        } else {
            $this->error("Tipe pengingat '{$type}' tidak dikenal. Gunakan morning, afternoon, atau auto.");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
