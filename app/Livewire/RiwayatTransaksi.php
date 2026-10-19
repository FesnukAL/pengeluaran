<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use App\Models\Dompet;
use Carbon\Carbon;

class RiwayatTransaksi extends Component
{
    public $bulan;
    public $tahun;

    // Variabel penampung fitur Edit
    public $is_edit = false;
    public $tipe_edit; // 'pemasukan' atau 'pengeluaran'
    public $selected_id;
    public $edit_tanggal;
    public $edit_kategori_id;
    public $edit_nominal;
    public $edit_keterangan;

    public function mount()
    {
        $this->bulan = Carbon::now()->format('m');
        $this->tahun = Carbon::now()->format('Y');
    }

    // 1. Aksi ketika tombol Edit diklik
    public function editAction($id, $tipe)
    {
        $this->selected_id = $id;
        $this->tipe_edit = $tipe;

        if ($tipe === 'pengeluaran') {
            $data = Pengeluaran::findOrFail($id);
            $this->edit_kategori_id = $data->kategori_id;
        } else {
            $data = Pemasukan::findOrFail($id);
            $this->edit_kategori_id = null;
        }

        $this->edit_tanggal = $data->tanggal;
        $this->edit_nominal = $data->nominal;
        $this->edit_keterangan = $data->keterangan;

        $this->is_edit = true;
    }

    // 2. Eksekusi Simpan Perubahan (SUDAH DIPERBAIKI SINKRONISASI SALDONYA)
    public function update()
    {
        $this->validate([
            'edit_tanggal' => 'required|date',
            'edit_nominal' => 'required|numeric|min:1',
            'edit_keterangan' => 'required',
        ]);

        if ($this->tipe_edit === 'pengeluaran') {
            $this->validate(['edit_kategori_id' => 'required']);
            $data = Pengeluaran::findOrFail($this->selected_id);

            // Hitung selisih nominal edit vs lama
            $selisih = $this->edit_nominal - $data->nominal;

            // Potong/Tambah saldo dompet sesuai selisih pengeluaran
            if ($data->dompet_id) {
                Dompet::where('id', $data->dompet_id)->decrement('saldo', $selisih);
            }

            $data->update([
                'tanggal' => $this->edit_tanggal,
                'kategori_id' => $this->edit_kategori_id,
                'nominal' => $this->edit_nominal,
                'keterangan' => $this->edit_keterangan,
            ]);
        } else {
            $data = Pemasukan::findOrFail($this->selected_id);

            // Hitung selisih nominal edit vs lama
            $selisih = $this->edit_nominal - $data->nominal;

            // Tambah/Kurangi saldo dompet sesuai selisih pemasukan
            if ($data->dompet_id) {
                Dompet::where('id', $data->dompet_id)->increment('saldo', $selisih);
            }

            $data->update([
                'tanggal' => $this->edit_tanggal,
                'nominal' => $this->edit_nominal,
                'keterangan' => $this->edit_keterangan,
            ]);
        }

        $this->is_edit = false;
        session()->flash('sukses', 'Mantap! Transaksi diperbarui dan saldo dompet otomatis disesuaikan.');
    }

    // 3. Eksekusi Hapus Data (SUDAH DIPERBAIKI SINKRONISASI SALDONYA)
    public function hapus($id, $tipe)
    {
        if ($tipe === 'pengeluaran') {
            $data = Pengeluaran::findOrFail($id);

            // Kembalikan uang ke dompet karena pengeluaran batal
            if ($data->dompet_id) {
                Dompet::where('id', $data->dompet_id)->increment('saldo', $data->nominal);
            }

            $data->delete();
        } else {
            $data = Pemasukan::findOrFail($id);

            // Tarik balik uang dari dompet karena pemasukan batal
            if ($data->dompet_id) {
                Dompet::where('id', $data->dompet_id)->decrement('saldo', $data->nominal);
            }

            $data->delete();
        }

        session()->flash('sukses', 'Transaksi dihapus dan saldo berhasil dikembalikan ke dompet!');
    }

    public function batalEdit()
    {
        $this->is_edit = false;
    }

    public function render()
    {
        // Ambil data pengeluaran pada bulan & tahun terpilih, beri label tipe
        $pengeluaran = Pengeluaran::with('kategori')
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'pengeluaran';
                $item->nama_tipe = $item->kategori->nama_kategori ?? 'Tanpa Kategori';
                return $item;
            });

        // Ambil data pemasukan pada bulan & tahun terpilih, beri label tipe
        $pemasukan = Pemasukan::whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'pemasukan';
                $item->nama_tipe = 'Uang Masuk / Pemasukan';
                return $item;
            });

        // Gabungkan kedua koleksi lalu urutkan berdasarkan tanggal terbaru
        $semua_transaksi = $pengeluaran->concat($pemasukan)->sortByDesc('tanggal')->values();

        return view('livewire.riwayat-transaksi', [
            'transaksi' => $semua_transaksi
        ]);
    }
}
