<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'dompet_asal_id',
        'dompet_tujuan_id',
        'nominal',
        'keterangan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi untuk mengambil nama dompet asal
    public function dompetAsal()
    {
        return $this->belongsTo(Dompet::class, 'dompet_asal_id');
    }

    // Relasi untuk mengambil nama dompet tujuan
    public function dompetTujuan()
    {
        return $this->belongsTo(Dompet::class, 'dompet_tujuan_id');
    }
}
