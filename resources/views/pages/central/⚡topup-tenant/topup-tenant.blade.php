<div class="min-vh-100 d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                {{-- Alert Success --}}
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4"
                         role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <strong>Sukses!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Card Container OP -->
                <div class="card card-op shadow-lg border-0" style="border-radius: 1.25rem; overflow: hidden;">

                    <!-- Header Section OP -->
                    <div class="header-op text-center py-4"
                         style="background: linear-gradient(135deg, #451a03, #b45309);">
                        <div class="mb-2 text-warning fs-3"><i class="bi bi-wallet-fill"></i></div>
                        <h2 class="fw-bold mb-1 text-white position-relative z-1">Top Up Saldo Tenant</h2>
                        <p class="text-white-50 mb-0 position-relative z-1" style="font-size: 0.95rem;">
                            Injeksi kredit pakaiapp.online untuk merchant.
                        </p>
                    </div>

                    <!-- Form Section -->
                    <div class="card-body p-4 p-md-5 bg-body">
                        <form wire:submit.prevent="processTopUp">

                            <!-- Step 1: Pilih Tenant -->
                            <div class="d-flex align-items-center mb-4">
                                <div
                                    class="step-badge me-3 bg-warning text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;">1
                                </div>
                                <h5 class="fw-bold mb-0 text-dark" style="color: #451a03 !important;">Target
                                    Merchant</h5>
                            </div>

                            <div class="form-floating mb-5">
                                <select class="form-select form-control-op @error('tenantId') is-invalid @enderror"
                                        id="tenantId" wire:model="tenantId">
                                    <option value="">-- Pilih Toko / Subdomain --</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}">{{ $tenant->id }}
                                            ({{ $tenant->domains->first()?->domain }})
                                        </option>
                                    @endforeach
                                </select>
                                <label for="tenantId">Pilih Tenant</label>
                                @error('tenantId')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Step 2: Nominal & Keterangan -->
                            <div class="d-flex align-items-center mb-4">
                                <div
                                    class="step-badge me-3 bg-warning text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;">2
                                </div>
                                <h5 class="fw-bold mb-0 text-dark" style="color: #451a03 !important;">Rincian Top
                                    Up</h5>
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-5">
                                    <div class="input-group input-group-lg h-100 shadow-sm rounded-3">
                                        <span class="input-group-text bg-body-tertiary border-end-0 fw-bold">Rp</span>
                                        <div class="form-floating flex-grow-1">
                                            <input type="number"
                                                   class="form-control form-control-op border-start-0 @error('amount') is-invalid @enderror"
                                                   id="amount" placeholder="Nominal" wire:model="amount" min="0">
                                            <label for="amount">Nominal Top Up</label>
                                        </div>
                                    </div>
                                    @error('amount')
                                    <div class="small text-danger mt-1 fw-bold">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-7">
                                    <div class="form-floating h-100">
                                        <input type="text"
                                               class="form-control form-control-op @error('description') is-invalid @enderror"
                                               id="description" placeholder="Catatan" wire:model="description">
                                        <label for="description">Catatan Transaksi</label>
                                        @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Gatekeeper -->
                            <div class="security-box p-4 mb-4 rounded-4 border border-danger bg-danger bg-opacity-10">
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
                                    class="btn w-100 mt-2 py-3 fw-bold rounded-3 shadow-sm d-flex justify-content-center align-items-center text-white"
                                    style="background: linear-gradient(135deg, #ca8a04, #b45309);"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    Eksekusi Top Up <i class="bi bi-lightning-charge-fill ms-2"></i>
                                </span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2" role="status"
                                          aria-hidden="true"></span>
                                    Menginjeksi Saldo ke Tenant...
                                </span>
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
