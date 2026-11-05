<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Pengingat Agenda WhatsApp via Fonnte ──
// 1. Pagi jam 07:00 (Agenda Hari Ini H-H & Agenda Besok H-1)
Schedule::command('agri:send-agenda-reminders --type=morning')
    ->dailyAt('07:00')
    ->timezone('Asia/Jakarta');

// 2. Siang jam 14:00 (Pengingat Agenda Hari Ini H-H yang belum selesai)
Schedule::command('agri:send-agenda-reminders --type=afternoon')
    ->dailyAt('14:00')
    ->timezone('Asia/Jakarta');
