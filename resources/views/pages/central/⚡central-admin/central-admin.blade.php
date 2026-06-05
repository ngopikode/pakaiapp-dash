<div class="min-vh-100 bg-light py-5"
     x-data="{
        init() {
            window.addEventListener('swal:success', event => {
                Swal.fire({ icon: 'success', title: event.detail.title, text: event.detail.message, confirmButtonColor: '#0d6efd' });
            });
            window.addEventListener('swal:error', event => {
                Swal.fire({ icon: 'error', title: 'Gagal', text: event.detail.message, confirmButtonColor: '#dc3545' });
            });
        }
     }">

    <div class="container">
        @if(!$isAuthenticated)
            {{-- TAMPILAN LOCK SCREEN NORMAL --}}
            <div class="row justify-content-center mt-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 p-sm-5">
                            <div class="text-center mb-4">
                                <h4 class="fw-bold">Superadmin Access</h4>
                                <p class="text-muted small">Masukkan PIN untuk melanjutkan</p>
                            </div>

                            <form wire:submit.prevent="login">
                                {{-- Alpine Component untuk Hybrid PIN --}}
                                <div x-data="{ pin: @entangle('authPin').live }">
                                    <div class="mb-4">
                                        <input type="password"
                                               class="form-control form-control-lg text-center fs-2 tracking-widest @error('authPin') is-invalid @enderror"
                                               x-model="pin"
                                               inputmode="numeric"
                                               maxlength="6"
                                               autofocus
                                               placeholder="••••••"
                                               style="letter-spacing: 0.5rem;">
                                        @error('authPin')
                                        <div class="invalid-feedback text-center">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Visual Numpad --}}
                                    <div class="row g-2 mb-4">
                                        <template x-for="i in 9" :key="i">
                                            <div class="col-4">
                                                <button type="button" class="btn btn-light border w-100 py-3 fs-5"
                                                        @click="pin = (pin || '') + i" x-text="i"></button>
                                            </div>
                                        </template>
                                        <div class="col-4">
                                            <button type="button"
                                                    class="btn btn-light border w-100 py-3 fs-5 text-danger"
                                                    @click="pin = (pin || '').slice(0, -1)">
                                                <i class="bi bi-backspace"></i>
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-light border w-100 py-3 fs-5"
                                                    @click="pin = (pin || '') + '0'">0
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5"
                                                    wire:loading.attr="disabled">
                                                <i class="bi bi-box-arrow-in-right" wire:loading.remove></i>
                                                <span class="spinner-border spinner-border-sm" wire:loading></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- TAMPILAN DASHBOARD NORMAL --}}
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">

                    {{-- Header --}}
                    <div
                        class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                        <div>
                            <h3 class="fw-bold mb-1">Superadmin Panel</h3>
                            <span class="text-muted">Manajemen sistem pakaiapp.online</span>
                        </div>
                        <button wire:click="logout" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-left me-1"></i> Logout
                        </button>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white pt-3 pb-0 border-bottom">
                            {{-- Standard Bootstrap Tabs --}}
                            <ul class="nav nav-tabs border-bottom-0">
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab === 'create_tenant' ? 'active fw-bold' : 'text-muted' }}"
                                       href="#" wire:click.prevent="changeTab('create_tenant')">
                                        <i class="bi bi-plus-circle me-1"></i> Deploy Tenant Baru
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab === 'topup' ? 'active fw-bold' : 'text-muted' }}"
                                       href="#" wire:click.prevent="changeTab('topup')">
                                        <i class="bi bi-wallet2 me-1"></i> Injeksi Saldo
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab === 'pending_activations' ? 'active fw-bold' : 'text-muted' }}"
                                       href="#" wire:click.prevent="changeTab('pending_activations')">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Aktivasi Tertunda
                                        @if(count($pendingRegistrations) > 0)
                                            <span class="badge bg-danger rounded-pill ms-1">{{ count($pendingRegistrations) }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            @if($activeTab === 'create_tenant')
                                <form wire:submit.prevent="createTenant">
                                    <h5 class="mb-3 border-bottom pb-2">Informasi Pemilik</h5>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" wire:model="userName"
                                                   placeholder="Masukkan nama pemilik">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Email Login</label>
                                            <input type="email" class="form-control" wire:model="userEmail"
                                                   placeholder="email@domain.com">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Password</label>
                                            <input type="password" class="form-control" wire:model="password"
                                                   placeholder="Minimal 8 karakter">
                                        </div>
                                    </div>

                                    <h5 class="mb-3 border-bottom pb-2">Identitas Bisnis</h5>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <label class="form-label">Nama Brand / Toko</label>
                                            <input type="text" class="form-control" wire:model.live="storeName"
                                                   placeholder="Contoh: Kopi Kenangan">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Sektor Bisnis</label>
                                            <select class="form-select" wire:model="storeType">
                                                <option value="resto">F&B (Resto/Cafe)</option>
                                                <option value="retail">Retail Store</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Paket Awal</label>
                                            <select class="form-select" wire:model="subscriptionPlan">
                                                <option value="free">Uji Coba (Gratis)</option>
                                                <option value="santai">Paket Santai</option>
                                                <option value="premium">Auto Premium</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Subdomain URL</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" wire:model="tenantId"
                                                       placeholder="namatoko">
                                                <span class="input-group-text bg-light">.pakaiapp.online</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled">
                                            <span wire:loading.remove>Eksekusi Database</span>
                                            <span wire:loading><span
                                                    class="spinner-border spinner-border-sm me-2"></span> Memproses...</span>
                                        </button>
                                    </div>
                                </form>

                            @elseif($activeTab === 'topup')
                                <form wire:submit.prevent="processTopUp">
                                    <div class="row g-3 mb-4">
                                        <div class="col-12">
                                            <label class="form-label">Pilih Merchant Tujuan</label>
                                            <select class="form-select" wire:model="selectedTenant">
                                                <option value="">-- Cari & Pilih Tenant --</option>
                                                @foreach($tenants as $t)
                                                    <option value="{{ $t->id }}">
                                                        {{ strtoupper($t->id) }} - {{ $t->domains->first()?->domain }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-5">
                                            <label class="form-label">Nominal Saldo</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">Rp</span>
                                                <input type="number" class="form-control fw-bold"
                                                       wire:model="topupAmount" placeholder="0" min="0">
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <label class="form-label">Keterangan / Ref. Mutasi</label>
                                            <input type="text" class="form-control" wire:model="topupDescription"
                                                   placeholder="Contoh: Topup manual transfer BCA">
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success px-4" wire:loading.attr="disabled">
                                            <span wire:loading.remove>Kirim Saldo</span>
                                            <span wire:loading><span
                                                    class="spinner-border spinner-border-sm me-2"></span> Memproses...</span>
                                        </button>
                                    </div>
                                </form>
                            @elseif($activeTab === 'pending_activations')
                                <div>
                                    <h5 class="mb-3 border-bottom pb-2">Antrean Aktivasi Toko (Manual & Gagal Setup)</h5>
                                    <p class="text-muted small mb-4">
                                        Daftar di bawah mencakup merchant yang mendaftar via <strong>Manual Transfer (WhatsApp)</strong> dan perlu persetujuan, serta merchant yang sudah membayar secara otomatis (PAID) namun proses setup servernya terganggu. Klik tombol aksi untuk memicu setup.
                                    </p>

                                    @if(count($pendingRegistrations) === 0)
                                        <div class="text-center py-5 text-muted">
                                            <i class="bi bi-check-circle fs-1 text-success mb-3 d-block"></i>
                                            <span>Tidak ada aktivasi berbayar yang tertunda. Semua server sehat dan teraktivasi!</span>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Invoice</th>
                                                        <th>Nama Toko / Subdomain</th>
                                                        <th>Pemilik / Kontak</th>
                                                        <th>Paket & Nilai</th>
                                                        <th class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pendingRegistrations as $reg)
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-secondary font-monospace">{{ $reg->invoice_code }}</span>
                                                                <span class="badge {{ $reg->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger' }} ms-1" style="font-size: 0.65rem;">{{ strtoupper($reg->status) }}</span>
                                                                <div class="text-muted small mt-1" style="font-size: 0.72rem;">{{ $reg->created_at->format('d/m/Y H:i') }}</div>
                                                            </td>
                                                            <td>
                                                                <div class="fw-bold">{{ $reg->store_name }}</div>
                                                                <div class="text-primary small font-monospace">{{ $reg->tenant_id }}.pakaiapp.online</div>
                                                            </td>
                                                            <td>
                                                                <div>{{ $reg->owner_name }}</div>
                                                                <div class="text-muted small" style="font-size: 0.8rem;">
                                                                    <i class="bi bi-envelope"></i> {{ $reg->email }} <br>
                                                                    <i class="bi bi-whatsapp"></i> {{ $reg->whatsapp }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-warning text-dark text-uppercase" style="font-size: 0.75rem;">{{ $reg->plan }}</span>
                                                                <div class="fw-bold mt-1 text-success" style="font-size: 0.85rem;">Rp {{ number_format($reg->amount, 0, ',', '.') }}</div>
                                                                <div class="text-muted small" style="font-size: 0.72rem;">via {{ strtoupper($reg->payment_method) }}</div>
                                                            </td>
                                                            <td class="text-center">
                                                                <button wire:click="retryCreateTenant({{ $reg->id }})" 
                                                                        class="btn btn-primary btn-sm"
                                                                        wire:loading.attr="disabled"
                                                                        wire:target="retryCreateTenant({{ $reg->id }})">
                                                                    <span wire:loading.remove wire:target="retryCreateTenant({{ $reg->id }})">
                                                                        <i class="bi bi-play-circle"></i> {{ $reg->payment_method === 'manual' ? 'Approve & Generate' : 'Aktivasi Paksa' }}
                                                                    </span>
                                                                    <span wire:loading wire:target="retryCreateTenant({{ $reg->id }})">
                                                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...
                                                                    </span>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
