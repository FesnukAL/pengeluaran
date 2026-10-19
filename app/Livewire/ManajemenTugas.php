<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tugas;
use App\Models\Matkul;
use App\Services\TelegramService;
use Carbon\Carbon;

class ManajemenTugas extends Component
{
    public $tipe = 'kuliah'; // 'kuliah' atau 'daily'
    public $matkul_id;
    public $judul_tugas;
    public $deskripsi;
    public $deadline;
    public $prioritas = 'Sedang';
    public $status = 'belum';

    public $edit_id = null;
    public $is_edit = false;

    // Filter
    public $filter_status = 'belum'; // default tampilkan yang belum selesai
    public $filter_tipe = ''; // '' = semua, 'kuliah', 'daily'
    public $filter_matkul = '';

    public function mount()
    {
        $this->deadline = Carbon::now()->addDays(2)->format('Y-m-d\TH:i');
    }

    public function resetForm()
    {
        $this->tipe = 'kuliah';
        $this->matkul_id = '';
        $this->judul_tugas = '';
        $this->deskripsi = '';
        $this->deadline = Carbon::now()->addDays(2)->format('Y-m-d\TH:i');
        $this->prioritas = 'Sedang';
        $this->status = 'belum';
        $this->edit_id = null;
        $this->is_edit = false;
    }

    public function setTipeForm($tipe)
    {
        $this->tipe = $tipe;
        if ($tipe === 'daily') {
            $this->matkul_id = '';
            $this->deadline = Carbon::today()->setTime(23, 59)->format('Y-m-d\TH:i');
        } else {
            $this->deadline = Carbon::now()->addDays(2)->format('Y-m-d\TH:i');
        }
    }

