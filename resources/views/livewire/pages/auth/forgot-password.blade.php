<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <div class="auth-header">
        <div class="brand-badge">
            <i class="bi bi-key-fill"></i> Reset Password
        </div>
        <h2 class="fw-bold mb-2 text-dark">Lupa Password? 🔑</h2>
        <p class="text-muted small mb-0">Masukkan email kamu untuk menerima tautan pemulihan kata sandi.</p>
    </div>

    <div class="auth-body">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show mb-4 small" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="sendPasswordResetLink">
            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="form-label small fw-semibold text-secondary">Alamat Email Terdaftar</label>
                <div class="input-group-modern">
                    <i class="bi bi-envelope input-icon"></i>
                    <input wire:model="email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required autofocus>
                </div>
                @error('email')
                    <div class="text-danger small mt-n2 mb-2">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-modern-primary w-100">
                <i class="bi bi-send me-1"></i> Kirim Link Pemulihan
            </button>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <p class="small text-muted mb-0">
                Ingat password kamu? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Kembali Masuk</a>
            </p>
        </div>
    </div>
</div>
