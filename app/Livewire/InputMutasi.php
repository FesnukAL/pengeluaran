<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dompet;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;

class InputMutasi extends Component
{
    public $tanggal;
    public $dompet_asal_id = '';
    public $dompet_tujuan_id = '';
    public $nominal;
    public $keterangan;

    // Method mount dipanggil pertama kali saat halaman di-load
    public function mount()
    {
        $this->tanggal = date('Y-m-d'); // Set default tanggal hari ini
    }

    public function simpanMutasi()
    {
        // 1. Validasi Input
        $this->validate([
            'tanggal'          => 'required|date',
            'dompet_asal_id'   => 'required|exists:dompets,id|different:dompet_tujuan_id',
            'dompet_tujuan_id' => 'required|exists:dompets,id',
            'nominal'          => 'required|integer|min:1',
            'keterangan'       => 'nullable|string|max:255',
        ], [
            'dompet_asal_id.different' => 'Dompet Asal dan Dompet Tujuan tidak boleh sama!',
        ]);

        try {
            // 2. Transaksi Database
            DB::transaction(function () {
                // Catat di tabel mutasis
                Mutasi::create([
                    'tanggal'          => $this->tanggal,
                    'dompet_asal_id'   => $this->dompet_asal_id,
                    'dompet_tujuan_id' => $this->dompet_tujuan_id,
                    'nominal'          => $this->nominal,
                    'keterangan'       => $this->keterangan,
                ]);

                // Kurangi saldo asal
                $dompetAsal = Dompet::lockForUpdate()->findOrFail($this->dompet_asal_id);
                $dompetAsal->decrement('saldo', $this->nominal);

                // Tambah saldo tujuan
                $dompetTujuan = Dompet::lockForUpdate()->findOrFail($this->dompet_tujuan_id);
                $dompetTujuan->increment('saldo', $this->nominal);
            });

            // 3. Beri notifikasi sukses dan reset form
            session()->flash('success', 'Transfer antar dompet berhasil dicatat!');
            $this->reset(['dompet_asal_id', 'dompet_tujuan_id', 'nominal', 'keterangan']);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Kirim data dompet ke view untuk dropdown
        return view('livewire.input-mutasi', [
            'dompets' => Dompet::all()
        ]);
    }
}
