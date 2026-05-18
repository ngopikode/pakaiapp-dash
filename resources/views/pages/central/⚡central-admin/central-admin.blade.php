<div class="min-vh-100 bg-body-tertiary d-flex align-items-center position-relative overflow-hidden"
     x-data="{
        init() {
            window.addEventListener('swal:success', event => {
                Swal.fire({ icon: 'success', title: event.detail.title, text: event.detail.message, confirmButtonColor: '#B67332', customClass: { confirmButton: 'btn btn-primary rounded-pill px-4' }, buttonsStyling: false });
            });
            window.addEventListener('swal:error', event => {
                Swal.fire({ icon: 'error', title: 'Oops!', text: event.detail.message, confirmButtonColor: '#B67332', customClass: { confirmButton: 'btn btn-primary rounded-pill px-4' }, buttonsStyling: false });
            });
        }
     }">

    {{-- Background Orbs --}}
    <div class="position-absolute rounded-circle bg-warning opacity-10"
         style="width: 600px; height: 600px; filter: blur(80px); top: -200px; left: -100px; pointer-events: none;"></div>

    <div class="container position-relative z-1 py-5">
        @if(!$isAuthenticated)
            {{-- ULTRA-PREMIUM LOCK SCREEN --}}
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <div class="card border border-light border-opacity-25 shadow-lg rounded-5 overflow-hidden"
                         style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px);">
                        <div class="card-body p-5 text-center">
                            <div
                                class="d-inline-flex align-items-center justify-content-center bg-dark text-warning rounded-circle mb-4 shadow"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-fingerprint fs-1"></i>
                            </div>
                            <h4 class="fw-black mb-1 text-body">Central Gatekeeper</h4>
                            <p class="text-secondary small mb-4">Otorisasi Sistem Superadmin</p>

                            <form wire:submit.prevent="login">
                                <div class="form-floating mb-4">
                                    <input type="password"
                                           class="form-control bg-transparent text-center fs-3 fw-bold letter-spacing-2 @error('authPin') is-invalid @enderror"
                                           wire:model="authPin" placeholder="PIN" autofocus
                                           style="border-radius: 1rem; height: 70px;">
                                    <label>Masukkan PIN Otoritas</label>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove>Buka Brankas <i class="bi bi-unlock-fill ms-2"></i></span>
                                    <span wire:loading><span class="spinner-border spinner-border-sm"></span> Verifikasi...</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- MODERN DASHBOARD --}}
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <div>
                            <span
                                class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 mb-2 border border-danger border-opacity-25"><i
                                    class="bi bi-shield-shaded me-1"></i> Mode Superadmin</span>
                            <h2 class="fw-black mb-0 text-body" style="letter-spacing: -1px;">Command Center</h2>
                        </div>
                        <button wire:click="logout"
                                class="btn btn-outline-danger bg-body rounded-pill fw-bold px-4 shadow-sm hover-lift">
                            <i class="bi bi-power me-1"></i> Akhiri Sesi
                        </button>
                    </div>

                    <div class="card border shadow-sm rounded-4 bg-body">
                        {{-- Segmented Controls (macOS Style) --}}
                        <div class="p-2 border-bottom bg-body-tertiary">
                            <div class="d-flex bg-body rounded-pill p-1 border shadow-sm"
                                 style="border-color: var(--bs-border-color-translucent) !important;">
                                <button wire:click="changeTab('create_tenant')"
                                        class="btn rounded-pill px-4 py-2 fw-bold flex-grow-1 border-0 transition-all {{ $activeTab === 'create_tenant' ? 'btn-primary shadow-sm' : 'text-secondary hover-bg-light' }}">
                                    <i class="bi bi-rocket-takeoff me-1"></i> Deploy Tenant
                                </button>
                                <button wire:click="changeTab('topup')"
                                        class="btn rounded-pill px-4 py-2 fw-bold flex-grow-1 border-0 transition-all {{ $activeTab === 'topup' ? 'btn-primary shadow-sm' : 'text-secondary hover-bg-light' }}">
                                    <i class="bi bi-wallet2 me-1"></i> Injeksi Saldo
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            @if($activeTab === 'create_tenant')
                                <form wire:submit.prevent="createTenant">
                                    <div class="d-flex align-items-center mb-4">
                                        <div
                                            class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                            style="width: 35px; height: 35px; font-weight: 800;">1
                                        </div>
                                        <h5 class="fw-bold mb-0">Informasi Pemilik</h5>
                                    </div>
                                    <div class="row g-3 mb-5">
                                        <div class="col-md-4">
                                            <div class="form-floating"><input type="text" class="form-control"
                                                                              wire:model="userName"
                                                                              placeholder="Nama"><label>Nama
                                                    Lengkap</label></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating"><input type="email" class="form-control"
                                                                              wire:model="userEmail"
                                                                              placeholder="Email"><label>Email
                                                    Login</label></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating"><input type="password" class="form-control"
                                                                              wire:model="password"
                                                                              placeholder="Pass"><label>Password
                                                    Akun</label></div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div
                                            class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                            style="width: 35px; height: 35px; font-weight: 800;">2
                                        </div>
                                        <h5 class="fw-bold mb-0">Identitas Bisnis</h5>
                                    </div>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <div class="form-floating"><input type="text" class="form-control"
                                                                              wire:model.live="storeName"
                                                                              placeholder="Toko"><label>Nama Brand /
                                                    Toko</label></div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select" wire:model="storeType">
                                                    <option value="resto">☕ F&B (Resto/Cafe)</option>
                                                    <option value="retail">🛍️ Retail Store</option>
                                                </select>
                                                <label>Sektor</label>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group input-group-lg h-100 shadow-sm">
                                                <div class="form-floating flex-grow-1"><input type="text"
                                                                                              class="form-control border-end-0 text-primary fw-bold"
                                                                                              wire:model="tenantId"
                                                                                              placeholder="Subdomain"><label>Alamat
                                                        URL (Subdomain)</label></div>
                                                <span class="input-group-text bg-body-tertiary fw-medium">.pakaiapp.online</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end border-top pt-4 mt-4">
                                        <button type="submit"
                                                class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm hover-lift"
                                                wire:loading.attr="disabled">
                                            <span wire:loading.remove>Eksekusi & Build Database <i
                                                    class="bi bi-database-add ms-2"></i></span>
                                            <span wire:loading><span
                                                    class="spinner-border spinner-border-sm me-2"></span> Compiling...</span>
                                        </button>
                                    </div>
                                </form>

                            @elseif($activeTab === 'topup')
                                <form wire:submit.prevent="processTopUp">
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-12">
                                            <div class="form-floating shadow-sm">
                                                <select class="form-select fw-bold text-primary"
                                                        wire:model="selectedTenant">
                                                    <option value="">-- Cari & Pilih Tenant --</option>
                                                    @foreach($tenants as $t)
                                                        <option value="{{ $t->id }}">
                                                            {{ strtoupper($t->id) }} &bull; {{ $t->domains->first()?->domain }}</option>
                                                    @endforeach
                                                </select>
                                                <label>Pilih Merchant Tujuan</label>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group input-group-lg h-100 shadow-sm">
                                                <span
                                                    class="input-group-text bg-primary text-white border-0 fw-bold">Rp</span>
                                                <div class="form-floating flex-grow-1">
                                                    <input type="number"
                                                           class="form-control border-start-0 fs-4 fw-bold text-success"
                                                           wire:model="topupAmount" placeholder="Nominal" min="0">
                                                    <label>Nominal Saldo</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-floating shadow-sm h-100">
                                                <input type="text" class="form-control h-100"
                                                       wire:model="topupDescription" placeholder="Catatan">
                                                <label>Keterangan / Ref. Mutasi</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end border-top pt-4">
                                        <button type="submit"
                                                class="btn text-white rounded-pill px-5 py-3 fw-bold shadow-sm hover-lift"
                                                style="background: linear-gradient(135deg, #10b981, #059669);"
                                                wire:loading.attr="disabled">
                                            <span wire:loading.remove>Tembak Saldo Sekarang <i
                                                    class="bi bi-send-fill ms-2"></i></span>
                                            <span wire:loading><span
                                                    class="spinner-border spinner-border-sm me-2"></span> Memproses Transaksi...</span>
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

