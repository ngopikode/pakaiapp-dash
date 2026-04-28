<div>
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end mb-4 gap-3">
        <div>
            <h2 class="fw-bolder text-dark mb-1" style="letter-spacing: -0.5px;">Manajemen User</h2>
            <p class="text-secondary mb-0 fw-medium">Kelola akses Kasir dan Admin untuk tokomu.</p>
        </div>

        <div class="d-flex gap-2">
            <div class="position-relative" style="min-width: 250px;">
                <i class="bi bi-search position-absolute text-muted fs-5"
                   style="top: 50%; left: 1.25rem; transform: translateY(-50%);"></i>
                <input type="text"
                       class="form-control form-control-lg rounded-pill border border-light shadow-sm ps-5 text-sm fw-medium transition-all"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nama / email...">
            </div>
            <button wire:click="$dispatch('openModal')"
                    class="btn btn-dark rounded-pill px-4 shadow-sm fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> <span class="d-none d-md-inline">Tambah User</span>
            </button>
        </div>
    </div>

    <div class="row row-cols-3 g-3 mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="text-muted small fw-bold text-uppercase">Total User</div>
                <h3 class="fw-bolder text-dark mb-0 mt-1">{{ $totalUsers }}</h3>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="text-muted small fw-bold text-uppercase">Manager</div>
                <h3 class="fw-bolder text-primary mb-0 mt-1">{{ $managerCount }}</h3>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="text-muted small fw-bold text-uppercase">Kasir</div>
                <h3 class="fw-bolder text-success mb-0 mt-1">{{ $cashierCount }}</h3>
            </div>
        </div>
    </div>

    <div class="position-relative" wire:poll.15s>

        <div wire:loading wire:target="search"
             class="w-100 position-absolute top-0 start-0 z-2 bg-light bg-opacity-75 rounded-4"
             style="min-height: 300px; backdrop-filter: blur(3px);">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 py-4 px-2">
                @for($i = 0; $i < 3; $i++)
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 p-4 placeholder-glow">
                            <div class="d-flex align-items-center gap-3">
                                <span class="placeholder rounded-circle" style="width: 50px; height: 50px;"></span>
                                <div class="d-flex flex-column gap-2 w-75">
                                    <span class="placeholder col-8 rounded"></span>
                                    <span class="placeholder col-5 rounded bg-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div wire:loading.remove wire:target="search" class="w-100">
            @if($users->isEmpty())
                <div
                    class="card border border-light border-2 border-dashed shadow-none rounded-4 text-center py-5">
                    <div class="card-body py-4">
                        <div
                            class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-people fs-2 text-muted"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak ada user</h5>
                        <p class="text-secondary mb-0">Belum ada user yang cocok dengan pencarian.</p>
                    </div>
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    @foreach($users as $user)
                        <div class="col" wire:key="user-{{ $user->id }}">
                            <div
                                class="card border border-light shadow-sm rounded-4 transition-all hover-shadow h-100">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div
                                                class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center fw-bolder shadow-sm border border-white"
                                                style="width: 50px; height: 50px; font-size: 1.25rem;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                        <span
                                            class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-success' }} bg-opacity-10 text-{{ $user->role == 'admin' ? 'primary' : 'success' }}-emphasis rounded-pill px-3 py-2 fw-bold text-capitalize">
                                            <i class="bi {{ $user->role == 'admin' ? 'bi-shield-lock' : 'bi-shop' }} me-1"></i> {{ $user->role }}
                                        </span>

                                        @if($user->id !== auth()->id())
                                            <div class="d-flex gap-2">
                                                <button
                                                    wire:click="$dispatch('openModal', { userId: {{ $user->id }} })"
                                                    class="btn btn-light btn-sm rounded-3 shadow-sm border px-2 py-1">
                                                    <i class="bi bi-pencil-square text-dark"></i>
                                                </button>
                                                <button wire:click="deleteUser({{ $user->id }})"
                                                        wire:confirm="Yakin ingin menghapus user ini?"
                                                        class="btn btn-light btn-sm rounded-3 shadow-sm border px-2 py-1">
                                                    <i class="bi bi-trash text-danger"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($users->hasMorePages())
                    <div x-intersect.full="$wire.loadMore()"
                         class="d-flex justify-content-center align-items-center py-5 mt-2">
                        <div class="spinner-border text-dark spinner-border-sm me-2" role="status"></div>
                        <span class="fw-bold text-muted small">Memuat lebih banyak user...</span>
                    </div>
                @else
                    <div class="text-center py-4 mt-3">
                        <div
                            class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-2"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-check2-all text-success"></i>
                        </div>
                        <p class="text-muted small fw-medium mb-0">Semua user telah ditampilkan.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
