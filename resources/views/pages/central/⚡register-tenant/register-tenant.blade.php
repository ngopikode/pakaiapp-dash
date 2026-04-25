<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">Buka Toko Baru</h3>
                        <p class="text-muted small">Registrasi Tenant pakaiapp.online</p>
                    </div>

                    <form wire:submit.prevent="createTenant">

                        <h6 class="fw-bold mb-3 border-bottom pb-2">1. Data Pemilik</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control @error('userName') is-invalid @enderror"
                                   wire:model="userName">
                            @error('userName')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Email Login</label>
                                <input type="email" class="form-control @error('userEmail') is-invalid @enderror"
                                       wire:model="userEmail">
                                @error('userEmail')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       wire:model="password">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2">2. Info Resto / Toko</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Toko</label>
                            <input type="text" class="form-control @error('storeName') is-invalid @enderror"
                                   wire:model.live="storeName">
                            @error('storeName')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">URL Subdomain</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('tenantId') is-invalid @enderror"
                                       wire:model="tenantId">
                                <span
                                    class="input-group-text bg-light">.{{ config('tenancy.central_domains')[2] }}</span>
                            </div>
                            @error('tenantId')
                            <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-danger">3. Keamanan</h6>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-danger">PIN Registrasi</label>
                            <input type="password" class="form-control border-danger @error('pin') is-invalid @enderror"
                                   wire:model="pin" placeholder="Masukkan PIN rahasia">
                            @error('pin')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" wire:loading.attr="disabled">
                            <span wire:loading.remove>Daftarkan Toko</span>
                            <span wire:loading>Memproses Database... (Bisa agak lama)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
