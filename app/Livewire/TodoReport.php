<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TodoReport extends Component
{
    public $tgl_mulai;
    public $tgl_selesai;

    public function resetFilter()
    {
        $this->tgl_mulai = null;
        $this->tgl_selesai = null;
    }

    public function hapusTask($id)
    {
        Todo::where('user_id', Auth::id())->where('id', $id)->delete();
        session()->flash('sukses', 'Kegiatan berhasil dihapus dari laporan.');
    }

    public function hapusPerTanggal($tanggal)
    {
        Todo::where('user_id', Auth::id())
            ->where('is_ditutup', true)
            ->whereDate('updated_at', $tanggal)
            ->delete();

        session()->flash('sukses', 'Seluruh laporan pada tanggal tersebut berhasil dibersihkan.');
    }

    public function render()
    {
        $userId = Auth::id();
        $query = Todo::where('user_id', $userId)->where('is_ditutup', true);

        if ($this->tgl_mulai) {
            $query->whereDate('updated_at', '>=', $this->tgl_mulai);
        }

        if ($this->tgl_selesai) {
            $query->whereDate('updated_at', '<=', $this->tgl_selesai);
        }

        $allClosedTodos = $query->get();
        $totalTask = $allClosedTodos->count();

        $totalSelesai = $allClosedTodos->where('is_selesai', true)->count();
        $totalTerlewat = $allClosedTodos->where('is_selesai', false)->count();

        $persenSelesai = $totalTask > 0 ? round(($totalSelesai / $totalTask) * 100, 1) : 0;
        $persenTerlewat = $totalTask > 0 ? round(($totalTerlewat / $totalTask) * 100, 1) : 0;

        $dataLaporan = $allClosedTodos->sortByDesc('updated_at')->groupBy(function ($date) {
            return Carbon::parse($date->updated_at)->format('Y-m-d');
        });

        return view('livewire.todo-report', [
            'laporan'        => $dataLaporan,
            'totalTask'      => $totalTask,
            'persenSelesai'  => $persenSelesai,
            'persenTerlewat' => $persenTerlewat
        ]);
    }
}
