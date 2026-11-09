<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use App\Models\Dompet;
use App\Models\Pemasukan;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Auth;

class LaporanMingguan extends Component
{
    public $tgl_mulai;
    public $tgl_selesai;
    public $batas_boros = 500000;
    public $filter_dompet = '';

    public function mount()
    {
        $userId = Auth::id();
        $this->tgl_mulai = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->tgl_selesai = Carbon::now()->format('Y-m-d');

        $pengaturan = Pengaturan::firstOrCreate(['user_id' => $userId], [
            'batas_mingguan' => 500000,
            'batas_bulanan'  => 1000000,
            'batas_tahunan'  => 5000000
        ]);
        $this->batas_boros = $pengaturan->batas_mingguan;
    }

    public function updatedBatasBoros($value)
    {
        $userId = Auth::id();
        $pengaturan = Pengaturan::where('user_id', $userId)->first();
        if ($pengaturan) {
            $pengaturan->update([
                'batas_mingguan' => (int) $value
            ]);
        }
    }

    public function render()
    {
        $userId = Auth::id();

        // 1. Ambil Pengeluaran milik user aktif
        $laporan = Pengeluaran::with(['kategori', 'dompet'])
            ->where('user_id', $userId)
            ->whereBetween('tanggal', [$this->tgl_mulai, $this->tgl_selesai])
            ->when($this->filter_dompet, function ($query) {
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        $total_pengeluaran = $laporan->sum('nominal');

        // 2. Ambil Pemasukan milik user aktif
        $total_pemasukan = Pemasukan::where('user_id', $userId)
            ->whereBetween('tanggal', [$this->tgl_mulai, $this->tgl_selesai])
            ->when($this->filter_dompet, function ($query) {
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->sum('nominal');

        $sisa_saldo = $total_pemasukan - $total_pengeluaran;
        $status_boros = $total_pengeluaran > $this->batas_boros ? 'BOROS! 😭' : 'AMAN 👍';

        return view('livewire.laporan-mingguan', [
            'laporan'           => $laporan,
            'total_pengeluaran' => $total_pengeluaran,
            'total_pemasukan'   => $total_pemasukan,
            'sisa_saldo'        => $sisa_saldo,
            'status_boros'      => $status_boros,
            'daftarDompet'      => Dompet::where('user_id', $userId)->get()
        ]);
    }
}
