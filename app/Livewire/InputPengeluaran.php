<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Dompet;
use App\Models\Kategori; // Pastikan model Kategori di-import

class InputPengeluaran extends Component
{
    public $tanggal;
    public $dompet_id; // TAMBAHAN: Properti untuk menampung pilihan dompet
    public $pengeluaran = [];
    public $total_nominal = 0;

    // Tambahkan variabel ini untuk menyimpan pesan error
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
        // Reset pesan error setiap kali kamu ngetik
        $this->pesan_error = null;

        if (str_starts_with($property, 'pengeluaran.') && str_ends_with($property, '.nominal')) {
            $index = explode('.', $property)[1];

            if ($value !== '') {
                // Ambil nilai mentahnya dulu sebelum dibersihkan
                $nilai_mentah = (int) $value;
                $nominal_bersih = abs($nilai_mentah);

                // Cek apakah nilainya minus, nol, atau lebih dari 1 miliar
                if ($nilai_mentah <= 0 || $nominal_bersih > 1000000000) {

                    // Memicu pesan ledekan!
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
        // 1. Validasi untuk memastikan tidak ada kolom yang terlewat
        $this->validate([
            'dompet_id' => 'required', // TAMBAHAN: Validasi wajib pilih dompet
            'tanggal' => 'required|date',
            'pengeluaran.*.kategori_id' => 'required',
            'pengeluaran.*.nominal' => 'required|numeric|min:1',
        ], [
            'dompet_id.required' => 'Pilih sumber dana/dompet dulu wle😝!', // TAMBAHAN: Pesan error
            'pengeluaran.*.kategori_id.required' => 'Pilih kategori dulu, ya!',
            'pengeluaran.*.nominal.required' => 'Nominal tidak boleh kosong!'
        ]);

        // 2. Looping data array dan simpan ke database satu per satu
        foreach ($this->pengeluaran as $item) {
            Pengeluaran::create([
                'dompet_id' => $this->dompet_id, // TAMBAHAN: Simpan ID dompet ke tabel pengeluaran
                'tanggal' => $this->tanggal,
                'kategori_id' => $item['kategori_id'],
                'nominal' => $item['nominal'],
                'keterangan' => $item['keterangan']
            ]);
        }

        // 3. TAMBAHAN: Potong saldo dompet berdasarkan TOTAL NOMINAL semua baris
        $dompet = Dompet::find($this->dompet_id);
        if ($dompet) {
            $dompet->decrement('saldo', $this->total_nominal);
        }

        // 4. Reset form kembali menjadi 1 baris kosong setelah berhasil simpan
        $this->pengeluaran = [];
        $this->tambahBaris();
        $this->hitungTotal();
        $this->reset('dompet_id'); // TAMBAHAN: Reset pilihan dompet ke kosong

        // 5. Munculkan pesan sukses
        session()->flash('sukses', 'Mantap! Pengeluaran berhasil dicatat dan saldo dompet otomatis terpotong.');
    }

    public function render()
    {
        // TAMBAHAN: Kirim variabel daftarDompet agar bisa dipakai di form select (Blade)
        return view('livewire.input-pengeluaran', [
            'daftarDompet' => Dompet::all(),
            'daftarKategori' => Kategori::all() // Pastikan model Kategori di-import
        ]);
    }
}
