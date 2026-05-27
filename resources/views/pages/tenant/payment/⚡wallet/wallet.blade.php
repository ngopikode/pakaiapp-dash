<div class="pb-5">

    {{-- ─── HEADER ──────────────────────────────────────────────────── --}}
    <div class="mb-5 pt-2">
        <h2 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">Dompet</h2>
        <p class="text-secondary small mb-0 fw-medium">Pantau saldo dan riwayat mutasi wallet toko Anda.</p>
    </div>

    {{-- ─── BALANCE CARDS ────────────────────────────────────────────── --}}
    <div class="row g-4 mb-5">

        {{-- Saldo Aktif --}}
        <div class="col-12 col-md-5">
            <div class="card border shadow-sm rounded-4 overflow-hidden h-100 position-relative bg-body"
                 style="border-color: var(--bs-border-color-translucent) !important; min-height: 160px;">

                <div class="card-body p-4 position-relative d-flex flex-column justify-content-between" style="z-index:1;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 fw-bold text-secondary" style="font-size:0.78rem; letter-spacing:1.5px; text-transform:uppercase;">Saldo Aktif</p>
                            <h2 class="fw-black mb-0 text-body" style="font-size: clamp(1.6rem, 4vw, 2.2rem); letter-spacing:-1px;">
                                Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                            </h2>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:rgba(249, 115, 22, 0.1); color: #F97316;">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                    <p class="small mb-0 text-secondary mt-3">
                        <i class="bi bi-info-circle me-1"></i>Saldo digunakan untuk biaya layanan pakaiapp (Rp 300/transaksi).
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Masuk & Keluar --}}
        <div class="col-12 col-md-7">
            <div class="row g-4 h-100">
                <div class="col-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="background: rgba(22,163,74,0.06);">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                 style="width:38px;height:38px;background:rgba(22,163,74,0.12);">
                                <i class="bi bi-arrow-down-circle-fill text-success fs-5"></i>
                            </div>
                            <span class="small fw-bold text-muted text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Total Masuk</span>
                        </div>
                        <h4 class="fw-black mb-0 text-success" style="letter-spacing:-0.5px;">
                            Rp {{ number_format($totalCredit, 0, ',', '.') }}
                        </h4>
                        <p class="small text-muted mb-0 mt-1">Semua CREDIT</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="background: rgba(239,68,68,0.05);">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                 style="width:38px;height:38px;background:rgba(239,68,68,0.1);">
                                <i class="bi bi-arrow-up-circle-fill text-danger fs-5"></i>
                            </div>
                            <span class="small fw-bold text-muted text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Total Keluar</span>
                        </div>
                        <h4 class="fw-black mb-0 text-danger" style="letter-spacing:-0.5px;">
                            Rp {{ number_format($totalDebit, 0, ',', '.') }}
                        </h4>
                        <p class="small text-muted mb-0 mt-1">Semua DEBIT</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── TRANSACTION HISTORY ──────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        {{-- Header + Filter + Search --}}
        <div class="card-header bg-body border-bottom-0 p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bolder mb-0">Riwayat Mutasi</h5>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    {{-- Filter Tabs --}}
                    <div class="btn-group border rounded-3 overflow-hidden shadow-sm" role="group">
                        <button type="button" wire:click="$set('filter','all')"
                                class="btn btn-sm fw-bold px-3 {{ $filter === 'all' ? 'btn-dark' : 'btn-outline-secondary border-0' }}">
                            Semua
                        </button>
                        <button type="button" wire:click="$set('filter','credit')"
                                class="btn btn-sm fw-bold px-3 {{ $filter === 'credit' ? 'btn-success' : 'btn-outline-secondary border-0' }}">
                            <i class="bi bi-arrow-down me-1"></i>Masuk
                        </button>
                        <button type="button" wire:click="$set('filter','debit')"
                                class="btn btn-sm fw-bold px-3 {{ $filter === 'debit' ? 'btn-danger' : 'btn-outline-secondary border-0' }}">
                            <i class="bi bi-arrow-up me-1"></i>Keluar
                        </button>
                    </div>
                    {{-- Search --}}
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute text-muted" style="top:50%;left:0.9rem;transform:translateY(-50%);pointer-events:none;"></i>
                        <input type="text" wire:model.live.debounce.300ms="search"
                               class="form-control rounded-3 ps-5" style="min-width:220px;"
                               placeholder="Cari keterangan...">
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading skeleton --}}
        <div wire:loading wire:target="filter,search" class="px-4 pb-4">
            @for($i = 0; $i < 5; $i++)
                <div class="d-flex align-items-center gap-3 py-3 border-bottom">
                    <div class="skeleton-block rounded-circle" style="width:40px;height:40px;flex-shrink:0;"></div>
                    <div class="flex-grow-1">
                        <div class="skeleton-block mb-2" style="width:60%;height:14px;"></div>
                        <div class="skeleton-block" style="width:35%;height:11px;"></div>
                    </div>
                    <div class="skeleton-block" style="width:100px;height:18px;border-radius:2rem;"></div>
                </div>
            @endfor
        </div>

        {{-- Table --}}
        <div wire:loading.remove wire:target="filter,search">
            @if($transactions->isEmpty())
                <div class="text-center py-5">
                    <div class="bg-body-tertiary rounded-circle d-inline-flex p-4 mb-3 text-muted border">
                        <i class="bi bi-wallet2 fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Belum Ada Mutasi</h6>
                    <p class="text-muted small mb-0">Riwayat transaksi akan muncul di sini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" style="font-size:0.9rem;">
                        <thead class="bg-body-tertiary">
                            <tr>
                                <th class="px-4 py-3 fw-bold text-muted" style="font-size:0.75rem;letter-spacing:0.8px;text-transform:uppercase;">Waktu</th>
                                <th class="px-3 py-3 fw-bold text-muted" style="font-size:0.75rem;letter-spacing:0.8px;text-transform:uppercase;">Keterangan</th>
                                <th class="px-3 py-3 fw-bold text-muted text-center" style="font-size:0.75rem;letter-spacing:0.8px;text-transform:uppercase;">Tipe</th>
                                <th class="px-3 py-3 fw-bold text-muted text-end" style="font-size:0.75rem;letter-spacing:0.8px;text-transform:uppercase;">Jumlah</th>
                                <th class="px-4 py-3 fw-bold text-muted text-end" style="font-size:0.75rem;letter-spacing:0.8px;text-transform:uppercase;">Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $tx)
                                <tr wire:key="tx-{{ $tx->id }}">
                                    <td class="px-4 py-3">
                                        <div class="fw-medium text-body">{{ $tx->created_at->format('d M Y') }}</div>
                                        <div class="small text-muted">{{ $tx->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-3 py-3" style="max-width:280px;">
                                        <div class="text-body fw-medium text-truncate" title="{{ $tx->description }}">
                                            {{ $tx->description ?? '-' }}
                                        </div>
                                        @if($tx->reference_id)
                                            <div class="small text-muted">Ref #{{ $tx->reference_id }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        @if($tx->type === 'CREDIT')
                                            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background:rgba(22,163,74,0.1);color:#16a34a;">
                                                <i class="bi bi-arrow-down-short"></i> Masuk
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background:rgba(239,68,68,0.08);color:#dc2626;">
                                                <i class="bi bi-arrow-up-short"></i> Keluar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-end fw-bold"
                                        style="color: {{ $tx->type === 'CREDIT' ? '#16a34a' : '#dc2626' }};">
                                        {{ $tx->type === 'CREDIT' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-end fw-bold text-body">
                                        Rp {{ number_format($tx->closing_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($transactions->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>
