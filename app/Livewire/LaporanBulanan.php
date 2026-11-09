<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use App\Models\Dompet;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanBulanan extends Component
{
    public $bulan;
    public $tahun;
    public $batas_boros;
    public $filter_dompet = '';

    public function mount()
    {
        $userId = Auth::id();
        $this->bulan = Carbon::now()->format('m');
        $this->tahun = Carbon::now()->format('Y');

        $pengaturan = Pengaturan::firstOrCreate(['user_id' => $userId], [
            'batas_mingguan' => 500000,
            'batas_bulanan'  => 1000000,
            'batas_tahunan'  => 5000000
        ]);

        $this->batas_boros = $pengaturan->batas_bulanan;
    }

    public function updatedBatasBoros($value)
    {
        $userId = Auth::id();
        $pengaturan = Pengaturan::where('user_id', $userId)->first();
        if ($pengaturan) {
            $pengaturan->update([
                'batas_bulanan' => (int) $value
            ]);
        }
    }

    public function render()
    {
        $userId = Auth::id();

        // 1. Ambil data pengeluaran milik user aktif
        $laporan = Pengeluaran::with(['kategori', 'dompet'])
            ->where('user_id', $userId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->when($this->filter_dompet, function ($query) {
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        $total_pengeluaran = $laporan->sum('nominal');

        // 2. Ambil data pemasukan milik user aktif
        $total_pemasukan = Pemasukan::where('user_id', $userId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->when($this->filter_dompet, function ($query) {
                return $query->where('dompet_id', $this->filter_dompet);
            })
            ->sum('nominal');

        $sisa_saldo = $total_pemasukan - $total_pengeluaran;
        $status_boros = $total_pengeluaran > $this->batas_boros ? 'BOROS! 😭' : 'AMAN 👍';

        return view('livewire.laporan-bulanan', [
            'laporan'           => $laporan,
            'total_pengeluaran' => $total_pengeluaran,
            'total_pemasukan'   => $total_pemasukan,
            'sisa_saldo'        => $sisa_saldo,
            'status_boros'      => $status_boros,
            'daftarDompet'      => Dompet::where('user_id', $userId)->get()
        ]);
    }
}
