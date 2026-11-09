<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matkul_id',
        'tipe',
        'judul_tugas',
        'deskripsi',
        'deadline',
        'status',
        'prioritas',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class);
    }
}
