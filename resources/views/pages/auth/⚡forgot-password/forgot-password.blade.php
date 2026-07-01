<div class="card card-login-enterprise border-0">
    <div class="card-body p-4 p-sm-5">

        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center logo-container mb-4">
                @if($this->settings && $this->settings->logo)
                    <img src="{{ Storage::url($this->settings->logo) }}" alt="{{ $this->settings->name }}"
                         class="rounded-3 w-100 h-100" style="object-fit: cover;">
                @else
                    <i class="bi bi-shield-lock-fill fs-3"></i>
                @endif
            </div>
            <h4 class="fw-bold mb-2 text-dark" style="letter-spacing: -0.5px;">
                Lupa Password
            </h4>
            <p class="text-secondary small mb-0">
                Masukkan alamat email Anda untuk mendapatkan tautan pemulihan kata sandi.
            </p>
        </div>

        @if ($isSuccess)
            <div class="alert alert-success d-flex align-items-start mb-4 py-3 border-0 rounded-3 small fw-medium"
                 role="alert"
                 style="background-color: #ecfdf5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2 fs-5 mt-0.5"></i>
                <div>{{ $statusMessage }}</div>
            </div>
            
            <div class="d-grid mt-2">
                <a href="{{ route('login') }}" class="btn btn-enterprise text-center" wire:navigate.hover>
                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Login
                </a>
            </div>
        @else
            <form wire:submit="sendResetLink">
                <div class="form-floating mb-4">
                    <input wire:model="email"
                           type="email"
                           class="form-control form-control-enterprise @error('email') is-invalid @enderror"
                           id="email"
                           placeholder="Alamat Email"
                           required autofocus>
                    <label for="email" class="text-secondary ps-3">Alamat Email</label>
                    @error('email')
                    <div class="invalid-feedback ps-1 small fw-medium">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-3">
                    <button type="submit"
                            class="btn btn-enterprise"
                            wire:loading.attr="disabled"
                            wire:target="sendResetLink">
                        <span wire:loading.remove wire:target="sendResetLink">Kirim Tautan Pemulihan <i class="bi bi-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="sendResetLink">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Mengirimkan...
                        </span>
                    </button>
                    
                    <a href="{{ route('login') }}" class="text-decoration-none small fw-bold text-primary text-center" wire:navigate.hover>
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
                    </a>
                </div>
            </form>
        @endif

    </div>
</div>

<style>
.card-login-enterprise {
    border-radius: 1.25rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    background: #ffffff;
}

[data-bs-theme="dark"] .card-login-enterprise {
    background: #1c2333;
    border: 1px solid #2a3447;
}

.logo-container {
    width: 64px;
    height: 64px;
    background-color: #0f172a;
    color: #ffffff;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.form-control-enterprise {
    border-color: #e2e8f0;
    border-radius: 0.75rem;
    font-size: 0.95rem;
}

[data-bs-theme="dark"] .form-control-enterprise {
    border-color: #2a3447;
    background-color: #222d40;
    color: #f0f4f8;
}

.form-control-enterprise:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
}

.btn-enterprise {
    background-color: #0f172a;
    color: #ffffff;
    border-radius: 0.75rem;
    padding: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    transition: all 0.2s ease;
}

.btn-enterprise:hover, .btn-enterprise:focus {
    background-color: #1e293b;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
}

.btn-enterprise:disabled {
    background-color: #64748b;
    transform: none;
    box-shadow: none;
}
</style>
