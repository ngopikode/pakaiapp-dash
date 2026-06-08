<div>
    <!-- Header Section -->
    <div class="d-flex align-items-center mb-5 pb-3 border-bottom"
         style="border-color: var(--bs-border-color-translucent) !important;">
        <div class="d-flex align-items-center justify-content-center rounded-4 me-3 shadow-sm bg-body-tertiary"
             style="width: 56px; height: 56px; color: var(--brand-caramel, #b45309);">
            <i class="bi bi-person-badge fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--brand-caramel, #451a03);">Profil Pengguna</h3>
            <p class="text-secondary small mb-0 fw-medium">Kelola informasi personal dan pengaturan keamanan akun Anda.</p>
        </div>
    </div>

    <!-- Alert Success (Safe in Dark/Light Mode using Bootstrap styles) -->
    @if($showSuccessMessage)
        <div class="alert alert-success border-0 rounded-4 shadow-sm alert-dismissible fade show d-flex align-items-center mb-4 p-3"
             role="alert" style="border-left: 4px solid var(--bs-success) !important;">
            <i class="bi bi-check2-circle fs-4 me-3"></i>
            <div class="fw-medium">Profil dan keamanan akun berhasil diperbarui!</div>
            <button type="button" class="btn-close" wire:click="$set('showSuccessMessage', false)"
                    aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Informasi Profil -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-person-vcard text-warning fs-4 me-2"></i>
                        <h5 class="fw-bold mb-0">Informasi Profil</h5>
                    </div>

                    <form wire:submit="updateProfileInformation">

                        <!-- Input Nama -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-3 bg-body-tertiary border-0 @error('form.name') is-invalid @enderror"
                                   id="nameInput" wire:model="form.name" placeholder="Nama Lengkap">
                            <label for="nameInput" class="text-muted fw-medium">Nama Lengkap</label>
                            @error('form.name')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Input Email (Disabled & Styled for Dark Mode) -->
                        <div class="form-floating mb-4 position-relative">
                            <input type="email" class="form-control rounded-3 bg-body border-0 text-muted"
                                   id="emailInput" wire:model="form.email" placeholder="Email" disabled
                                   style="cursor: not-allowed; opacity: 0.6;">
                            <label for="emailInput" class="text-muted">Alamat Email</label>
                            <i class="bi bi-lock-fill position-absolute"
                               style="top: 18px; right: 16px; opacity: 0.5;"></i>
                            <div class="form-text small mt-1 text-muted">
                                <i class="bi bi-info-circle me-1"></i> Email digunakan untuk login dan tidak dapat diubah.
                            </div>
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="d-flex justify-content-end mt-4 pt-2">
                            <button type="submit" class="btn border-0 shadow-sm rounded-pill px-4 py-2 fw-bold text-white"
                                    style="background: #F97316;"
                                    wire:loading.attr="disabled" wire:target="updateProfileInformation">
                                <span wire:loading.remove wire:target="updateProfileInformation">
                                    Simpan Profil
                                </span>
                                <span wire:loading wire:target="updateProfileInformation">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"
                                          aria-hidden="true"></span>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Ubah Password -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-shield-lock text-danger fs-4 me-2"></i>
                        <h5 class="fw-bold mb-0">Keamanan & Password</h5>
                    </div>

                    <form wire:submit="updatePassword">

                        <div class="form-floating mb-3">
                            <input type="password"
                                   class="form-control rounded-3 bg-body-tertiary border-0 @error('form.current_password') is-invalid @enderror"
                                   id="currentPassword" wire:model="form.current_password"
                                   placeholder="Password Saat Ini">
                            <label for="currentPassword" class="text-muted fw-medium">Password Saat Ini</label>
                            @error('form.current_password')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password"
                                   class="form-control rounded-3 bg-body-tertiary border-0 @error('form.password') is-invalid @enderror"
                                   id="newPassword" wire:model="form.password" placeholder="Password Baru">
                            <label for="newPassword" class="text-muted fw-medium">Password Baru</label>
                            @error('form.password')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password"
                                   class="form-control rounded-3 bg-body-tertiary border-0 @error('form.password_confirmation') is-invalid @enderror"
                                   id="confirmPassword" wire:model="form.password_confirmation"
                                   placeholder="Konfirmasi Password">
                            <label for="confirmPassword" class="text-muted fw-medium">Konfirmasi Password Baru</label>
                            @error('form.password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Tombol Ubah Password -->
                        <div class="d-flex justify-content-end mt-4 pt-2">
                            <button type="submit" class="btn border-0 shadow-sm rounded-pill px-4 py-2 fw-bold text-white"
                                    style="background: var(--brand-caramel, #451a03);"
                                    wire:loading.attr="disabled" wire:target="updatePassword">
                                <span wire:loading.remove wire:target="updatePassword">
                                    <i class="bi bi-key-fill me-1"></i> Ubah Password
                                </span>
                                <span wire:loading wire:target="updatePassword">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"
                                          aria-hidden="true"></span>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Extra spacing for mobile bottom navbar -->
    <div style="height: 100px;" class="d-block d-md-none"></div>
</div>
