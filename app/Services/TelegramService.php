<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Tugas;
use App\Models\Matkul;
use Carbon\Carbon;

class TelegramService
{
    protected static function getBotToken()
    {
        return env('TELEGRAM_BOT_TOKEN');
    }

    protected static function getChatId()
    {
        return env('TELEGRAM_CHAT_ID');
    }

    /**
     * Kirim pesan teks umum ke Telegram
     */
    public static function kirimPesan(string $pesan)
    {
        $token = self::getBotToken();
        $chatId = self::getChatId();

        if (!$token || !$chatId) {
            Log::warning('Telegram bot token atau chat ID belum diatur di .env');
            return false;
        }

        try {
            $response = Http::withoutVerifying()->timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $pesan,
                'parse_mode' => 'Markdown',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Gagal mengirim pesan Telegram: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi saat tugas atau daily task baru ditambahkan
     */
    public static function notifikasiTugasDitambahkan(Tugas $tugas)
    {
        $deadlineCarbon = Carbon::parse($tugas->deadline, 'Asia/Jakarta');
        if ($tugas->tipe === 'daily') {
            $pesan = "📌 *DAILY TASK BARU DITAMBAHKAN!* 📌\n\n"
                . "⚡ *Kegiatan:* {$tugas->judul_tugas}\n"
                . "🎯 *Prioritas:* {$tugas->prioritas}\n"
                . "📅 *Target:* Hari ini (" . $deadlineCarbon->translatedFormat('d M Y') . ")\n\n"
                . "Semangat menuntaskannya hari ini, Albar! 🚀💪";
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
                . "Tugas sudah tersimpan di sistem. Jangan lupa dicicil ya! 🚀";
        }

        return self::kirimPesan($pesan);
    }

    /**
     * Kirim notifikasi saat tugas ditandai selesai
     */
    public static function notifikasiTugasSelesai(Tugas $tugas)
    {
        $waktuSelesai = Carbon::now('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB';

        if ($tugas->tipe === 'daily') {
            $pesan = "🎉 *ALHAMDULILLAH, DAILY TASK SELESAI!* 🎉\n\n"
                . "✅ *Kegiatan:* {$tugas->judul_tugas}\n"
                . "⚡ *Kategori:* Daily Task (Prioritas: {$tugas->prioritas})\n"
                . "⏰ *Waktu Diselesaikan:* {$waktuSelesai}\n\n"
                . "Mantap Albar, satu to-do harian beres! Terus produktif ya! 🔥💪";
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
                . "Kerja bagus Albar, 1 beban kuliah berkurang! Terus semangat ya! 🔥💪";
        }

        return self::kirimPesan($pesan);
    }

    /**
     * Kirim rekap pengingat harian:
     * - Jadwal HARI INI (Senin / Selasa / dst): Terpisah jelas antara Kuliah & Jaga Lab
     * - Daftar Tugas: Menampilkan deadline terdekat
     */
    public static function kirimPengingatHarian($force = false)
    {
        // Hindari spam: hanya kirim sekali dalam 4 jam kecuali force = true
        $cacheKey = 'telegram_pengingat_terakhir';
        if (!$force && Cache::has($cacheKey)) {
            return false;
        }

        $now = Carbon::now('Asia/Jakarta');
        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIni = $hariIndo[$now->format('l')] ?? $now->format('l');
        $tanggalHariIni = $now->translatedFormat('l, d F Y');

        // 1. Ambil Jadwal HARI INI (Dipisah antara Kuliah dan Jaga Lab)
        $kuliahHariIni = Matkul::where('tipe', 'kuliah')
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        $jagaLabHariIni = Matkul::where('tipe', 'jaga_lab')
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // 2. Ambil Tugas yang belum selesai
        $tugasBelumSelesai = Tugas::with('matkul')
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline', 'asc')
            ->get();

        if ($kuliahHariIni->isEmpty() && $jagaLabHariIni->isEmpty() && $tugasBelumSelesai->isEmpty()) {
            if ($force) {
                self::kirimPesan("✨ *LAPORAN HARIAN ALBAR:*\nHari ini ({$hariIni}) tidak ada jadwal kuliah maupun jaga lab, dan semua tugas sudah beres! Waktunya santai! 😎👌");
            }
            return true;
        }

        $pesan = "🔔 *PENGINGAT HARIAN ALBAR* 🔔\n"
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
            $pesan .= "\nSemangat ya, jangan lupa juga catat pengeluaran hari ini! 🚀💪";
        } else {
            $pesan .= "✨ *Tugas:* Semua tugas beres! Tidak ada deadline menanti. Mantap! 👍";
        }

        $hasil = self::kirimPesan($pesan);

        if ($hasil) {
            // Set cooldown 4 jam
            Cache::put($cacheKey, now(), now()->addHours(4));
        }

        return $hasil;
    }

    /**
     * Kirim Laporan Harian Lengkap ke Telegram:
     * - Produktivitas Daily Task hari ini (selesai & belum, persentase)
     * - Jadwal Kuliah & Jaga Lab hari ini
     * - Tugas Kuliah aktif & deadline terdekat
     */
    public static function kirimLaporanHarianTelegram()
    {
        $now = Carbon::now('Asia/Jakarta');
        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIni = $hariIndo[$now->format('l')] ?? $now->format('l');
        $tanggalFormatted = $now->translatedFormat('l, d F Y');

        // 1. Daily Task Hari Ini
        $dailyToday = Tugas::where('tipe', 'daily')
            ->whereDate('created_at', $now->toDateString())
            ->get();
        
        $totalDaily = $dailyToday->count();
        $selesaiDaily = $dailyToday->where('status', 'selesai')->count();
        $rateDaily = $totalDaily > 0 ? round(($selesaiDaily / $totalDaily) * 100) : 0;

        // 2. Jadwal Kuliah & Jaga Lab
        $kuliahHariIni = Matkul::where('tipe', 'kuliah')->where('hari', $hariIni)->orderBy('jam_mulai')->get();
        $jagaLabHariIni = Matkul::where('tipe', 'jaga_lab')->where('hari', $hariIni)->orderBy('jam_mulai')->get();

        // 3. Tugas Kuliah Belum Selesai
        $tugasKuliahBelum = Tugas::with('matkul')
            ->where('tipe', 'kuliah')
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline', 'asc')
            ->get();

        $pesan = "📊 *LAPORAN KEGIATAN HARIAN ALBAR* 📊\n"
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
        $pesan .= "Tetap konsisten dan jaga kesehatan ya! ✨💪";

        return self::kirimPesan($pesan);
    }
}
