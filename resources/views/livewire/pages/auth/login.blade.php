<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: false);
    }
}; ?>

<div>
    <div class="auth-header">
        <div class="brand-badge">
            <i class="bi bi-wallet2"></i> Catatan Keuangan & Tugas
        </div>
        <h2 class="fw-bold mb-2 text-dark">Selamat Datang! 👋</h2>
        <p class="text-muted small mb-0">Masuk untuk mengelola catatan pengeluaran & tugas kamu.</p>
    </div>

    <div class="auth-body">
        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show mb-4 small" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="login">
            <!-- Email or Username -->
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-secondary">Email atau Username</label>
                <div class="input-group-modern">
                    <i class="bi bi-person input-icon"></i>
                    <input wire:model="form.email" id="email" type="text" class="form-control @error('form.email') is-invalid @enderror" placeholder="nama@email.com atau username" required autofocus autocomplete="username">
                </div>
                @error('form.email')
                    <div class="text-danger small mt-n2 mb-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                <div class="input-group-modern">
                    <i class="bi bi-shield-lock input-icon"></i>
                    <input wire:model="form.password" id="password" type="password" class="form-control @error('form.password') is-invalid @enderror" placeholder="••••••••" required autocomplete="current-password">
                </div>
                @error('form.password')
                    <div class="text-danger small mt-n2 mb-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input wire:model="form.remember" class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label small text-muted" for="remember">
                        Ingat saya
                    </label>
                </div>
                @if (Route::has('password.request'))
                    <a class="text-primary small text-decoration-none fw-semibold" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn btn-modern-primary w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
            </button>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <p class="small text-muted mb-0">
                Belum punya akun? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Daftar Akun Baru</a>
            </p>
        </div>
    </div>
</div>
