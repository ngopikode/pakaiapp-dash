<div class="card card-login-enterprise border-0">
    <div class="card-body p-4 p-sm-5">

        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center logo-container mb-4">
                @if($this->settings && $this->settings->logo)
                    <img src="{{ Storage::url($this->settings->logo) }}" alt="{{ $this->settings->name }}"
                         class="rounded-3 w-100 h-100" style="object-fit: cover;">
                @else
                    <i class="bi bi-shield-check-fill fs-3"></i>
                @endif
            </div>
            <h4 class="fw-bold mb-2 text-dark" style="letter-spacing: -0.5px;">
                Atur Ulang Password
            </h4>
            <p class="text-secondary small mb-0">
                Silakan buat kata sandi baru untuk akun kasir Anda.
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
                    Masuk Sekarang <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        @else
            <form wire:submit="resetPassword">
                <input type="hidden" wire:model="token">
                <input type="hidden" wire:model="email">

                <div class="form-floating mb-3">
                    <input type="text"
                           class="form-control form-control-enterprise"
                           id="email-display"
                           value="{{ $email }}"
                           disabled>
                    <label for="email-display" class="text-secondary ps-3">Alamat Email</label>
                </div>

                <div x-data="{ show: false }" class="mb-3">
                    <div class="input-group">
                        <div class="form-floating flex-grow-1">
                            <input wire:model="password"
                                   :type="show ? 'text' : 'password'"
                                   class="form-control form-control-enterprise rounded-end-0 border-end-0 @error('password') is-invalid @enderror"
                                   id="password"
                                   placeholder="Password Baru"
                                   required autofocus>
                            <label for="password" class="text-secondary ps-3">Password Baru</label>
                        </div>
                        <span class="input-group-text input-group-text-enterprise px-3 rounded-end-3 border-start-0"
                              @click="show = !show">
                            <i class="bi fs-5" :class="show ? 'bi-eye-slash text-primary' : 'bi-eye text-secondary'"></i>
                        </span>
                    </div>
                    @error('password')
                    <div class="text-danger small mt-1 ps-1 fw-medium">{{ $message }}</div>
                    @enderror
                </div>

                <div x-data="{ show: false }" class="mb-4">
                    <div class="input-group">
                        <div class="form-floating flex-grow-1">
                            <input wire:model="password_confirmation"
                                   :type="show ? 'text' : 'password'"
                                   class="form-control form-control-enterprise rounded-end-0 border-end-0"
                                   id="password_confirmation"
                                   placeholder="Konfirmasi Password"
                                   required>
                            <label for="password_confirmation" class="text-secondary ps-3">Konfirmasi Password</label>
                        </div>
                        <span class="input-group-text input-group-text-enterprise px-3 rounded-end-3 border-start-0"
                              @click="show = !show">
                            <i class="bi fs-5" :class="show ? 'bi-eye-slash text-primary' : 'bi-eye text-secondary'"></i>
                        </span>
                    </div>
                </div>

                @error('email')
                <div class="alert alert-danger py-2 border-0 rounded-3 small fw-medium mb-3" role="alert" style="background-color: #fef2f2; color: #991b1b;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}
                </div>
                @enderror

                <div class="d-grid mt-2">
                    <button type="submit"
                            class="btn btn-enterprise"
                            wire:loading.attr="disabled"
                            wire:target="resetPassword">
                        <span wire:loading.remove wire:target="resetPassword">Simpan Password Baru <i class="bi bi-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="resetPassword">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Menyimpan...
                        </span>
                    </button>
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
