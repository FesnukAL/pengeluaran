<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dompet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'saldo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel pengeluaran
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }

    // Relasi ke tabel pemasukan
    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class);
    }
}
