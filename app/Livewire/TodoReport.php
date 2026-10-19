<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;
use Carbon\Carbon;

class TodoReport extends Component
{
    public $tgl_mulai;
    public $tgl_selesai;

    // Fungsi untuk membersihkan filter
    public function resetFilter()
    {
        $this->tgl_mulai = null;
        $this->tgl_selesai = null;
    }

    // Fungsi menghapus satu baris kegiatan tertentu
    public function hapusTask($id)
    {
        Todo::where('id', $id)->delete();
        session()->flash('sukses', 'Kegiatan berhasil dihapus dari laporan.');
    }

    // Fungsi menghapus seluruh laporan di tanggal tertentu
    public function hapusPerTanggal($tanggal)
    {
        Todo::where('is_ditutup', true)
            ->whereDate('updated_at', $tanggal)
            ->delete();

        session()->flash('sukses', 'Seluruh laporan pada tanggal tersebut berhasil dibersihkan.');
    }

    public function render()
    {
        // Query dasar: Ambil data yang sudah masuk arsip laporan
        $query = Todo::where('is_ditutup', true);

        // Kondisi jika filter tanggal mulai diisi
        if ($this->tgl_mulai) {
            $query->whereDate('updated_at', '>=', $this->tgl_mulai);
        }

        // Kondisi jika filter tanggal selesai diisi
        if ($this->tgl_selesai) {
            $query->whereDate('updated_at', '<=', $this->tgl_selesai);
        }

        $allClosedTodos = $query->get();
        $totalTask = $allClosedTodos->count();

        // Hitung ulang statistik berdasarkan data yang sudah terfilter
        $totalSelesai = $allClosedTodos->where('is_selesai', true)->count();
        $totalTerlewat = $allClosedTodos->where('is_selesai', false)->count();

        $persenSelesai = $totalTask > 0 ? round(($totalSelesai / $totalTask) * 100, 1) : 0;
        $persenTerlewat = $totalTask > 0 ? round(($totalTerlewat / $totalTask) * 100, 1) : 0;

        // Kelompokkan hasil akhir data berdasarkan tanggal
        $dataLaporan = $allClosedTodos->sortByDesc('updated_at')->groupBy(function ($date) {
            return Carbon::parse($date->updated_at)->format('Y-m-d');
        });

        return view('livewire.todo-report', [
            'laporan' => $dataLaporan,
            'totalTask' => $totalTask,
            'persenSelesai' => $persenSelesai,
            'persenTerlewat' => $persenTerlewat
        ]);
    }
}
