<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use App\Models\Dompet;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public $bulan;
    public $tahun;

    public function mount()
    {
        // Set default ke bulan dan tahun saat ini
        $this->bulan = Carbon::now()->format('m');
        $this->tahun = Carbon::now()->format('Y');
    }

    public function render()
    {
        $hari_ini = Carbon::today();

        // 1. Hitung pengeluaran khusus hari ini
        $pengeluaran_hari_ini = Pengeluaran::whereDate('tanggal', $hari_ini)->sum('nominal');

        // 2. Hitung total bulanan keseluruhan (Gabungan semua dompet)
        $total_pengeluaran = Pengeluaran::whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->sum('nominal');

        $total_pemasukan = Pemasukan::whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->sum('nominal');

        // Total Sisa Saldo Gabungan
        $sisa_saldo = Dompet::sum('saldo');

        // 3. REVISI UTAMA: Hitung Breakdown Pemasukan & Pengeluaran PER DOMPET
        // Kita ambil semua dompet, lalu inject total pemasukan & pengeluaran bulanan masing-masing
        $breakdown_dompet = Dompet::get()->map(function ($dompet) {

            // Hitung pemasukan bulan ini khusus dompet ini
            $dompet->pemasukan_bulan_ini = Pemasukan::where('dompet_id', $dompet->id)
                ->whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun)
                ->sum('nominal');

            // Hitung pengeluaran bulan ini khusus dompet ini
            $dompet->pengeluaran_bulan_ini = Pengeluaran::where('dompet_id', $dompet->id)
                ->whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun)
                ->sum('nominal');

            return $dompet;
        });

        // 4. REVISI RIWAYAT: Ambil daftar transaksi dengan relasi dompet agar muncul di tabel
        $pengeluaran_list = Pengeluaran::with(['kategori', 'dompet']) // Tambah eager load 'dompet'
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'pengeluaran';
                // Menampilkan Kategori + Nama Dompet, contoh: "Transportasi (Uang Tunai)"
                $nama_dompet = $item->dompet->nama ?? 'Tanpa Dompet';
                $item->nama_tipe = ($item->kategori->nama_kategori ?? 'Tanpa Kategori') . " ($nama_dompet)";
                return $item;
            });

        $pemasukan_list = Pemasukan::with('dompet') // Tambah eager load 'dompet'
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'pemasukan';
                // Menampilkan Nama Dompet pada pemasukan, contoh: "Pemasukan (Bank BRI)"
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
            'breakdown_dompet'     => $breakdown_dompet, // VARIABEL BARU
            'transaksi'            => $transaksi
        ]);
    }
}
