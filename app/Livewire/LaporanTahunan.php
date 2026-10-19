<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use App\Models\Dompet;
use App\Models\Pengaturan;
use Carbon\Carbon;

class LaporanTahunan extends Component
{
    public $tahun;
    public $batas_boros = 5000000; // Default batas boros tahunan
    public $filter_dompet = ''; // TAMBAHAN: Variabel untuk filter dompet

    public function mount()
    {
        $this->tahun = Carbon::now()->format('Y');

        // Ambil data tahunan dari database
        $pengaturan = Pengaturan::firstOrCreate([], [
            'batas_mingguan' => 500000,
            'batas_bulanan' => 1000000,
            'batas_tahunan' => 5000000
        ]);
        $this->batas_boros = $pengaturan->batas_tahunan;
    }

    public function updatedBatasBoros($value)
    {
        $pengaturan = Pengaturan::first();
        if ($pengaturan) {
            $pengaturan->update([
                'batas_tahunan' => (int) $value
            ]);
        }
    }

    public function render()
    {
        $laporan_per_bulan = [];
        $total_setahun = 0;
        $nama_bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        // 1. Ambil data pengeluaran setahun cukup 1x hit ke database (lebih cepat!)
        $semua_pengeluaran = Pengeluaran::whereYear('tanggal', $this->tahun)
            ->when($this->filter_dompet, function ($query) { // Kondisi filter dompet
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->get();

        for ($i = 1; $i <= 12; $i++) {
            // Hitung total bulan ini dari koleksi data yang sudah diambil
            $total_bulan_ini = $semua_pengeluaran->filter(function ($item) use ($i) {
                return Carbon::parse($item->tanggal)->month == $i;
            })->sum('nominal');

            // Format array tetap sama persis dengan buatanmu
            $laporan_per_bulan[] = ['bulan' => $nama_bulan[$i], 'total' => $total_bulan_ini];
            $total_setahun += $total_bulan_ini;
        }

        // 2. Ambil Pemasukan Setahun Penuh + Filter Dompet
        $total_pemasukan_setahun = Pemasukan::whereYear('tanggal', $this->tahun)
            ->when($this->filter_dompet, function ($query) { // Kondisi filter dompet
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->sum('nominal');

        $sisa_saldo = $total_pemasukan_setahun - $total_setahun;

        $status_boros = $total_setahun > $this->batas_boros ? 'BOROS! 😭' : 'AMAN 👍';

        return view('livewire.laporan-tahunan', [
            'laporan_per_bulan' => $laporan_per_bulan,
            'total_setahun' => $total_setahun,
            'total_pemasukan_setahun' => $total_pemasukan_setahun,
            'sisa_saldo' => $sisa_saldo,
            'status_boros' => $status_boros,
            'daftarDompet' => Dompet::all() // TAMBAHAN: Kirim list dompet ke view tahunan
        ]);
    }
}
