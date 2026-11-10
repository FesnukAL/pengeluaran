<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Tugas;
use App\Models\Matkul;
use App\Models\Pengaturan;
use Carbon\Carbon;

class TelegramService
{
    /**
     * Bot token default sistem (dari config/services.php atau .env)
     */
    public static function getDefaultBotToken(): ?string
    {
        return config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
    }

    /**
     * Chat ID default sistem (dari config/services.php atau .env)
     */
    public static function getDefaultChatId(): ?string
    {
        return config('services.telegram.chat_id') ?: env('TELEGRAM_CHAT_ID');
    }

    /**
     * Kirim pesan teks ke Telegram dengan token & chatId tertentu atau default
     */
    public static function kirimPesan(string $pesan, ?string $token = null, ?string $chatId = null): bool
    {
        $botToken = $token ?: self::getDefaultBotToken();
        $targetChatId = $chatId ?: self::getDefaultChatId();

        if (!$botToken || !$targetChatId) {
            Log::warning('Telegram bot token atau chat ID kosong. Pesan dibatalkan.');
            return false;
        }

        try {
            $response = Http::withoutVerifying()->timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $targetChatId,
                'text' => $pesan,
                'parse_mode' => 'Markdown',
            ]);

            if (!$response->successful()) {
                Log::error('Telegram API error: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Gagal mengirim pesan Telegram: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim pesan ke user tertentu berdasarkan pengaturan Telegram miliknya
     */
    public static function kirimPesanKeUser(User $user, string $pesan): bool
    {
        $pengaturan = $user->pengaturan;
        if (!$pengaturan || !$pengaturan->telegram_chat_id) {
            return false;
        }

        $token = $pengaturan->getEffectiveBotToken();
        $chatId = $pengaturan->telegram_chat_id;

        return self::kirimPesan($pesan, $token, $chatId);
    }

    /**
     * Uji coba koneksi Telegram untuk user tertentu
     */
    public static function testKoneksiTelegram(User $user): array
    {
        $pengaturan = $user->pengaturan;
        if (!$pengaturan || empty($pengaturan->telegram_chat_id)) {
            return [
                'success' => false,
                'message' => 'Chat ID Telegram belum diisi! Silakan masukkan Chat ID kamu terlebih dahulu.',
            ];
        }

        $token = $pengaturan->getEffectiveBotToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Bot Token Telegram tidak ditemukan. Hubungi admin atau isi bot token pribadi.',
            ];
        }

        $jamPagi = $pengaturan->jam_pengingat_pagi ?? '07:00';
        $jamMalam = $pengaturan->jam_laporan_malam ?? '21:00';

        $pesan = "🎉 *TES KONEKSI TELEGRAM BERHASIL!* 🎉\n\n"
            . "Halo *{$user->name}*! 👋\n"
            . "Akun Catatan Keuangan & Tugas kamu sudah sukses terhubung dengan bot ini.\n\n"
            . "⚙️ *Ringkasan Pengaturan Pengingat Kamu:*\n"
            . "• 🔔 Status Notifikasi: " . ($pengaturan->notif_telegram_aktif ? 'Aktif ✅' : 'Nonaktif ⏸️') . "\n"
            . "• ⏰ Pengingat Jadwal & Tugas Pagi: *{$jamPagi} WIB*\n"
            . "• 🌙 Rekap Laporan Malam: *{$jamMalam} WIB*\n"
            . "• 📝 Notif Tugas Baru: " . ($pengaturan->notif_tugas_baru ? 'Aktif' : 'Mati') . "\n"
            . "• ✅ Notif Tugas Selesai: " . ($pengaturan->notif_tugas_selesai ? 'Aktif' : 'Mati') . "\n\n"
            . "Pesan ini dikirim sebagai uji coba koneksi. Selamat beraktivitas! 🚀";

        $terkirim = self::kirimPesan($pesan, $token, $pengaturan->telegram_chat_id);

        if ($terkirim) {
            return [
                'success' => true,
                'message' => 'Pesan uji coba berhasil dikirim ke Telegram kamu! Silakan periksa aplikasi Telegram.',
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal mengirim pesan ke Telegram. Pastikan Chat ID valid dan kamu sudah menekan tombol START di bot Telegram.',
        ];
    }

    /**
     * Kirim notifikasi saat tugas atau daily task baru ditambahkan
     */
    public static function notifikasiTugasDitambahkan(Tugas $tugas): bool
    {
        $user = $tugas->user;
        if (!$user) {
            return false;
        }

        $pengaturan = $user->pengaturan;
        if (!$pengaturan || !$pengaturan->notif_telegram_aktif || !$pengaturan->notif_tugas_baru || empty($pengaturan->telegram_chat_id)) {
            return false;
        }

        $userName = $user->name;
        $deadlineCarbon = Carbon::parse($tugas->deadline, 'Asia/Jakarta');

        if ($tugas->tipe === 'daily') {
            $pesan = "📌 *DAILY TASK BARU DITAMBAHKAN!* 📌\n\n"
                . "⚡ *Kegiatan:* {$tugas->judul_tugas}\n"
                . "🎯 *Prioritas:* {$tugas->prioritas}\n"
                . "📅 *Target:* Hari ini (" . $deadlineCarbon->translatedFormat('d M Y') . ")\n\n"
                . "Semangat menuntaskannya hari ini, {$userName}! 🚀💪";
        } else {
            $matkulNama = 'Umum';
            $icon = '📚';
            if ($tugas->matkul) {
                $isLab = ($tugas->matkul->tipe === 'jaga_lab');
                $matkulNama = ($isLab ? '[Lab] ' : '') . $tugas->matkul->nama_matkul;
                $icon = $isLab ? '🔬' : '📚';
            }
            $pesan = "📝 *TUGAS KULIAH BARU DICATAT!* 📝\n\n"
                . "📌 *Judul Tugas:* {$tugas->judul_tugas}\n"
                . "{$icon} *Matkul:* {$matkulNama}\n"
                . "🎯 *Prioritas:* {$tugas->prioritas}\n"
                . "⏰ *Deadline:* " . $deadlineCarbon->translatedFormat('d M Y, H:i') . " WIB\n\n"
                . "Tugas sudah tersimpan di sistem. Jangan lupa dicicil ya, {$userName}! 🚀";
        }

        return self::kirimPesanKeUser($user, $pesan);
    }

    /**
     * Kirim notifikasi saat tugas ditandai selesai
     */
    public static function notifikasiTugasSelesai(Tugas $tugas): bool
    {
        $user = $tugas->user;
        if (!$user) {
            return false;
        }

        $pengaturan = $user->pengaturan;
        if (!$pengaturan || !$pengaturan->notif_telegram_aktif || !$pengaturan->notif_tugas_selesai || empty($pengaturan->telegram_chat_id)) {
            return false;
        }

        $userName = $user->name;
        $waktuSelesai = Carbon::now('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB';

        if ($tugas->tipe === 'daily') {
            $pesan = "🎉 *ALHAMDULILLAH, DAILY TASK SELESAI!* 🎉\n\n"
                . "✅ *Kegiatan:* {$tugas->judul_tugas}\n"
                . "⚡ *Kategori:* Daily Task (Prioritas: {$tugas->prioritas})\n"
                . "⏰ *Waktu Diselesaikan:* {$waktuSelesai}\n\n"
                . "Mantap {$userName}, satu to-do harian beres! Terus produktif ya! 🔥💪";
        } else {
            $matkulNama = 'Umum';
            $icon = '📝';
            if ($tugas->matkul) {
                $isLab = ($tugas->matkul->tipe === 'jaga_lab');
                $matkulNama = ($isLab ? '[Lab] ' : '') . $tugas->matkul->nama_matkul;
                $icon = $isLab ? '🔬' : '📚';
            }

            $pesan = "🎉 *ALHAMDULILLAH, TUGAS KULIAH SELESAI!* 🎉\n\n"
                . "✅ *Judul Tugas:* {$tugas->judul_tugas}\n"
                . "{$icon} *Asal/Matkul:* {$matkulNama}\n"
                . "⏰ *Waktu Diselesaikan:* {$waktuSelesai}\n\n"
                . "Kerja bagus {$userName}, 1 beban kuliah berkurang! Terus semangat ya! 🔥💪";
        }

        return self::kirimPesanKeUser($user, $pesan);
    }

    /**
     * Kirim pengingat harian untuk 1 USER tertentu
     */
    public static function kirimPengingatHarianUser(User $user, bool $force = false): bool
    {
        $pengaturan = $user->pengaturan;
        if (!$pengaturan || !$pengaturan->notif_telegram_aktif || empty($pengaturan->telegram_chat_id)) {
            return false;
        }

        $now = Carbon::now('Asia/Jakarta');
        $cacheKey = "telegram_pengingat_user_{$user->id}_" . $now->toDateString();

        if (!$force) {
            // Cek apakah sudah pernah dikirim hari ini
            if (Cache::has($cacheKey)) {
                return false;
            }

            // Cek waktu pengingat yang diatur user
            $jamPengingat = $pengaturan->jam_pengingat_pagi ?? '07:00';
            $waktuTarget = Carbon::createFromFormat('Y-m-d H:i', $now->toDateString() . ' ' . $jamPengingat, 'Asia/Jakarta');
            if ($now->lt($waktuTarget)) {
                return false;
            }
        }

        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIni = $hariIndo[$now->format('l')] ?? $now->format('l');
        $tanggalHariIni = $now->translatedFormat('l, d F Y');

        // 1. Ambil Jadwal HARI INI milik user
        $kuliahHariIni = Matkul::where('user_id', $user->id)
            ->where('tipe', 'kuliah')
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        $jagaLabHariIni = Matkul::where('user_id', $user->id)
            ->where('tipe', 'jaga_lab')
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // 2. Ambil Tugas milik user yang belum selesai
        $tugasBelumSelesai = Tugas::where('user_id', $user->id)
            ->with('matkul')
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline', 'asc')
            ->get();

        if ($kuliahHariIni->isEmpty() && $jagaLabHariIni->isEmpty() && $tugasBelumSelesai->isEmpty()) {
            if ($force) {
                self::kirimPesanKeUser($user, "✨ *LAPORAN HARIAN {$user->name}:*\nHari ini ({$hariIni}) tidak ada jadwal kuliah maupun jaga lab, dan semua tugas sudah beres! Waktunya santai! 😎👌");
            }
            return true;
        }

        $pesan = "🔔 *PENGINGAT HARIAN {$user->name}* 🔔\n"
            . "📅 *{$tanggalHariIni}*\n\n";

        // === SEKSI 1: JADWAL KULIAH HARI INI ===
        if ($kuliahHariIni->isNotEmpty()) {
            $pesan .= "📚 *JADWAL KULIAH HARI INI ({$hariIni}):*\n";
            foreach ($kuliahHariIni as $index => $k) {
                $no = $index + 1;
                $jam = ($k->jam_mulai && $k->jam_selesai)
                    ? substr($k->jam_mulai, 0, 5) . ' - ' . substr($k->jam_selesai, 0, 5) . ' WIB'
                    : 'Waktu belum diatur';
                $ruang = $k->ruangan ? " | Ruang: {$k->ruangan}" : '';
                $catatan = $k->catatan ? " _({$k->catatan})_" : '';

                $pesan .= "{$no}. *{$k->nama_matkul}*\n";
                $pesan .= "   ⏰ {$jam}{$ruang}\n";
                if ($k->dosen) {
                    $pesan .= "   👤 Dosen: {$k->dosen}{$catatan}\n";
                } elseif ($k->catatan) {
                    $pesan .= "   📝 Catatan: {$k->catatan}\n";
                }
            }
            $pesan .= "\n";
        } else {
            $pesan .= "📚 *Jadwal Kuliah:* Tidak ada jadwal kuliah hari ini ({$hariIni}).\n\n";
        }

        // === SEKSI 2: JADWAL JAGA LAB HARI INI ===
        if ($jagaLabHariIni->isNotEmpty()) {
            $pesan .= "🔬 *JADWAL JAGA LAB HARI INI ({$hariIni}):*\n";
            foreach ($jagaLabHariIni as $index => $lab) {
                $no = $index + 1;
                $jam = ($lab->jam_mulai && $lab->jam_selesai)
                    ? substr($lab->jam_mulai, 0, 5) . ' - ' . substr($lab->jam_selesai, 0, 5) . ' WIB'
                    : 'Waktu belum diatur';
                $ruang = $lab->ruangan ? " | {$lab->ruangan}" : '';
                $shift = $lab->kode_matkul ? " [{$lab->kode_matkul}]" : '';
                $catatan = $lab->catatan ? " _({$lab->catatan})_" : '';

                $pesan .= "{$no}. *{$lab->nama_matkul}*{$shift}\n";
                $pesan .= "   ⏰ {$jam}{$ruang}\n";
                if ($lab->dosen) {
                    $pesan .= "   👤 Kordas/Rekan: {$lab->dosen}{$catatan}\n";
                } elseif ($lab->catatan) {
                    $pesan .= "   📝 Catatan: {$lab->catatan}\n";
                }
            }
            $pesan .= "\n";
        } else {
            $pesan .= "🔬 *Jadwal Jaga Lab:* Tidak ada jadwal jaga lab hari ini ({$hariIni}).\n\n";
        }

        // === SEKSI 3: TUGAS KULIAH / TUGAS LAB BELUM SELESAI ===
        if ($tugasBelumSelesai->isNotEmpty()) {
            $jumlahTugas = $tugasBelumSelesai->count();
            $pesan .= "📝 *DAFTAR TUGAS BELUM SELESAI ({$jumlahTugas} Tugas):*\n";
            foreach ($tugasBelumSelesai as $index => $t) {
                $no = $index + 1;
                $asalNama = 'Umum';
                $iconTipe = '📝';
                if ($t->matkul) {
                    $isLab = ($t->matkul->tipe === 'jaga_lab');
                    $asalNama = ($isLab ? '[Lab] ' : '') . $t->matkul->nama_matkul;
                    $iconTipe = $isLab ? '🔬' : '📚';
                }

                $deadline = Carbon::parse($t->deadline, 'Asia/Jakarta');

                $statusWaktu = '';
                if ($deadline->isPast()) {
                    $statusWaktu = "⚠️ *SUDAH TERLEWAT!* (" . $deadline->diffForHumans() . ")";
                } elseif ($deadline->isToday()) {
                    $statusWaktu = "🔥 *DEADLINE HARI INI!* (" . $deadline->format('H:i') . " WIB)";
                } else {
                    $statusWaktu = "⏳ " . $deadline->diffForHumans() . " (" . $deadline->translatedFormat('d M, H:i') . " WIB)";
                }

                $pesan .= "{$no}. *{$t->judul_tugas}*\n";
                $pesan .= "   {$iconTipe} Asal: {$asalNama} | Prioritas: {$t->prioritas}\n";
                $pesan .= "   ⏰ {$statusWaktu}\n";
            }
            $pesan .= "\nSemangat ya {$user->name}, jangan lupa juga catat pengeluaran hari ini! 🚀💪";
        } else {
            $pesan .= "✨ *Tugas:* Semua tugas beres! Tidak ada deadline menanti. Mantap! 👍";
        }

        $hasil = self::kirimPesanKeUser($user, $pesan);

        if ($hasil) {
            Cache::put($cacheKey, true, $now->copy()->endOfDay());
        }

        return $hasil;
    }

    /**
     * Kirim Laporan Harian Lengkap untuk 1 USER tertentu
     */
    public static function kirimLaporanHarianUser(User $user, bool $force = false): bool
    {
        $pengaturan = $user->pengaturan;
        if (!$pengaturan || !$pengaturan->notif_telegram_aktif || empty($pengaturan->telegram_chat_id)) {
            return false;
        }

        $now = Carbon::now('Asia/Jakarta');
        $cacheKey = "telegram_laporan_user_{$user->id}_" . $now->toDateString();

        if (!$force) {
            if (Cache::has($cacheKey)) {
                return false;
            }

            $jamMalam = $pengaturan->jam_laporan_malam ?? '21:00';
            $waktuTarget = Carbon::createFromFormat('Y-m-d H:i', $now->toDateString() . ' ' . $jamMalam, 'Asia/Jakarta');
            if ($now->lt($waktuTarget)) {
                return false;
            }
        }

        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIni = $hariIndo[$now->format('l')] ?? $now->format('l');
        $tanggalFormatted = $now->translatedFormat('l, d F Y');

        // 1. Daily Task Hari Ini milik user
        $dailyToday = Tugas::where('user_id', $user->id)
            ->where('tipe', 'daily')
            ->whereDate('created_at', $now->toDateString())
            ->get();

        $totalDaily = $dailyToday->count();
        $selesaiDaily = $dailyToday->where('status', 'selesai')->count();
        $rateDaily = $totalDaily > 0 ? round(($selesaiDaily / $totalDaily) * 100) : 0;

        // 2. Jadwal Kuliah & Jaga Lab
        $kuliahHariIni = Matkul::where('user_id', $user->id)->where('tipe', 'kuliah')->where('hari', $hariIni)->orderBy('jam_mulai')->get();
        $jagaLabHariIni = Matkul::where('user_id', $user->id)->where('tipe', 'jaga_lab')->where('hari', $hariIni)->orderBy('jam_mulai')->get();

        // 3. Tugas Kuliah Belum Selesai
        $tugasKuliahBelum = Tugas::where('user_id', $user->id)
            ->with('matkul')
            ->where('tipe', 'kuliah')
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline', 'asc')
            ->get();

        $pesan = "📊 *LAPORAN KEGIATAN HARIAN: {$user->name}* 📊\n"
            . "📅 *{$tanggalFormatted}*\n"
            . "────────────────────────\n\n";

        // === SEKSI DAILY TASK ===
        $pesan .= "⚡ *PRODUKTIVITAS DAILY TASK:*\n";
        if ($totalDaily > 0) {
            $pesan .= "📈 Capaian: *{$selesaiDaily}/{$totalDaily} Selesai* ({$rateDaily}%)\n";
            foreach ($dailyToday as $idx => $d) {
                $no = $idx + 1;
                $icon = ($d->status === 'selesai') ? '✅' : '⏳';
                $strike = ($d->status === 'selesai') ? ' _(Selesai)_' : ' *(Belum)*';
                $pesan .= "{$no}. {$icon} {$d->judul_tugas}{$strike}\n";
            }
        } else {
            $pesan .= "ℹ️ Belum ada Daily Task yang dicatat hari ini.\n";
        }
        $pesan .= "\n";

        // === SEKSI JADWAL HARI INI ===
        $pesan .= "🗓️ *AGENDA KULIAH & LAB HARI INI ({$hariIni}):*\n";
        if ($kuliahHariIni->isNotEmpty() || $jagaLabHariIni->isNotEmpty()) {
            foreach ($kuliahHariIni as $k) {
                $jam = ($k->jam_mulai && $k->jam_selesai) ? substr($k->jam_mulai, 0, 5) . '-' . substr($k->jam_selesai, 0, 5) : '-';
                $pesan .= "• 📚 *{$k->nama_matkul}* ({$jam} WIB | {$k->ruangan})\n";
            }
            foreach ($jagaLabHariIni as $lab) {
                $jam = ($lab->jam_mulai && $lab->jam_selesai) ? substr($lab->jam_mulai, 0, 5) . '-' . substr($lab->jam_selesai, 0, 5) : '-';
                $pesan .= "• 🔬 *[Lab] {$lab->nama_matkul}* ({$jam} WIB | {$lab->ruangan})\n";
            }
        } else {
            $pesan .= "• Tidak ada jadwal kuliah atau lab hari ini.\n";
        }
        $pesan .= "\n";

        // === SEKSI DEADLINE KULIAH TERSISA ===
        $pesan .= "📝 *TUGAS KULIAH BELUM SELESAI ({$tugasKuliahBelum->count()}):*\n";
        if ($tugasKuliahBelum->isNotEmpty()) {
            foreach ($tugasKuliahBelum->take(5) as $i => $t) {
                $num = $i + 1;
                $dl = Carbon::parse($t->deadline, 'Asia/Jakarta');
                $pesan .= "{$num}. *{$t->judul_tugas}* (" . ($t->matkul->nama_matkul ?? 'Umum') . ")\n";
                $pesan .= "   ⏰ " . $dl->diffForHumans() . " ({$dl->format('d M, H:i')})\n";
            }
            if ($tugasKuliahBelum->count() > 5) {
                $sisa = $tugasKuliahBelum->count() - 5;
                $pesan .= "   _...dan {$sisa} tugas lainnya._\n";
            }
        } else {
            $pesan .= "• 🎉 Semua tugas kuliah sudah tuntas!\n";
        }

        $pesan .= "\n────────────────────────\n";
        $pesan .= "Tetap konsisten dan jaga kesehatan ya, {$user->name}! ✨💪";

        $hasil = self::kirimPesanKeUser($user, $pesan);

        if ($hasil) {
            Cache::put($cacheKey, true, $now->copy()->endOfDay());
        }

        return $hasil;
    }

    /**
     * Kirim Pengingat Harian ke SEMUA user yang mengaktifkan Telegram
     */
    public static function kirimPengingatSemuaUser(bool $force = false): int
    {
        $users = User::whereHas('pengaturan', function ($q) {
            $q->where('notif_telegram_aktif', true)
              ->whereNotNull('telegram_chat_id')
              ->where('telegram_chat_id', '!=', '');
        })->with('pengaturan')->get();

        $terkirim = 0;
        foreach ($users as $user) {
            if (self::kirimPengingatHarianUser($user, $force)) {
                $terkirim++;
            }
        }

        return $terkirim;
    }

    /**
     * Kirim Laporan Harian ke SEMUA user yang mengaktifkan Telegram
     */
    public static function kirimLaporanSemuaUser(bool $force = false): int
    {
        $users = User::whereHas('pengaturan', function ($q) {
            $q->where('notif_telegram_aktif', true)
              ->whereNotNull('telegram_chat_id')
              ->where('telegram_chat_id', '!=', '');
        })->with('pengaturan')->get();

        $terkirim = 0;
        foreach ($users as $user) {
            if (self::kirimLaporanHarianUser($user, $force)) {
                $terkirim++;
            }
        }

        return $terkirim;
    }

    /**
     * Backward-compatibility wrapper untuk pemanggilan lama
     */
    public static function kirimPengingatHarian(bool $force = false): bool
    {
        $terkirim = self::kirimPengingatSemuaUser($force);
        return $terkirim > 0;
    }

    public static function kirimLaporanHarianTelegram(): bool
    {
        $terkirim = self::kirimLaporanSemuaUser(true);
        return $terkirim > 0;
    }
}
