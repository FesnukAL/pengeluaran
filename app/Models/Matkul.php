<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_matkul',
        'kode_matkul',
        'dosen',
        'sks',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'warna',
        'catatan',
        'tipe',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }
}
