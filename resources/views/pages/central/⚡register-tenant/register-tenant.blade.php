<div class="min-vh-100 d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                <!-- Card Container OP -->
                <div class="card card-op">

                    <!-- Header Section OP (Espresso & Sunset) -->
                    <div class="header-op text-center">
                        <div class="mb-2 text-warning fs-3"><i class="bi bi-cup-hot-fill"></i></div>
                        <h2 class="fw-bold mb-1 text-white position-relative z-1">Seduh Toko Digitalmu</h2>
                        <p class="text-white-50 mb-0 position-relative z-1" style="font-size: 0.95rem;">Racik ekosistem
                            bisnis senyaman menikmati kopi sore.</p>
                    </div>

                    <!-- Form Section -->
                    <div class="card-body p-4 p-md-5">
                        <form wire:submit.prevent="createTenant">

                            <!-- Step 1 -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="step-badge me-3">1</div>
                                <h5 class="fw-bold mb-0 text-dark" style="color: #451a03 !important;">Data Owner</h5>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="text"
                                       class="form-control form-control-op @error('userName') is-invalid @enderror"
                                       id="userName" placeholder="Nama Lengkap" wire:model="userName">
                                <label for="userName">Nama Lengkap</label>
                                @error('userName')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email"
                                               class="form-control form-control-op @error('userEmail') is-invalid @enderror"
                                               id="userEmail" placeholder="Email Login" wire:model="userEmail">
                                        <label for="userEmail">Email Kredensial</label>
                                        @error('userEmail')
                                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password"
                                               class="form-control form-control-op @error('password') is-invalid @enderror"
                                               id="password" placeholder="Password" wire:model="password">
                                        <label for="password">Password</label>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2 (Alpine.js Client-Side Processing - Tetap Aman & Ringan) -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="step-badge me-3">2</div>
                                <h5 class="fw-bold mb-0" style="color: #451a03 !important;">Identitas Toko / Resto</h5>
                            </div>

                            <div x-data="{
                                    name: @entangle('storeName'),
                                    slug: @entangle('tenantId'),
                                    generateSlug() {
                                        this.slug = this.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                                    }
                                }">

                                <div class="form-floating mb-4">
                                    <input type="text" x-model="name" @input="generateSlug"
                                           class="form-control form-control-op @error('storeName') is-invalid @enderror"
                                           id="storeName" placeholder="Nama Toko">
                                    <label for="storeName">Nama Toko / Brand Anda</label>
                                    @error('storeName')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row g-3 mb-5">
                                    <div class="col-md-4">
                                        <div class="form-floating h-100">
                                            <select
                                                class="form-select form-control-op h-100 @error('storeType') is-invalid @enderror"
                                                id="storeType" wire:model="storeType">
                                                <option value="resto">☕ Resto & Café</option>
                                                <option value="retail">🛍️ Retail Store</option>
                                            </select>
                                            <label for="storeType">Kategori</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-group input-group-lg h-100">
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" x-model="slug"
                                                       class="form-control form-control-op border-end-0 @error('tenantId') is-invalid @enderror"
                                                       id="tenantId" placeholder="Subdomain"
                                                       style="border-radius: 12px 0 0 12px;">
                                                <label for="tenantId">Alamat Subdomain</label>
                                            </div>
                                            <span class="input-group-text input-group-text-op px-3">
                                                .{{ config('tenancy.central_domains')[2] ?? 'pakaiapp.online' }}
                                            </span>
                                        </div>
                                        @error('tenantId')
                                        <div class="small text-danger mt-1 fw-bold">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="security-box p-4 mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-shield-lock-fill text-danger fs-5 me-2"></i>
                                    <h6 class="fw-bold text-danger mb-0">Otoritas Gatekeeper</h6>
                                </div>
                                <div class="form-floating">
                                    <input type="password"
                                           class="form-control form-control-op border-danger @error('pin') is-invalid @enderror"
                                           id="pin" placeholder="PIN Registrasi" wire:model="pin"
                                           style="background-color: #fff;">
                                    <label for="pin" class="text-danger">PIN Akses Sistem</label>
                                    @error('pin')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                    class="btn btn-op w-100 mt-2 d-flex justify-content-center align-items-center"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    Seduh & Daftarkan Toko <i class="bi bi-sunset-fill ms-2"></i>
                                </span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2" role="status"
                                          aria-hidden="true"></span>
                                    Sedang meracik database tenant...
                                </span>
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
