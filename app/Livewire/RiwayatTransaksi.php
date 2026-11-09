<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use App\Models\Dompet;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function editAction($id, $tipe)
    {
        $userId = Auth::id();
        $this->selected_id = $id;
        $this->tipe_edit = $tipe;

        if ($tipe === 'pengeluaran') {
            $data = Pengeluaran::where('user_id', $userId)->findOrFail($id);
            $this->edit_kategori_id = $data->kategori_id;
        } else {
            $data = Pemasukan::where('user_id', $userId)->findOrFail($id);
            $this->edit_kategori_id = null;
        }

        $this->edit_tanggal = $data->tanggal;
        $this->edit_nominal = $data->nominal;
        $this->edit_keterangan = $data->keterangan;

        $this->is_edit = true;
    }

    public function update()
    {
        $userId = Auth::id();

        $this->validate([
            'edit_tanggal'    => 'required|date',
            'edit_nominal'    => 'required|numeric|min:1',
            'edit_keterangan' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($userId) {
            if ($this->tipe_edit === 'pengeluaran') {
                $this->validate(['edit_kategori_id' => 'required']);
                $data = Pengeluaran::where('user_id', $userId)->lockForUpdate()->findOrFail($this->selected_id);

                $selisih = $this->edit_nominal - $data->nominal;

                if ($data->dompet_id) {
                    $dompet = Dompet::where('user_id', $userId)->lockForUpdate()->find($data->dompet_id);
                    if ($dompet) {
                        $dompet->decrement('saldo', $selisih);
                    }
                }

                $data->update([
                    'tanggal'     => $this->edit_tanggal,
                    'kategori_id' => $this->edit_kategori_id,
                    'nominal'     => $this->edit_nominal,
                    'keterangan'  => $this->edit_keterangan,
                ]);
            } else {
                $data = Pemasukan::where('user_id', $userId)->lockForUpdate()->findOrFail($this->selected_id);

                $selisih = $this->edit_nominal - $data->nominal;

                if ($data->dompet_id) {
                    $dompet = Dompet::where('user_id', $userId)->lockForUpdate()->find($data->dompet_id);
                    if ($dompet) {
                        $dompet->increment('saldo', $selisih);
                    }
                }

                $data->update([
                    'tanggal'    => $this->edit_tanggal,
                    'nominal'    => $this->edit_nominal,
                    'keterangan' => $this->edit_keterangan,
                ]);
            }
        });

        $this->is_edit = false;
        session()->flash('sukses', 'Mantap! Transaksi diperbarui dan saldo dompet otomatis disesuaikan.');
    }

    public function hapus($id, $tipe)
    {
        $userId = Auth::id();

        DB::transaction(function () use ($id, $tipe, $userId) {
            if ($tipe === 'pengeluaran') {
                $data = Pengeluaran::where('user_id', $userId)->lockForUpdate()->findOrFail($id);

                if ($data->dompet_id) {
                    $dompet = Dompet::where('user_id', $userId)->lockForUpdate()->find($data->dompet_id);
                    if ($dompet) {
                        $dompet->increment('saldo', $data->nominal);
                    }
                }

                $data->delete();
            } else {
                $data = Pemasukan::where('user_id', $userId)->lockForUpdate()->findOrFail($id);

                if ($data->dompet_id) {
                    $dompet = Dompet::where('user_id', $userId)->lockForUpdate()->find($data->dompet_id);
                    if ($dompet) {
                        $dompet->decrement('saldo', $data->nominal);
                    }
                }

                $data->delete();
            }
        });

        session()->flash('sukses', 'Transaksi dihapus dan saldo berhasil dikembalikan ke dompet!');
    }

    public function batalEdit()
    {
        $this->is_edit = false;
    }

    public function render()
    {
        $userId = Auth::id();

        $pengeluaran = Pengeluaran::with(['kategori', 'dompet'])
            ->where('user_id', $userId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'pengeluaran';
                $nama_dompet = $item->dompet->nama ?? 'Tanpa Dompet';
                $item->nama_tipe = ($item->kategori->nama_kategori ?? 'Tanpa Kategori') . " ($nama_dompet)";
                return $item;
            });

        $pemasukan = Pemasukan::with('dompet')
            ->where('user_id', $userId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'pemasukan';
                $nama_dompet = $item->dompet->nama ?? 'Tanpa Dompet';
                $item->nama_tipe = "Pemasukan ($nama_dompet)";
                return $item;
            });

        $semua_transaksi = $pengeluaran->concat($pemasukan)->sortByDesc('tanggal')->values();

        return view('livewire.riwayat-transaksi', [
            'transaksi' => $semua_transaksi,
            'kategoris' => Kategori::where('user_id', $userId)->orderBy('nama_kategori')->get(),
        ]);
    }
}
