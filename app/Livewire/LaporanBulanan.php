<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use Carbon\Carbon;
use App\Models\Dompet;
use App\Models\Pengaturan;

class LaporanBulanan extends Component
{
    public $bulan;
    public $tahun;
    public $batas_boros;
    public $filter_dompet = ''; // TAMBAHAN: Variabel untuk mengikat pilihan dompet

    public function mount()
    {
        $this->bulan = Carbon::now()->format('m');
        $this->tahun = Carbon::now()->format('Y');

        // Ambil data pengaturan, jika kosong otomatis buatkan baris pertama dengan nilai default
        $pengaturan = Pengaturan::firstOrCreate([], [
            'batas_mingguan' => 500000,
            'batas_bulanan' => 1000000,
            'batas_tahunan' => 5000000
        ]);

        // Isi variabel batas_boros dengan angka dari database
        $this->batas_boros = $pengaturan->batas_bulanan;
    }

    // Fungsi otomatis berjalan saat kamu mengubah angka di kotak input batas_boros
    public function updatedBatasBoros($value)
    {
        $pengaturan = Pengaturan::first();
        if ($pengaturan) {
            $pengaturan->update([
                'batas_bulanan' => (int) $value
            ]);
        }
    }

    public function render()
    {
        // 1. Ambil data pengeluaran (Eager load relasi dompet & terapkan filter jika ada)
        $laporan = Pengeluaran::with(['kategori', 'dompet']) // MODIFIKASI: Load relasi 'dompet' sekaligus
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->when($this->filter_dompet, function ($query) { // TAMBAHAN: Kondisi filter dompet
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        $total_pengeluaran = $laporan->sum('nominal');

        // 2. Ambil data pemasukan (Terapkan filter dompet jika ada agar sisa saldo akurat)
        $total_pemasukan = Pemasukan::whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->when($this->filter_dompet, function ($query) { // TAMBAHAN: Kondisi filter dompet
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->sum('nominal');

        $sisa_saldo = $total_pemasukan - $total_pengeluaran;

        // Logika boros sekarang membaca dari variabel dinamis database
        $status_boros = $total_pengeluaran > $this->batas_boros ? 'BOROS! 😭' : 'AMAN 👍';

        return view('livewire.laporan-bulanan', [
            'laporan' => $laporan,
            'total_pengeluaran' => $total_pengeluaran,
            'total_pemasukan' => $total_pemasukan,
            'sisa_saldo' => $sisa_saldo,
            'status_boros' => $status_boros,
            'daftarDompet' => Dompet::all() // TAMBAHAN: Kirim list dompet ke file view
        ]);
    }
}
