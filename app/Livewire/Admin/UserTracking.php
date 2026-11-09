<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use App\Models\Tugas;
use App\Models\Dompet;
use Illuminate\Support\Facades\Auth;

class UserTracking extends Component
{
    public $search = '';

    public function mount()
    {
        // Proteksi: Hanya user dengan role 'admin' yang boleh masuk
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak! Halaman ini hanya untuk Super Admin.');
        }
    }

    public function toggleRole($userId)
    {
        // Cegah admin mengubah rolenya sendiri menjadi user biasa
        if ($userId == Auth::id()) {
            session()->flash('error', 'Anda tidak dapat mengubah role akun Anda sendiri!');
            return;
        }

        $user = User::findOrFail($userId);
        $user->role = ($user->role === 'admin') ? 'user' : 'admin';
        $user->save();

        session()->flash('sukses', "Role untuk {$user->name} berhasil diubah menjadi {$user->role}!");
    }

    public function hapusUser($userId)
    {
        if ($userId == Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
            return;
        }

        $user = User::findOrFail($userId);
        $nama = $user->name;
        $user->delete(); // On delete cascade akan otomatis membersihkan semua data transaksi & tugas miliknya

        session()->flash('sukses', "Pengguna {$nama} dan seluruh datanya berhasil dihapus dari sistem!");
    }

    public function render()
    {
        // Ambil daftar seluruh pengguna sistem
        $users = User::withCount(['dompets', 'pengeluarans', 'pemasukans', 'tugas', 'matkuls'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->get();

        $stats = [
            'total_users'        => User::count(),
            'total_admin'        => User::where('role', 'admin')->count(),
            'total_tugas'        => Tugas::count(),
            'total_transaksi'    => Pengeluaran::count() + Pemasukan::count(),
            'total_uang_beredar' => Dompet::sum('saldo'),
        ];

        return view('livewire.admin.user-tracking', [
            'users' => $users,
            'stats' => $stats,
        ]);
    }
}
