<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dompet;
use Illuminate\Support\Facades\DB;

class ManajemenDompet extends Component
{
  public $nama;
    public $saldo = 0; // Default 0

    public function simpanDompet()
    {
        // 1. Validasi input
        $this->validate([
            'nama'  => 'required|string|max:255',
            'saldo' => 'required|numeric|min:0',
        ], [
            'nama.required'  => 'Nama dompet wajib diisi.',
            'saldo.required' => 'Saldo awal wajib diisi (minimal 0).',
        ]);

        try {
            // 2. Simpan ke database
            Dompet::create([
                'nama'  => $this->nama,
                'saldo' => $this->saldo,
            ]);

            // 3. Reset form & beri notifikasi
            $this->reset(['nama', 'saldo']);
            session()->flash('success', 'Dompet baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan dompet: ' . $e->getMessage());
        }
    }

    public function hapusDompet($id)
    {
        // Opsional: Fitur hapus dompet
        $dompet = Dompet::find($id);
        if ($dompet) {
            $dompet->delete();
            session()->flash('success', 'Dompet berhasil dihapus!');
        }
    }

    public function render()
    {
        // Ambil semua data dompet untuk ditampilkan di tabel
        $dompets = Dompet::latest()->get();
        
        return view('livewire.manajemen-dompet', [
            'dompets' => $dompets
        ]);
    }
}
