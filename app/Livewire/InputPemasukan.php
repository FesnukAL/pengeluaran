<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pemasukan;
use App\Models\Dompet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InputPemasukan extends Component
{
    public $tanggal;
    public $nominal;
    public $keterangan;
    public $dompet_id;

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    public function simpan()
    {
        $userId = Auth::id();

        $this->validate([
            'tanggal'    => 'required|date',
            'nominal'    => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
            'dompet_id'  => [
                'required',
                Rule::exists('dompets', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
        ], [
            'dompet_id.required'  => 'Pilih dulu uangnya mau dimasukkan ke dompet mana!',
            'dompet_id.exists'    => 'Dompet tidak valid!',
            'nominal.min'         => 'Masa masukin uang minus atau nol wle😝',
            'keterangan.required' => 'Tulis sumber uangnya dari mana biar nggak lupa.',
        ]);

        try {
            DB::transaction(function () use ($userId) {
                Pemasukan::create([
                    'user_id'    => $userId,
                    'tanggal'    => $this->tanggal,
                    'nominal'    => $this->nominal,
                    'keterangan' => $this->keterangan,
                    'dompet_id'  => $this->dompet_id,
                ]);

                $dompet = Dompet::where('user_id', $userId)->lockForUpdate()->findOrFail($this->dompet_id);
                $dompet->increment('saldo', $this->nominal);
            });

            $this->reset(['nominal', 'keterangan', 'dompet_id']);
            session()->flash('sukses', 'Alhamdulillah! Pemasukan berhasil dicatat dan saldo dompet bertambah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $userId = Auth::id();
        return view('livewire.input-pemasukan', [
            'daftarDompet' => Dompet::where('user_id', $userId)->get()
        ]);
    }
}
