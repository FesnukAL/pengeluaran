<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kategori;

class ManajemenKategori extends Component
{
    public $nama_kategori;

    public function simpanKategori()
    {
        // Validasi inputan
        $this->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi, ya!',
            'nama_kategori.unique'   => 'Kategori ini sudah ada di database.',
        ]);

        try {
            // Simpan ke database
            Kategori::create([
                'nama_kategori' => $this->nama_kategori,
            ]);

            // Reset form & beri notifikasi
            $this->reset('nama_kategori');
            session()->flash('success', 'Mantap! Kategori baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan kategori: ' . $e->getMessage());
        }
    }

    public function hapusKategori($id)
    {
        $kategori = Kategori::find($id);
        if ($kategori) {
            $kategori->delete();
            session()->flash('success', 'Kategori berhasil dihapus!');
        }
    }

    public function render()
    {
        // Ambil semua data kategori, urutkan dari yang terbaru
        $kategoris = Kategori::latest()->get();

        return view('livewire.manajemen-kategori', [
            'kategoris' => $kategoris
        ]);
    }
}