    public function simpan()
    {
        if ($this->tipe === 'daily') {
            $this->validate([
                'judul_tugas' => 'required|min:2',
                'prioritas' => 'required|in:Tinggi,Sedang,Rendah',
            ], [
                'judul_tugas.required' => 'Nama kegiatan / daily task wajib diisi!',
            ]);

            $deadlineValue = Carbon::today()->setTime(23, 59, 59);

            if ($this->is_edit && $this->edit_id) {
                $tugas = Tugas::findOrFail($this->edit_id);
                $tugas->update([
                    'tipe' => 'daily',
                    'matkul_id' => null,
                    'judul_tugas' => $this->judul_tugas,
                    'deskripsi' => $this->deskripsi,
                    'prioritas' => $this->prioritas,
                    'status' => $this->status,
                ]);
                session()->flash('sukses', 'Daily task berhasil diperbarui!');
            } else {
                $tugas = Tugas::create([
                    'tipe' => 'daily',
                    'matkul_id' => null,
                    'judul_tugas' => $this->judul_tugas,
                    'deskripsi' => $this->deskripsi,
                    'deadline' => $deadlineValue,
                    'prioritas' => $this->prioritas,
                    'status' => 'belum',
                ]);
                TelegramService::notifikasiTugasDitambahkan($tugas);
                session()->flash('sukses', 'Daily task berhasil ditambahkan & notifikasi terkirim ke Telegram!');
            }
        } else {
            $this->validate([
                'judul_tugas' => 'required|min:3',
                'deadline' => 'required',
                'prioritas' => 'required|in:Tinggi,Sedang,Rendah',
            ], [
                'judul_tugas.required' => 'Judul tugas kuliah wajib diisi!',
                'deadline.required' => 'Tanggal & waktu deadline wajib ditentukan!',
            ]);

            if ($this->is_edit && $this->edit_id) {
                $tugas = Tugas::findOrFail($this->edit_id);
                $tugas->update([
                    'tipe' => 'kuliah',
                    'matkul_id' => $this->matkul_id ?: null,
                    'judul_tugas' => $this->judul_tugas,
                    'deskripsi' => $this->deskripsi,
                    'deadline' => $this->deadline,
                    'prioritas' => $this->prioritas,
                    'status' => $this->status,
                ]);
                session()->flash('sukses', 'Tugas kuliah berhasil diperbarui!');
            } else {
                $tugas = Tugas::create([
                    'tipe' => 'kuliah',
                    'matkul_id' => $this->matkul_id ?: null,
                    'judul_tugas' => $this->judul_tugas,
                    'deskripsi' => $this->deskripsi,
                    'deadline' => $this->deadline,
                    'prioritas' => $this->prioritas,
                    'status' => $this->status,
                ]);
                TelegramService::notifikasiTugasDitambahkan($tugas);
                session()->flash('sukses', 'Tugas baru berhasil ditambahkan & notifikasi terkirim ke Telegram!');
            }
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $tugas = Tugas::findOrFail($id);
        $this->edit_id = $tugas->id;
        $this->tipe = $tugas->tipe ?? 'kuliah';
        $this->matkul_id = $tugas->matkul_id;
        $this->judul_tugas = $tugas->judul_tugas;
        $this->deskripsi = $tugas->deskripsi;
        $this->deadline = Carbon::parse($tugas->deadline)->format('Y-m-d\TH:i');
        $this->prioritas = $tugas->prioritas;
        $this->status = $tugas->status;
        $this->is_edit = true;
    }

    public function toggleStatus($id)
    {
        $tugas = Tugas::with('matkul')->findOrFail($id);
        $tugas->status = ($tugas->status === 'selesai') ? 'belum' : 'selesai';
        $tugas->save();

        if ($tugas->status === 'selesai') {
            TelegramService::notifikasiTugasSelesai($tugas);
            session()->flash('sukses', 'Hebat! Tugas selesai dan notifikasi terkirim ke Telegram.');
        }
    }

    public function setStatus($id, $newStatus)
    {
        $tugas = Tugas::with('matkul')->findOrFail($id);
        $tugas->status = $newStatus;
        $tugas->save();

        if ($newStatus === 'selesai') {
            TelegramService::notifikasiTugasSelesai($tugas);
            session()->flash('sukses', 'Hebat! Tugas selesai dan notifikasi terkirim ke Telegram.');
        }
    }

    public function kirimPengingatManual()
    {
        $kirim = TelegramService::kirimPengingatHarian(true);
        if ($kirim) {
            session()->flash('sukses', 'Notifikasi jadwal & pengingat berhasil dikirim ke Telegram kamu!');
        } else {
            session()->flash('error', 'Gagal mengirim notifikasi ke Telegram.');
        }
    }

    public function kirimLaporanHarian()
    {
        $kirim = TelegramService::kirimLaporanHarianTelegram();
        if ($kirim) {
            session()->flash('sukses', '📊 Laporan kegiatan harian berhasil dikirim ke bot Telegram kamu!');
        } else {
            session()->flash('error', 'Gagal mengirim laporan harian ke Telegram.');
        }
    }

    public function hapus($id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->delete();
        session()->flash('sukses', 'Tugas berhasil dihapus!');
        if ($this->edit_id === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $query = Tugas::with('matkul');

        if ($this->filter_status === 'belum') {
            $query->where('status', '!=', 'selesai');
        } elseif ($this->filter_status === 'selesai') {
            $query->where('status', 'selesai');
        }

        if ($this->filter_tipe) {
            $query->where('tipe', $this->filter_tipe);
        }

        if ($this->filter_matkul) {
            $query->where('matkul_id', $this->filter_matkul);
        }

        $tugasList = $query->orderBy('deadline', 'asc')->get();

        $total_tugas = Tugas::count();
        $total_kuliah = Tugas::where('tipe', 'kuliah')->count();
        $total_daily = Tugas::where('tipe', 'daily')->count();
        $total_belum = Tugas::where('status', '!=', 'selesai')->count();
        $total_selesai = Tugas::where('status', 'selesai')->count();
        $total_deadline_hari_ini = Tugas::where('status', '!=', 'selesai')
            ->whereDate('deadline', Carbon::today())
            ->count();

        return view('livewire.manajemen-tugas', [
            'tugasList' => $tugasList,
            'daftarMatkul' => Matkul::orderBy('nama_matkul')->get(),
            'total_tugas' => $total_tugas,
            'total_kuliah' => $total_kuliah,
            'total_daily' => $total_daily,
            'total_belum' => $total_belum,
            'total_selesai' => $total_selesai,
            'total_deadline_hari_ini' => $total_deadline_hari_ini,
        ]);
    }
}
