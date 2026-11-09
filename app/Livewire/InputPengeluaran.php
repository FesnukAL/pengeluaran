<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Dompet;
use App\Models\Kategori;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InputPengeluaran extends Component
{
    public $tanggal;
    public $dompet_id;
    public $pengeluaran = [];
    public $total_nominal = 0;
    public $pesan_error = null;

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->tambahBaris();
    }

    public function tambahBaris()
    {
        $this->pengeluaran[] = [
            'kategori_id' => '',
            'nominal' => '',
            'keterangan' => ''
        ];
    }

    public function hapusBaris($index)
    {
        unset($this->pengeluaran[$index]);
        $this->pengeluaran = array_values($this->pengeluaran);
        $this->hitungTotal();
    }

    public function updated($property, $value)
    {
        $this->pesan_error = null;

        if (str_starts_with($property, 'pengeluaran.') && str_ends_with($property, '.nominal')) {
            $index = explode('.', $property)[1];

            if ($value !== '') {
                $nilai_mentah = (int) $value;
                $nominal_bersih = abs($nilai_mentah);

                if ($nilai_mentah <= 0 || $nominal_bersih > 1000000000) {
                    $this->pesan_error = "Wle gabisa input mines dan 1miliar wle😝";

                    if ($nilai_mentah <= 0) {
                        $this->pengeluaran[$index]['nominal'] = '';
                    } else {
                        $this->pengeluaran[$index]['nominal'] = 1000000000;
                    }
                } else {
                    $this->pengeluaran[$index]['nominal'] = $nominal_bersih;
                }
            }
        }

        $this->hitungTotal();
    }

    public function hitungTotal()
    {
        $this->total_nominal = 0;
        foreach ($this->pengeluaran as $item) {
            $this->total_nominal += (int) $item['nominal'];
        }
    }

    public function simpan()
    {
        $userId = Auth::id();

        // 1. Validasi kepemilikan dompet dan kategori milik user aktif
        $this->validate([
            'dompet_id' => [
                'required',
                Rule::exists('dompets', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'tanggal' => 'required|date',
            'pengeluaran.*.kategori_id' => [
                'required',
                Rule::exists('kategoris', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'pengeluaran.*.nominal' => 'required|numeric|min:1',
        ], [
            'dompet_id.required' => 'Pilih sumber dana/dompet dulu wle😝!',
            'dompet_id.exists' => 'Dompet yang dipilih tidak valid!',
            'pengeluaran.*.kategori_id.required' => 'Pilih kategori dulu, ya!',
            'pengeluaran.*.kategori_id.exists' => 'Kategori yang dipilih tidak valid!',
            'pengeluaran.*.nominal.required' => 'Nominal tidak boleh kosong!'
        ]);

        // 2. Transaksi Database dengan Lock agar saldo konsisten
        DB::transaction(function () use ($userId) {
            $dompet = Dompet::where('user_id', $userId)->lockForUpdate()->find($this->dompet_id);
            if ($dompet) {
                foreach ($this->pengeluaran as $item) {
                    Pengeluaran::create([
                        'user_id' => $userId,
                        'dompet_id' => $this->dompet_id,
                        'tanggal' => $this->tanggal,
                        'kategori_id' => $item['kategori_id'],
                        'nominal' => $item['nominal'],
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }

                $dompet->decrement('saldo', $this->total_nominal);
            }
        });

        // 3. Reset form
        $this->pengeluaran = [];
        $this->tambahBaris();
        $this->hitungTotal();
        $this->reset('dompet_id');

        session()->flash('sukses', 'Mantap! Pengeluaran berhasil dicatat dan saldo dompet otomatis terpotong.');
    }

    public function render()
    {
        $userId = Auth::id();
        return view('livewire.input-pengeluaran', [
            'daftarDompet' => Dompet::where('user_id', $userId)->get(),
            'daftarKategori' => Kategori::where('user_id', $userId)->get()
        ]);
    }
}
