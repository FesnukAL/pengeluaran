<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Endpoint Webhook Cron Gratis untuk Hosting (misal dipanggil oleh cron-job.org setiap pagi/malam)
Route::get('/api/telegram-cron', function (Request $request) {
    $secret = config('services.telegram.cron_key') ?: env('TELEGRAM_CRON_KEY', 'albar-cron-secret');
    if ($request->query('key') !== $secret) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized key'], 401);
    }
    $tipe = $request->query('tipe', 'pengingat'); // 'pengingat' atau 'laporan'
    if ($tipe === 'laporan') {
        $count = \App\Services\TelegramService::kirimLaporanSemuaUser(false);
    } else {
        $count = \App\Services\TelegramService::kirimPengingatSemuaUser(false);
    }
    return response()->json([
        'status' => 'success',
        'tipe' => $tipe,
        'terkirim_ke_user' => $count,
        'message' => "Proses pengiriman {$tipe} selesai ke {$count} user aktif."
    ]);
});

// Seluruh Menu Aplikasi Membutuhkan Login (Multi-User Protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/pengeluaran/input', function () {
        return view('pengeluaran.input');
    })->name('pengeluaran.input');

    Route::get('/pemasukan/input', function () {
        return view('pemasukan.input');
    })->name('pemasukan.input');

    Route::get('/mutasi/input', function () {
        return view('mutasi.input');
    })->name('mutasi.input');

    Route::get('/transaksi/riwayat', function () {
        return view('transaksi.riwayat');
    })->name('transaksi.riwayat');

    Route::get('/dompet', function () {
        return view('dompet.index');
    })->name('dompet.index');

    Route::get('/kategori', function () {
        return view('kategori.index');
    })->name('kategori.index');

    // Laporan
    Route::get('/laporan/mingguan', function () {
        return view('laporan.mingguan');
    })->name('laporan.mingguan');

    Route::get('/laporan/bulanan', function () {
        return view('laporan.bulanan');
    })->name('laporan.bulanan');

    Route::get('/laporan/tahunan', function () {
        return view('laporan.tahunan');
    })->name('laporan.tahunan');

    // Perkuliahan & Tugas
    Route::get('/kuliah/matkul', function () {
        return view('kuliah.matkul');
    })->name('kuliah.matkul');

    Route::get('/kuliah/tugas', function () {
        return view('kuliah.tugas');
    })->name('kuliah.tugas');

    // Pengaturan Notifikasi & Bot Telegram
    Route::get('/pengaturan/telegram', function () {
        return view('pengaturan.telegram');
    })->name('pengaturan.telegram');

    // Biodata & Portofolio Pengembang
    Route::get('/biodata', function () {
        return view('biodata');
    })->name('biodata');

    // Legacy Redirects
    Route::get('/daily-input', function () {
        return redirect()->route('kuliah.tugas');
    })->name('daily.input');

    Route::get('/laporan-kegiatan', function () {
        return redirect()->route('kuliah.tugas');
    })->name('daily.report');

    // Panel Super Admin (Tracking User)
    Route::get('/admin/users', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak! Khusus Super Admin.');
        }
        return view('admin.users');
    })->name('admin.users');

    // Logout
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

require __DIR__ . '/auth.php';
