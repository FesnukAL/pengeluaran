<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class KirimPengingatTugas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tugas:ingatkan {--force : Paksa kirim meskipun dalam masa cooldown}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi rekap pengingat tugas kuliah ke Telegram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mengirim notifikasi pengingat tugas ke Telegram...');
        $force = $this->option('force');
        $sukses = TelegramService::kirimPengingatHarian($force);

        if ($sukses) {
            $this->info('Notifikasi berhasil terkirim ke Telegram!');
        } else {
            $this->warn('Notifikasi dilewati (masih dalam cooldown atau gagal). Gunakan --force untuk memaksa.');
        }

        return Command::SUCCESS;
    }
}
