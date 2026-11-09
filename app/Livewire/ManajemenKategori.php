<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kategori;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ManajemenKategori extends Component
{
    public $nama_kategori;

    public function simpanKategori()
    {
        $userId = Auth::id();

        // Validasi inputan unik per user
        $this->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategoris', 'nama_kategori')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi, ya!',
            'nama_kategori.unique'   => 'Kategori ini sudah ada di daftar kamu.',
        ]);

        try {
            Kategori::create([
                'user_id'       => $userId,
                'nama_kategori' => $this->nama_kategori,
            ]);

            $this->reset('nama_kategori');
            session()->flash('success', 'Mantap! Kategori baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan kategori: ' . $e->getMessage());
        }
    }

    public function hapusKategori($id)
    {
        $kategori = Kategori::where('user_id', Auth::id())->find($id);
        if ($kategori) {
            $kategori->delete();
            session()->flash('success', 'Kategori berhasil dihapus!');
        }
    }

    public function render()
    {
        $kategoris = Kategori::where('user_id', Auth::id())->latest()->get();

        return view('livewire.manajemen-kategori', [
            'kategoris' => $kategoris
        ]);
    }
}
