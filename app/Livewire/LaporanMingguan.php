<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use App\Models\Dompet;
use App\Models\Pemasukan;
use App\Models\Pengaturan;

class LaporanMingguan extends Component
{
    public $tgl_mulai;
    public $tgl_selesai;
    public $batas_boros = 500000;
    public $filter_dompet = ''; // TAMBAHAN: Variabel untuk menampung pilihan dompet

    public function mount()
    {
        $this->tgl_mulai = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->tgl_selesai = Carbon::now()->format('Y-m-d');

        $pengaturan = Pengaturan::firstOrCreate([], [
            'batas_mingguan' => 500000,
            'batas_bulanan' => 1000000,
            'batas_tahunan' => 5000000
        ]);
        $this->batas_boros = $pengaturan->batas_mingguan;
    }

    public function updatedBatasBoros($value)
    {
        $pengaturan = Pengaturan::first();
        if ($pengaturan) {
            $pengaturan->update([
                'batas_mingguan' => (int) $value
            ]);
        }
    }

    public function render()
    {
        // 1. Ambil Pengeluaran (Terapkan filter dompet jika ada)
        $laporan = Pengeluaran::with(['kategori', 'dompet']) // Tambah 'dompet' agar namanya bisa dipanggil di tabel view
            ->whereBetween('tanggal', [$this->tgl_mulai, $this->tgl_selesai])
            ->when($this->filter_dompet, function ($query) {
                // Hanya berjalan kalau filter_dompet ada isinya
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        $total_pengeluaran = $laporan->sum('nominal');

        // 2. Ambil Pemasukan di rentang tanggal yang sama (Terapkan filter dompet jika ada)
        $total_pemasukan = Pemasukan::whereBetween('tanggal', [$this->tgl_mulai, $this->tgl_selesai])
            ->when($this->filter_dompet, function ($query) {
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->sum('nominal');

        // 3. Kalkulasi (Arus kas bersih pada rentang waktu tersebut)
        $sisa_saldo = $total_pemasukan - $total_pengeluaran;
        $status_boros = $total_pengeluaran > $this->batas_boros ? 'BOROS! 😭' : 'AMAN 👍';

        return view('livewire.laporan-mingguan', [
            'laporan' => $laporan,
            'total_pengeluaran' => $total_pengeluaran,
            'total_pemasukan' => $total_pemasukan,
            'sisa_saldo' => $sisa_saldo,
            'status_boros' => $status_boros,
            'daftarDompet' => Dompet::all() // TAMBAHAN: Kirim ke view untuk opsi dropdown
        ]);
    }
}
