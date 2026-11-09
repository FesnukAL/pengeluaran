<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DailyInput extends Component
{
    public $kegiatan_baru;
    public $prioritas = 'Biasa';
    public $kegiatan_terpilih = [];
    public $konfirmasi_simpan = false;

    public function tambah()
    {
        $this->validate([
            'kegiatan_baru' => 'required|min:3',
            'prioritas'     => 'required|string'
        ]);

        $data = [
            'user_id'    => Auth::id(),
            'kegiatan'   => $this->kegiatan_baru,
            'is_selesai' => false,
            'is_ditutup' => false,
        ];

        if (Schema::hasColumn('todos', 'prioritas')) {
            $data['prioritas'] = $this->prioritas;
        }

        Todo::create($data);

        $this->kegiatan_baru = '';
        $this->prioritas = 'Biasa';
    }

    public function cekDanSimpan()
    {
        $userId = Auth::id();
        $semuaTaskHariIni = Todo::where('user_id', $userId)->where('is_ditutup', false)->get();
        $totalTask = $semuaTaskHariIni->count();

        if ($totalTask == 0) {
            session()->flash('error', 'Tidak ada kegiatan untuk ditutup.');
            return;
        }

        if (count($this->kegiatan_terpilih) < $totalTask) {
            $this->konfirmasi_simpan = true;
        } else {
            $this->eksekusiSimpan();
        }
    }

    public function batalSimpan()
    {
        $this->konfirmasi_simpan = false;
    }

    public function eksekusiSimpan()
    {
        $userId = Auth::id();
        $semuaTaskHariIni = Todo::where('user_id', $userId)->where('is_ditutup', false)->get();

        foreach ($semuaTaskHariIni as $task) {
            $task->update([
                'is_selesai' => in_array($task->id, $this->kegiatan_terpilih),
                'is_ditutup' => true
            ]);
        }

        $this->kegiatan_terpilih = [];
        $this->konfirmasi_simpan = false;
        session()->flash('sukses', 'Kegiatan hari ini berhasil ditutup dan dipindah ke Laporan!');
    }

    public function render()
    {
        $userId = Auth::id();
        return view('livewire.daily-input', [
            'todos' => Todo::where('user_id', $userId)->where('is_ditutup', false)->latest()->get()
        ]);
    }
}
