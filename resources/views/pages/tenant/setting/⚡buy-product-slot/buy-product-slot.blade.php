<div class="card dash-card border bg-body p-1 shadow-sm"
     style="border-color: var(--bs-border-color-translucent) !important;">
    <div class="card-body p-3 p-md-4">

        {{-- INFO SALDO (BARU) --}}
        <div
            class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-4 bg-body-tertiary border shadow-sm"
            style="border-color: var(--bs-border-color-translucent) !important;">
            <div>
                <small class="text-secondary fw-bold d-block mb-1">
                    <i class="bi bi-wallet2 me-1"></i> Saldo Pakaiapp
                </small>
                <h4 class="fw-black mb-0 {{ $walletBalance < $price ? 'text-danger' : 'text-body' }}">
                    Rp {{ number_format($walletBalance, 0, ',', '.') }}
                </h4>
            </div>
            <div
                class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"
                style="width: 48px; height: 48px;">
                <i class="bi bi-credit-card-fill fs-4"></i>
            </div>
        </div>

        <hr class="border-secondary opacity-10 mb-4">

        {{-- INFO KAPASITAS ETALASE --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div
                    class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 d-flex justify-content-center align-items-center flex-shrink-0"
                    style="width: 45px; height: 45px;">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-body">Kapasitas Etalase Menu</h6>
                    <small class="text-secondary fw-medium">Kapasitas maksimal produk yang bisa ditampilkan</small>
                </div>
            </div>
        </div>

        {{-- Kalkulasi Persentase untuk Progress Bar --}}
        @php
            $percentage = ($quota->total_slots > 0) ? ($quota->used_slots / $quota->total_slots) * 100 : 0;
            $isWarning = $percentage >= 80; // Merah jika pemakaian sudah 80% ke atas
        @endphp

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-secondary small">Terpakai: <span
                        class="{{ $isWarning ? 'text-danger' : 'text-body' }}">{{ $quota->used_slots }}</span> / {{ $quota->total_slots }} Slot</span>
                <span
                    class="fw-bold small {{ $isWarning ? 'text-danger' : 'text-success' }}">{{ round($percentage) }}%</span>
            </div>
            <div class="progress" style="height: 10px; border-radius: 10px; background-color: var(--bs-secondary-bg);">
                <div class="progress-bar {{ $isWarning ? 'bg-danger' : 'bg-success' }} progress-bar-striped"
                     role="progressbar"
                     style="width: {{ $percentage }}%; border-radius: 10px;"
                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>

        <div
            class="mt-4 p-3 rounded-4 bg-body-tertiary border d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
            <div>
                <h6 class="fw-bold mb-1 text-body">Butuh Lebih Banyak Slot?</h6>
                <small class="text-secondary">Tambah <strong class="text-dark">+{{ $additionalSlots }} slot</strong>
                    cuma <strong>Rp {{ number_format($price, 0, ',', '.') }}</strong> (Permanen)</small>
            </div>

            {{-- Tombol Beli dengan SMART LOGIC Alpine.js & SweetAlert2 --}}
            <button type="button"
                    x-data="{ balance: {{ (float) $walletBalance }}, price: {{ $price }} }"
                    x-on:click="
                        if (balance < price) {
                            Swal.fire({
                                title: 'Saldo Tidak Cukup!',
                                html: `Sisa Saldo Pakaiapp Anda <b>Rp {{ number_format($walletBalance, 0, ',', '.') }}</b>.<br><br>Anda butuh <b>Rp {{ number_format($price, 0, ',', '.') }}</b> untuk menambah slot. Silakan hubungi Admin untuk Top Up.`,
                                icon: 'warning',
                                confirmButtonColor: '#F97316',
                                confirmButtonText: 'Tutup',
                                customClass: {
                                    confirmButton: 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm'
                                },
                                buttonsStyling: false
                            });
                        } else {
                            Swal.fire({
                                title: 'Upgrade Slot Menu?',
                                html: `Saldo Anda akan dipotong <b>Rp {{ number_format($price, 0, ',', '.') }}</b> untuk menambah <b>{{ $additionalSlots }} slot produk</b> secara permanen.`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#F97316',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: '<i class=\'bi bi-cart-check-fill me-1\'></i> Ya, Beli Sekarang',
                                cancelButtonText: 'Batal',
                                reverseButtons: true,
                                customClass: {
                                    confirmButton: 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm',
                                    cancelButton: 'btn btn-secondary rounded-pill px-4 fw-bold me-2'
                                },
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $wire.buySlot();
                                }
                            })
                        }
                    "
                    class="btn text-white fw-bold px-4 rounded-pill flex-shrink-0 border-0"
                    style="background-color: #F97316;"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="buySlot">
                    <i class="bi bi-cart-plus-fill me-1"></i> Beli Slot
                </span>
                <span wire:loading wire:target="buySlot">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Memproses...
                </span>
            </button>
        </div>

    </div>
</div>
