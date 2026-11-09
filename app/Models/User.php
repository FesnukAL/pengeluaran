<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah user adalah Super Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function dompets()
    {
        return $this->hasMany(Dompet::class);
    }

    public function kategoris()
    {
        return $this->hasMany(Kategori::class);
    }

    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class);
    }

    public function pemasukans()
    {
        return $this->hasMany(Pemasukan::class);
    }

    public function mutasis()
    {
        return $this->hasMany(Mutasi::class);
    }

    public function matkuls()
    {
        return $this->hasMany(Matkul::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function pengaturan()
    {
        return $this->hasOne(Pengaturan::class);
    }
}
