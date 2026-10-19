<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\TelegramService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Command Artisan untuk kirim pengingat jadwal & tugas harian
Artisan::command('telegram:kirim-pengingat', function () {
    $this->info('Mengirim notifikasi pengingat harian ke Telegram...');
    $sukses = TelegramService::kirimPengingatHarian(true);
    if ($sukses) {
        $this->info('Pengingat jadwal & tugas berhasil dikirim ke bot Telegram!');
    } else {
        $this->error('Gagal mengirim pengingat ke Telegram.');
    }
})->purpose('Kirim pengingat jadwal kuliah, lab, dan tugas ke bot Telegram');

// Command Artisan untuk kirim rekap laporan produktivitas harian
Artisan::command('telegram:kirim-laporan', function () {
    $this->info('Mengirim laporan kegiatan harian ke Telegram...');
    $sukses = TelegramService::kirimLaporanHarianTelegram();
    if ($sukses) {
        $this->info('Laporan harian berhasil dikirim ke bot Telegram!');
    } else {
        $this->error('Gagal mengirim laporan harian.');
    }
})->purpose('Kirim rekap laporan produktivitas harian ke bot Telegram');

// 1. Jadwalkan otomatis kirim pengingat jadwal kuliah, lab, dan tugas tepat pukul 07:00 Pagi WIB
Schedule::command('telegram:kirim-pengingat')
    ->dailyAt('07:00')
    ->timezone('Asia/Jakarta')
    ->name('pengingat-harian-0700');

// 2. Jadwalkan otomatis kirim rekap laporan harian pukul 21:00 Malam WIB
Schedule::command('telegram:kirim-laporan')
    ->dailyAt('21:00')
    ->timezone('Asia/Jakarta')
    ->name('laporan-harian-2100');
