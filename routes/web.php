<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    \App\Services\TelegramService::kirimPengingatHarian();
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    \App\Services\TelegramService::kirimPengingatHarian();
    return view('dashboard');
})->name('dashboard');
Route::get('/pengeluaran/input', function () {
    return view('pengeluaran.input');
});
Route::get('/laporan/mingguan', function () {
    return view('laporan.mingguan');
});
Route::get('/laporan/tahunan', function () {
    return view('laporan.tahunan');
});
Route::get('/pemasukan/input', function () {
    return view('pemasukan.input'); // Buat file input.blade.php di views/pemasukan/ mirip yang pengeluaran
});
Route::get('/laporan/bulanan', function () {
    return view('laporan.bulanan');
});
Route::get('/transaksi/riwayat', function () {
    return view('transaksi.riwayat');
});
// Redirect rute daily lama ke menu tugas terpadu
Route::get('/daily-input', function () {
    return redirect()->route('kuliah.tugas');
})->name('daily.input');
Route::get('/laporan-kegiatan', function () {
    return redirect()->route('kuliah.tugas');
})->name('daily.report');

// Endpoint Webhook Cron Gratis untuk Hosting (misal dipanggil oleh cron-job.org setiap pagi/malam)
Route::get('/api/telegram-cron', function (\Illuminate\Http\Request $request) {
    $secret = env('TELEGRAM_CRON_KEY', 'albar-cron-secret');
    if ($request->query('key') !== $secret) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized key'], 401);
    }
    $tipe = $request->query('tipe', 'pengingat'); // 'pengingat' atau 'laporan'
    if ($tipe === 'laporan') {
        $sent = \App\Services\TelegramService::kirimLaporanHarianTelegram();
    } else {
        $sent = \App\Services\TelegramService::kirimPengingatHarian(true);
    }
    return response()->json([
        'status' => $sent ? 'success' : 'failed',
        'tipe' => $tipe,
        'message' => $sent ? 'Pesan Telegram berhasil diproses!' : 'Gagal mengirim pesan ke Telegram.'
    ]);
});

Route::get('/mutasi/input', function () {
    return view('mutasi.input');
})->name('mutasi.input');

Route::get('/dompet', function () {
    return view('dompet.index');
})->name('dompet.index');

Route::get('/kategori', function () {
    return view('kategori.index');
})->name('kategori.index');

// Menu Perkuliahan
Route::get('/kuliah/matkul', function () {
    return view('kuliah.matkul');
})->name('kuliah.matkul');

Route::get('/kuliah/tugas', function () {
    return view('kuliah.tugas');
})->name('kuliah.tugas');

require __DIR__ . '/auth.php';