<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dompet;
use Illuminate\Support\Facades\Auth;

class ManajemenDompet extends Component
{
    public $nama;
    public $saldo = 0;

    public function simpanDompet()
    {
        $this->validate([
            'nama'  => 'required|string|max:255',
            'saldo' => 'required|numeric|min:0',
        ], [
            'nama.required'  => 'Nama dompet wajib diisi.',
            'saldo.required' => 'Saldo awal wajib diisi (minimal 0).',
        ]);

        try {
            Dompet::create([
                'user_id' => Auth::id(),
                'nama'    => $this->nama,
                'saldo'   => $this->saldo,
            ]);

            $this->reset(['nama', 'saldo']);
            session()->flash('success', 'Dompet baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan dompet: ' . $e->getMessage());
        }
    }

    public function hapusDompet($id)
    {
        $dompet = Dompet::where('user_id', Auth::id())->find($id);
        if ($dompet) {
            $dompet->delete();
            session()->flash('success', 'Dompet berhasil dihapus!');
        }
    }

    public function render()
    {
        $dompets = Dompet::where('user_id', Auth::id())->latest()->get();

        return view('livewire.manajemen-dompet', [
            'dompets' => $dompets
        ]);
    }
}
