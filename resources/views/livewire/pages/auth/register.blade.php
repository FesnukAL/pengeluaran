<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: false);
    }
}; ?>

<div>
    <div class="auth-header">
        <div class="brand-badge">
            <i class="bi bi-person-plus-fill"></i> Pendaftaran Akun
        </div>
        <h2 class="fw-bold mb-2 text-dark">Daftar Akun Baru 🚀</h2>
        <p class="text-muted small mb-0">Kelola keuangan dan jadwal tugas dengan mudah.</p>
    </div>

    <div class="auth-body">
        <form wire:submit="register">
            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label small fw-semibold text-secondary">Nama Lengkap</label>
                <div class="input-group-modern">
                    <i class="bi bi-person input-icon"></i>
                    <input wire:model="name" id="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Nama kamu" required autofocus autocomplete="name">
                </div>
                @error('name')
                    <div class="text-danger small mt-n2 mb-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-secondary">Alamat Email</label>
                <div class="input-group-modern">
                    <i class="bi bi-envelope input-icon"></i>
                    <input wire:model="email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required autocomplete="username">
                </div>
                @error('email')
                    <div class="text-danger small mt-n2 mb-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                <div class="input-group-modern">
                    <i class="bi bi-shield-lock input-icon"></i>
                    <input wire:model="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                </div>
                @error('password')
                    <div class="text-danger small mt-n2 mb-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label small fw-semibold text-secondary">Ulangi Password</label>
                <div class="input-group-modern">
                    <i class="bi bi-shield-check input-icon"></i>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Ketik ulang password" required autocomplete="new-password">
                </div>
                @error('password_confirmation')
                    <div class="text-danger small mt-n2 mb-2">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-modern-primary w-100">
                <i class="bi bi-person-check me-1"></i> Buat Akun Sekarang
            </button>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <p class="small text-muted mb-0">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
