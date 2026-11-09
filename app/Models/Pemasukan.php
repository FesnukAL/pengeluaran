<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'dompet_id',
        'nominal',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dompet()
    {
        return $this->belongsTo(Dompet::class);
    }
}
