<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pemasukan;
use App\Models\Dompet;

class InputPemasukan extends Component
{
    public $tanggal;
    public $nominal;
    public $keterangan;
    public $dompet_id; // Penampung pilihan dompet

    public function mount()
    {
        $this->tanggal = date('Y-m-d'); // Default hari ini
    }

    public function simpan()
    {
        // 1. Validasi Inputan
        $this->validate([
            'tanggal'    => 'required|date',
            'nominal'    => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
            'dompet_id'  => 'required|exists:dompets,id',
        ], [
            'dompet_id.required'  => 'Pilih dulu uangnya mau dimasukkan ke dompet mana!',
            'nominal.min'         => 'Masa masukin uang minus atau nol wle😝',
            'keterangan.required' => 'Tulis sumber uangnya dari mana biar nggak lupa.',
        ]);

        try {
            // 2. Simpan Riwayat Pemasukan ke Database
            Pemasukan::create([
                'tanggal'    => $this->tanggal,
                'nominal'    => $this->nominal,
                'keterangan' => $this->keterangan,
                'dompet_id'  => $this->dompet_id, // Simpan ID dompetnya
            ]);

            // 3. Tambahkan Saldo ke Dompet yang Dipilih
            $dompet = Dompet::findOrFail($this->dompet_id);
            $dompet->increment('saldo', $this->nominal);

            // 4. Reset Form & Beri Pesan Sukses
            $this->reset(['nominal', 'keterangan', 'dompet_id']);
            session()->flash('sukses', 'Alhamdulillah! Pemasukan berhasil dicatat dan saldo dompet bertambah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Kirim data dompet ke view untuk dropdown
        return view('livewire.input-pemasukan', [
            'daftarDompet' => Dompet::all()
        ]);
    }
}
