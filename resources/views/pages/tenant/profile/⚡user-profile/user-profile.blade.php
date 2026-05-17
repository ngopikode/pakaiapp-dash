<div>
    <!-- Header Section -->
    <div class="d-flex align-items-center mb-5 pb-3 border-bottom"
         style="border-color: rgba(124, 45, 18, 0.1) !important;">
        <div class="d-flex align-items-center justify-content-center rounded-4 me-3 shadow-sm"
             style="width: 56px; height: 56px; background: linear-gradient(135deg, #fef3c7, #ffedd5); color: #b45309;">
            <i class="bi bi-person-badge fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-1" style="color: #451a03;">Profil Pengguna</h3>
            <p class="text-muted small mb-0">Kelola informasi personal dan pengaturan keamanan akun Anda.</p>
        </div>
    </div>

    <!-- Alert Success -->
    @if($showSuccessMessage)
        <div class="alert border-0 rounded-4 shadow-sm alert-dismissible fade show d-flex align-items-center mb-4 p-3"
             role="alert" style="background-color: #ecfccb; color: #3f6212; border-left: 4px solid #84cc16 !important;">
            <i class="bi bi-check2-circle fs-4 me-3"></i>
            <div class="fw-medium">Profil dan keamanan akun berhasil diperbarui!</div>
            <button type="button" class="btn-close" wire:click="$set('showSuccessMessage', false)"
                    aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Informasi Profil -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: rgba(255, 255, 255, 0.9);">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-person-vcard text-warning fs-4 me-2"></i>
                        <h5 class="fw-bold mb-0" style="color: #451a03;">Informasi Profil</h5>
                    </div>

                    <form wire:submit="updateProfileInformation">

                        <!-- Input Nama -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-3 @error('form.name') is-invalid @enderror"
                                   id="nameInput" wire:model="form.name" placeholder="Nama Lengkap"
                                   style="background-color: #faf8f5; border-color: #e7e1d6;">
                            <label for="nameInput" style="color: #7c2d12; opacity: 0.8;">Nama Lengkap</label>
                            @error('form.name')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Input Email (Disabled) -->
                        <div class="form-floating mb-4 position-relative">
                            <input type="email" class="form-control rounded-3"
                                   id="emailInput" wire:model="form.email" placeholder="Email" disabled
                                   style="background-color: #f3f4f6; border-color: #e5e7eb; color: #6b7280; cursor: not-allowed;">
                            <label for="emailInput" style="color: #6b7280;">Alamat Email</label>
                            <i class="bi bi-lock-fill position-absolute"
                               style="top: 18px; right: 16px; color: #9ca3af;"></i>
                            <div class="form-text small mt-1 text-muted">
                                <i class="bi bi-info-circle me-1"></i> Email digunakan untuk login dan tidak dapat
                                diubah.
                            </div>
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="d-flex justify-content-end mt-4 pt-2">
                            <button type="submit" class="btn border-0 shadow-sm rounded-3 px-4 py-2 fw-bold"
                                    style="background: linear-gradient(135deg, #ca8a04, #b45309); color: white;"
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
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: rgba(255, 255, 255, 0.9);">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-shield-lock text-danger fs-4 me-2"></i>
                        <h5 class="fw-bold mb-0" style="color: #451a03;">Keamanan & Password</h5>
                    </div>

                    <form wire:submit="updatePassword">

                        <div class="form-floating mb-3">
                            <input type="password"
                                   class="form-control rounded-3 @error('form.current_password') is-invalid @enderror"
                                   id="currentPassword" wire:model="form.current_password"
                                   placeholder="Password Saat Ini"
                                   style="background-color: #faf8f5; border-color: #e7e1d6;">
                            <label for="currentPassword" style="color: #7c2d12; opacity: 0.8;">Password Saat Ini</label>
                            @error('form.current_password')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password"
                                   class="form-control rounded-3 @error('form.password') is-invalid @enderror"
                                   id="newPassword" wire:model="form.password" placeholder="Password Baru"
                                   style="background-color: #faf8f5; border-color: #e7e1d6;">
                            <label for="newPassword" style="color: #7c2d12; opacity: 0.8;">Password Baru</label>
                            @error('form.password')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password"
                                   class="form-control rounded-3 @error('form.password_confirmation') is-invalid @enderror"
                                   id="confirmPassword" wire:model="form.password_confirmation"
                                   placeholder="Konfirmasi Password"
                                   style="background-color: #faf8f5; border-color: #e7e1d6;">
                            <label for="confirmPassword" style="color: #7c2d12; opacity: 0.8;">Konfirmasi Password
                                Baru</label>
                            @error('form.password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Tombol Ubah Password -->
                        <div class="d-flex justify-content-end mt-4 pt-2">
                            <button type="submit" class="btn border-0 shadow-sm rounded-3 px-4 py-2 fw-bold"
                                    style="background: #451a03; color: white;"
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
</div>
