<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\TelegramService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Command Artisan untuk kirim pengingat jadwal & tugas harian
Artisan::command('telegram:kirim-pengingat {--force : Paksa kirim abaikan batas jam}', function () {
    $force = (bool) $this->option('force');
    $this->info('Mengirim notifikasi pengingat harian ke user aktif...');
    $count = TelegramService::kirimPengingatSemuaUser($force);
    $this->info("Pengingat jadwal & tugas berhasil dikirim ke {$count} user!");
})->purpose('Kirim pengingat jadwal kuliah, lab, dan tugas ke bot Telegram pengguna');

// Command Artisan untuk kirim rekap laporan produktivitas harian
Artisan::command('telegram:kirim-laporan {--force : Paksa kirim abaikan batas jam}', function () {
    $force = (bool) $this->option('force');
    $this->info('Mengirim laporan kegiatan harian ke user aktif...');
    $count = TelegramService::kirimLaporanSemuaUser($force);
    $this->info("Laporan harian berhasil dikirim ke {$count} user!");
})->purpose('Kirim rekap laporan produktivitas harian ke bot Telegram pengguna');

// Scheduler otomatis (mengecek kecocokan jam pengaturan masing-masing pengguna)
Schedule::command('telegram:kirim-pengingat')
    ->everyThirtyMinutes()
    ->timezone('Asia/Jakarta')
    ->name('pengingat-harian-otomatis');

Schedule::command('telegram:kirim-laporan')
    ->everyThirtyMinutes()
    ->timezone('Asia/Jakarta')
    ->name('laporan-harian-otomatis');
