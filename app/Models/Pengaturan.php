<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengaturan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'batas_mingguan',
        'batas_bulanan',
        'batas_tahunan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
