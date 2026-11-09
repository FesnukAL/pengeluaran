<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use App\Models\Dompet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardStats extends Component
{
    public $bulan;
    public $tahun;

    public function mount()
    {
        $this->bulan = Carbon::now()->format('m');
        $this->tahun = Carbon::now()->format('Y');
    }

    public function render()
    {
        $userId = Auth::id();
        $hari_ini = Carbon::today();

        // 1. Hitung pengeluaran khusus hari ini milik user aktif
        $pengeluaran_hari_ini = Pengeluaran::where('user_id', $userId)
            ->whereDate('tanggal', $hari_ini)
            ->sum('nominal');

        // 2. Hitung total bulanan keseluruhan milik user aktif
        $total_pengeluaran = Pengeluaran::where('user_id', $userId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->sum('nominal');

        $total_pemasukan = Pemasukan::where('user_id', $userId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->sum('nominal');

        // Total Sisa Saldo Gabungan dompet user aktif
        $sisa_saldo = Dompet::where('user_id', $userId)->sum('saldo');

        // 3. Breakdown Pemasukan & Pengeluaran PER DOMPET milik user aktif
        $breakdown_dompet = Dompet::where('user_id', $userId)->get()->map(function ($dompet) use ($userId) {
            $dompet->pemasukan_bulan_ini = Pemasukan::where('user_id', $userId)
                ->where('dompet_id', $dompet->id)
                ->whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun)
                ->sum('nominal');

            $dompet->pengeluaran_bulan_ini = Pengeluaran::where('user_id', $userId)
                ->where('dompet_id', $dompet->id)
                ->whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun)
                ->sum('nominal');

            return $dompet;
        });

        // 4. Riwayat transaksi terbaru milik user aktif
        $pengeluaran_list = Pengeluaran::with(['kategori', 'dompet'])
            ->where('user_id', $userId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'pengeluaran';
                $nama_dompet = $item->dompet->nama ?? 'Tanpa Dompet';
                $item->nama_tipe = ($item->kategori->nama_kategori ?? 'Tanpa Kategori') . " ($nama_dompet)";
                return $item;
            });

        $pemasukan_list = Pemasukan::with('dompet')
            ->where('user_id', $userId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'pemasukan';
                $nama_dompet = $item->dompet->nama ?? 'Tanpa Dompet';
                $item->nama_tipe = "Pemasukan ($nama_dompet)";
                return $item;
            });

        $transaksi = $pengeluaran_list->concat($pemasukan_list)->sortByDesc('tanggal')->values();

        return view('livewire.dashboard-stats', [
            'pengeluaran_hari_ini' => $pengeluaran_hari_ini,
            'total_pengeluaran'    => $total_pengeluaran,
            'total_pemasukan'      => $total_pemasukan,
            'sisa_saldo'           => $sisa_saldo,
            'breakdown_dompet'     => $breakdown_dompet,
            'transaksi'            => $transaksi
        ]);
    }
}
