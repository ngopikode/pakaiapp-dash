<div class="card card-login-enterprise border-0">
    <div class="card-body p-4 p-sm-5">

        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center logo-container mb-4">
                @if($this->settings && $this->settings->logo)
                    <img src="{{ Storage::url($this->settings->logo) }}" alt="{{ $this->settings->name }}"
                         class="rounded-3 w-100 h-100" style="object-fit: cover;">
                @else
                    <i class="bi bi-layers-fill fs-3"></i>
                @endif
            </div>
            <h4 class="fw-bold mb-2 text-dark" style="letter-spacing: -0.5px;">
                {{ $this->settings ? 'Login ke ' . $this->settings->name : 'Login Sistem' }}
            </h4>
            <p class="text-secondary small mb-0">
                Silakan masukkan alamat email dan password Anda.
            </p>
        </div>

        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center mb-4 py-3 border-0 rounded-3 small fw-medium"
                 role="alert"
                 style="background-color: #ecfdf5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        <form wire:submit="login">
            <div class="form-floating mb-3">
                <input wire:model="form.email"
                       type="email"
                       class="form-control form-control-enterprise @error('form.email') is-invalid @enderror"
                       id="email"
                       placeholder="Alamat Email"
                       required autofocus>
                <label for="email" class="text-secondary ps-3">Alamat Email</label>
                @error('form.email')
                <div class="invalid-feedback ps-1 small fw-medium">{{ $message }}</div>
                @enderror
            </div>

            <div x-data="{ show: false }" class="mb-4">
                <div class="input-group">
                    <div class="form-floating flex-grow-1">
                        <input wire:model="form.password"
                               :type="show ? 'text' : 'password'"
                               class="form-control form-control-enterprise rounded-end-0 border-end-0 @error('form.password') is-invalid @enderror"
                               id="password"
                               placeholder="Password"
                               required>
                        <label for="password" class="text-secondary ps-3">Password</label>
                    </div>
                    <span class="input-group-text input-group-text-enterprise px-3 rounded-end-3 border-start-0"
                          @click="show = !show">
                        <i class="bi fs-5" :class="show ? 'bi-eye-slash text-primary' : 'bi-eye text-secondary'"></i>
                    </span>
                </div>
                @error('form.password')
                <div class="text-danger small mt-1 ps-1 fw-medium">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 pt-1">
                <div class="form-check">
                    <input wire:model="form.remember" id="remember" type="checkbox"
                           class="form-check-input form-check-input-enterprise">
                    <label for="remember" class="form-check-label text-secondary small fw-medium user-select-none"
                           style="cursor: pointer;">
                        Ingat Perangkat Saya
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a class="text-decoration-none small fw-bold text-primary"
                       href="{{ route('password.request') }}" wire:navigate>
                        Lupa Password?
                    </a>
                @endif
            </div>

            <div class="d-grid mt-2">
                <button type="submit"
                        class="btn btn-enterprise"
                        wire:loading.attr="disabled"
                        wire:target="login">
                    <span wire:loading.remove wire:target="login">Masuk <i class="bi bi-arrow-right ms-2"></i></span>
                    <span wire:loading wire:target="login">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Mengautentikasi...
                    </span>
                </button>
            </div>
        </form>

    </div>
</div>
