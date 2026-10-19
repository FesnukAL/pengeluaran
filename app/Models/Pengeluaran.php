<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengeluaran extends Model
{
    use HasFactory;

    // Membuka izin agar semua kolom bisa diisi data dari Livewire
    protected $guarded = [];

    public function kategori()
    {
        // Mengarahkan ke model Kategori menggunakan foreign key 'kategori_id'
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
    public function dompet()
    {
        return $this->belongsTo(Dompet::class);
    }
}
