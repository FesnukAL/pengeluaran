<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Matkul;
use Illuminate\Support\Facades\Auth;

class ManajemenMatkul extends Component
{
    public $tipe = 'kuliah'; // 'kuliah' atau 'jaga_lab'
    public $nama_matkul;
    public $kode_matkul;
    public $dosen;
    public $sks = 2;
    public $hari = 'Senin';
    public $jam_mulai;
    public $jam_selesai;
    public $ruangan;
    public $warna = '#435ebe';
    public $catatan;

    public $edit_id = null;
    public $is_edit = false;
    public $filter_hari = '';
    public $filter_tipe = '';

    public $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function updatedTipe($value)
    {
        if (!$this->is_edit) {
            $this->warna = ($value === 'jaga_lab') ? '#198754' : '#435ebe';
        }
    }

    public function resetForm()
    {
        $this->tipe = 'kuliah';
        $this->nama_matkul = '';
        $this->kode_matkul = '';
        $this->dosen = '';
        $this->sks = 2;
        $this->hari = 'Senin';
        $this->jam_mulai = '';
        $this->jam_selesai = '';
        $this->ruangan = '';
        $this->warna = '#435ebe';
        $this->catatan = '';
        $this->edit_id = null;
        $this->is_edit = false;
    }

    public function simpan()
    {
        $userId = Auth::id();

        $this->validate([
            'tipe' => 'required|in:kuliah,jaga_lab',
            'nama_matkul' => 'required|min:2',
            'hari' => 'required',
            'sks' => 'nullable|numeric|min:0',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
        ], [
            'nama_matkul.required' => $this->tipe === 'jaga_lab' ? 'Nama Lab / Sesi jaga wajib diisi!' : 'Nama mata kuliah wajib diisi!',
            'hari.required' => 'Pilih hari pelaksanaan!',
        ]);

        if ($this->is_edit && $this->edit_id) {
            $matkul = Matkul::where('user_id', $userId)->findOrFail($this->edit_id);
            $matkul->update([
                'tipe' => $this->tipe,
                'nama_matkul' => $this->nama_matkul,
                'kode_matkul' => $this->kode_matkul,
                'dosen' => $this->dosen,
                'sks' => $this->sks ?: 0,
                'hari' => $this->hari,
                'jam_mulai' => $this->jam_mulai ?: null,
                'jam_selesai' => $this->jam_selesai ?: null,
                'ruangan' => $this->ruangan,
                'warna' => $this->warna ?: ($this->tipe === 'jaga_lab' ? '#198754' : '#435ebe'),
                'catatan' => $this->catatan,
            ]);
            $label = $this->tipe === 'jaga_lab' ? 'Jadwal Jaga Lab' : 'Mata kuliah';
            session()->flash('sukses', "{$label} berhasil diperbarui!");
        } else {
            Matkul::create([
                'user_id' => $userId,
                'tipe' => $this->tipe,
                'nama_matkul' => $this->nama_matkul,
                'kode_matkul' => $this->kode_matkul,
                'dosen' => $this->dosen,
                'sks' => $this->sks ?: 0,
                'hari' => $this->hari,
                'jam_mulai' => $this->jam_mulai ?: null,
                'jam_selesai' => $this->jam_selesai ?: null,
                'ruangan' => $this->ruangan,
                'warna' => $this->warna ?: ($this->tipe === 'jaga_lab' ? '#198754' : '#435ebe'),
                'catatan' => $this->catatan,
            ]);
            $label = $this->tipe === 'jaga_lab' ? 'Jadwal Jaga Lab baru' : 'Mata kuliah baru';
            session()->flash('sukses', "{$label} berhasil ditambahkan!");
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $matkul = Matkul::where('user_id', Auth::id())->findOrFail($id);
        $this->edit_id = $matkul->id;
        $this->tipe = $matkul->tipe ?: 'kuliah';
        $this->nama_matkul = $matkul->nama_matkul;
        $this->kode_matkul = $matkul->kode_matkul;
        $this->dosen = $matkul->dosen;
        $this->sks = $matkul->sks;
        $this->hari = $matkul->hari;
        $this->jam_mulai = $matkul->jam_mulai ? substr($matkul->jam_mulai, 0, 5) : '';
        $this->jam_selesai = $matkul->jam_selesai ? substr($matkul->jam_selesai, 0, 5) : '';
        $this->ruangan = $matkul->ruangan;
        $this->warna = $matkul->warna ?: '#435ebe';
        $this->catatan = $matkul->catatan;
        $this->is_edit = true;
    }

    public function hapus($id)
    {
        $matkul = Matkul::where('user_id', Auth::id())->findOrFail($id);
        $label = $matkul->tipe === 'jaga_lab' ? 'Jadwal Jaga Lab' : 'Mata kuliah';
        $matkul->delete();
        session()->flash('sukses', "{$label} berhasil dihapus!");
        if ($this->edit_id === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $userId = Auth::id();

        $query = Matkul::where('user_id', $userId)
            ->withCount(['tugas as tugas_aktif_count' => function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('status', '!=', 'selesai');
            }]);

        if ($this->filter_hari) {
            $query->where('hari', $this->filter_hari);
        }

        if ($this->filter_tipe) {
            $query->where('tipe', $this->filter_tipe);
        }

        $matkuls = $query->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get();

        return view('livewire.manajemen-matkul', [
            'matkuls' => $matkuls,
            'total_matkul' => Matkul::where('user_id', $userId)->where('tipe', 'kuliah')->count(),
            'total_lab' => Matkul::where('user_id', $userId)->where('tipe', 'jaga_lab')->count(),
            'total_sks' => Matkul::where('user_id', $userId)->where('tipe', 'kuliah')->sum('sks'),
        ]);
    }
}
