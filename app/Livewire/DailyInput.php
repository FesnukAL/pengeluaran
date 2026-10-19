<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;

class DailyInput extends Component
{
    public $kegiatan_baru;
    public $prioritas = 'Biasa'; // TAMBAHAN: Nilai default dropdown
    public $kegiatan_terpilih = [];
    public $konfirmasi_simpan = false; // Flag untuk mendeteksi mode konfirmasi

    public function tambah()
    {
        // TAMBAHAN: Masukkan prioritas ke dalam validasi
        $this->validate([
            'kegiatan_baru' => 'required|min:3',
            'prioritas' => 'required|string'
        ]);

        Todo::create([
            'kegiatan' => $this->kegiatan_baru,
            'prioritas' => $this->prioritas, // TAMBAHAN: Simpan data prioritas ke DB
            'is_selesai' => false,
            'is_ditutup' => false
        ]);

        // Kosongkan form teks dan kembalikan prioritas ke opsi default
        $this->kegiatan_baru = '';
        $this->prioritas = 'Biasa';
    }

    // Fungsi pengecekan awal sebelum simpan harian
    public function cekDanSimpan()
    {
        $semuaTaskHariIni = Todo::where('is_ditutup', false)->get();
        $totalTask = $semuaTaskHariIni->count();

        if ($totalTask == 0) {
            session()->flash('error', 'Tidak ada kegiatan untuk ditutup.');
            return;
        }

        // Jika jumlah dicentang kurang dari total task yang ada, pemicu konfirmasi menyala
        if (count($this->kegiatan_terpilih) < $totalTask) {
            $this->konfirmasi_simpan = true;
        } else {
            // Jika sudah dicentang semua, langsung eksekusi tanpa tanya-tanya
            $this->eksekusiSimpan();
        }
    }

    // Jika user klik Batal, kembalikan tampilan tulisan list harian seperti semula
    public function batalSimpan()
    {
        $this->konfirmasi_simpan = false;
    }

    // Fungsi final untuk merekam data ke database
    public function eksekusiSimpan()
    {
        $semuaTaskHariIni = Todo::where('is_ditutup', false)->get();

        foreach ($semuaTaskHariIni as $task) {
            $task->update([
                'is_selesai' => in_array($task->id, $this->kegiatan_terpilih), // Tetap rekam status centang aslinya
                'is_ditutup' => true // Semua otomatis ditutup masuk arsip report
            ]);
        }

        $this->kegiatan_terpilih = [];
        $this->konfirmasi_simpan = false; // Reset status konfirmasi
        session()->flash('sukses', 'Kegiatan hari ini berhasil ditutup dan dipindah ke Laporan!');
    }

    public function render()
    {
        return view('livewire.daily-input', [
            'todos' => Todo::where('is_ditutup', false)->latest()->get()
        ]);
    }
}
