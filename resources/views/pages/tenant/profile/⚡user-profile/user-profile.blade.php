<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold font-serif text-dark mb-1">Profil Pengguna</h2>
            <p class="text-muted small mb-0">Kelola informasi akun dan keamanan.</p>
        </div>
    </div>

    @if($showSuccessMessage)
        <div
            class="alert alert-success border-0 rounded-4 shadow-sm alert-dismissible fade show d-flex align-items-center"
            role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>Profil berhasil diperbarui!</div>
            <button type="button" class="btn-close" wire:click="$set('showSuccessMessage', false)"
                    aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Informasi Profil</h5>
                    <form wire:submit="updateProfileInformation">
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control rounded-pill" wire:model="form.name">
                            @error('form.name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Email</label>
                            <input type="email" class="form-control rounded-pill" wire:model="form.email" disabled>
                            @error('form.email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-brand rounded-pill px-4">
                                <span wire:loading.remove wire:target="updateProfileInformation">Simpan</span>
                                <span wire:loading wire:target="updateProfileInformation">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Ubah Password</h5>
                    <form wire:submit="updatePassword">
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Password Saat Ini</label>
                            <input type="password" class="form-control rounded-pill" wire:model="form.current_password">
                            @error('form.current_password') <span
                                class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Password Baru</label>
                            <input type="password" class="form-control rounded-pill" wire:model="form.password">
                            @error('form.password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Konfirmasi Password</label>
                            <input type="password" class="form-control rounded-pill"
                                   wire:model="form.password_confirmation">
                            @error('form.password_confirmation') <span
                                class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-brand rounded-pill px-4">
                                <span wire:loading.remove wire:target="updatePassword">Ubah Password</span>
                                <span wire:loading wire:target="updatePassword">Memproses...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
