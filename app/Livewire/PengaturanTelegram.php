<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan;
use App\Services\TelegramService;

class PengaturanTelegram extends Component
{
    public ?string $telegram_chat_id = '';
    public ?string $telegram_bot_token = '';
    public bool $notif_telegram_aktif = false;
    public bool $notif_tugas_baru = true;
    public bool $notif_tugas_selesai = true;
    public string $jam_pengingat_pagi = '07:00';
    public string $jam_laporan_malam = '21:00';

    public bool $has_system_bot = false;
    public ?string $status_message = null;
    public ?string $status_type = null; // 'success' atau 'danger'

    protected $rules = [
        'telegram_chat_id'     => 'nullable|string|max:50',
        'telegram_bot_token'   => 'nullable|string|max:100',
        'notif_telegram_aktif' => 'boolean',
        'notif_tugas_baru'     => 'boolean',
        'notif_tugas_selesai'  => 'boolean',
        'jam_pengingat_pagi'   => 'required',
        'jam_laporan_malam'    => 'required',
    ];

    public function mount()
    {
        $userId = Auth::id();
        $pengaturan = Pengaturan::firstOrCreate(
            ['user_id' => $userId],
            [
                'batas_mingguan'       => 500000,
                'batas_bulanan'        => 1000000,
                'batas_tahunan'        => 5000000,
                'notif_telegram_aktif' => false,
                'notif_tugas_baru'     => true,
                'notif_tugas_selesai'  => true,
                'jam_pengingat_pagi'   => '07:00',
                'jam_laporan_malam'    => '21:00',
            ]
        );

        $this->telegram_chat_id     = $pengaturan->telegram_chat_id ?? '';
        $this->telegram_bot_token   = $pengaturan->telegram_bot_token ?? '';
        $this->notif_telegram_aktif = (bool) $pengaturan->notif_telegram_aktif;
        $this->notif_tugas_baru     = (bool) $pengaturan->notif_tugas_baru;
        $this->notif_tugas_selesai  = (bool) $pengaturan->notif_tugas_selesai;
        $this->jam_pengingat_pagi   = $pengaturan->jam_pengingat_pagi ?? '07:00';
        $this->jam_laporan_malam    = $pengaturan->jam_laporan_malam ?? '21:00';

        $this->has_system_bot = !empty(TelegramService::getDefaultBotToken());
    }

    public function simpan()
    {
        $this->validate();

        $userId = Auth::id();
        $pengaturan = Pengaturan::where('user_id', $userId)->first();

        if ($pengaturan) {
            $pengaturan->update([
                'telegram_chat_id'     => trim($this->telegram_chat_id) ?: null,
                'telegram_bot_token'   => trim($this->telegram_bot_token) ?: null,
                'notif_telegram_aktif' => $this->notif_telegram_aktif,
                'notif_tugas_baru'     => $this->notif_tugas_baru,
                'notif_tugas_selesai'  => $this->notif_tugas_selesai,
                'jam_pengingat_pagi'   => $this->jam_pengingat_pagi,
                'jam_laporan_malam'    => $this->jam_laporan_malam,
            ]);

            $this->status_type = 'success';
            $this->status_message = 'Pengaturan Bot Telegram & Waktu Pengingat berhasil disimpan!';
            session()->flash('sukses', $this->status_message);
        }
    }

    public function testKoneksi()
    {
        // Simpan terlebih dahulu agar pengaturan terbaru digunakan
        $this->simpan();

        $user = Auth::user();
        $result = TelegramService::testKoneksiTelegram($user);

        if ($result['success']) {
            $this->status_type = 'success';
            $this->status_message = $result['message'];
            session()->flash('sukses', $result['message']);
        } else {
            $this->status_type = 'danger';
            $this->status_message = $result['message'];
            session()->flash('error', $result['message']);
        }
    }

    public function kirimPengingatManual()
    {
        $user = Auth::user();
        if (!$user->pengaturan || empty($user->pengaturan->telegram_chat_id)) {
            $this->status_type = 'danger';
            $this->status_message = 'Chat ID Telegram belum diatur!';
            session()->flash('error', $this->status_message);
            return;
        }

        $sent = TelegramService::kirimPengingatHarianUser($user, true);
        if ($sent) {
            $this->status_type = 'success';
            $this->status_message = 'Pengingat jadwal & tugas berhasil dikirim ke Telegram kamu!';
            session()->flash('sukses', $this->status_message);
        } else {
            $this->status_type = 'danger';
            $this->status_message = 'Gagal mengirim pengingat ke Telegram.';
            session()->flash('error', $this->status_message);
        }
    }

    public function kirimLaporanManual()
    {
        $user = Auth::user();
        if (!$user->pengaturan || empty($user->pengaturan->telegram_chat_id)) {
            $this->status_type = 'danger';
            $this->status_message = 'Chat ID Telegram belum diatur!';
            session()->flash('error', $this->status_message);
            return;
        }

        $sent = TelegramService::kirimLaporanHarianUser($user, true);
        if ($sent) {
            $this->status_type = 'success';
            $this->status_message = 'Laporan kegiatan harian berhasil dikirim ke Telegram kamu!';
            session()->flash('sukses', $this->status_message);
        } else {
            $this->status_type = 'danger';
            $this->status_message = 'Gagal mengirim laporan ke Telegram.';
            session()->flash('error', $this->status_message);
        }
    }

    public function render()
    {
        return view('livewire.pengaturan-telegram');
    }
}

