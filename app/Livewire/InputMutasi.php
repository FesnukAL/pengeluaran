<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dompet;
use App\Models\Mutasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InputMutasi extends Component
{
    public $tanggal;
    public $dompet_asal_id = '';
    public $dompet_tujuan_id = '';
    public $nominal;
    public $keterangan;

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    public function simpanMutasi()
    {
        $userId = Auth::id();

        // 1. Validasi Input
        $this->validate([
            'tanggal'          => 'required|date',
            'dompet_asal_id'   => [
                'required',
                'different:dompet_tujuan_id',
                Rule::exists('dompets', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'dompet_tujuan_id' => [
                'required',
                Rule::exists('dompets', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'nominal'          => 'required|integer|min:1',
            'keterangan'       => 'nullable|string|max:255',
        ], [
            'dompet_asal_id.different' => 'Dompet Asal dan Dompet Tujuan tidak boleh sama!',
            'dompet_asal_id.exists'    => 'Dompet asal tidak valid!',
            'dompet_tujuan_id.exists'  => 'Dompet tujuan tidak valid!',
        ]);

        try {
            // 2. Transaksi Database dengan Lock
            DB::transaction(function () use ($userId) {
                $dompetAsal = Dompet::where('user_id', $userId)->lockForUpdate()->findOrFail($this->dompet_asal_id);
                $dompetTujuan = Dompet::where('user_id', $userId)->lockForUpdate()->findOrFail($this->dompet_tujuan_id);

                if ($dompetAsal->saldo < $this->nominal) {
                    throw new \Exception('Saldo dompet asal tidak mencukupi untuk melakukan transfer!');
                }

                // Catat di tabel mutasis
                Mutasi::create([
                    'user_id'          => $userId,
                    'tanggal'          => $this->tanggal,
                    'dompet_asal_id'   => $this->dompet_asal_id,
                    'dompet_tujuan_id' => $this->dompet_tujuan_id,
                    'nominal'          => $this->nominal,
                    'keterangan'       => $this->keterangan,
                ]);

                // Kurangi saldo asal & tambah saldo tujuan
                $dompetAsal->decrement('saldo', $this->nominal);
                $dompetTujuan->increment('saldo', $this->nominal);
            });

            session()->flash('success', 'Transfer antar dompet berhasil dicatat!');
            $this->reset(['dompet_asal_id', 'dompet_tujuan_id', 'nominal', 'keterangan']);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $userId = Auth::id();
        return view('livewire.input-mutasi', [
            'dompets' => Dompet::where('user_id', $userId)->get()
        ]);
    }
}
