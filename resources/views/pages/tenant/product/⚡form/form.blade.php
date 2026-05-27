<div class="container-fluid py-4 pb-5 pb-md-4" x-data="{ tab: 'general' }">

    {{-- Global Premium Sync Indicator --}}
    <div wire:loading.delay class="position-fixed top-0 start-50 translate-middle-x mt-3 z-3 animate-slide-down">
        <div class="d-flex align-items-center gap-2 px-3 py-2 bg-body-secondary border rounded-pill shadow-lg"
             style="backdrop-filter: blur(12px); border-color: rgba(182, 115, 50, 0.25) !important;">
            <div class="spinner-border spinner-border-sm" role="status"
                 style="width: 12px; height: 12px; color: var(--brand-caramel, #B67332);"></div>
            <span class="small fw-bolder text-secondary"
                  style="font-size: 0.7rem; letter-spacing: 0.5px; text-transform: uppercase;">Sinkronisasi...</span>
        </div>
    </div>

    {{-- Header --}}
    <div class="mb-4">
        <a href="{{ route('product') }}"
           class="btn-back-link text-decoration-none"
           wire:navigate>
            <i class="bi bi-chevron-left"></i> Kembali ke Produk
        </a>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div>
                <h2 class="brand-title fw-bolder mb-1 fs-3">
                    {{ $product ? 'Edit Produk' : 'Tambah Produk Baru' }}
                </h2>
                <p class="text-secondary small mb-0 fw-medium">Lengkapi informasi dan harga untuk menu/produkmu.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" @click="window.dispatchEvent(new CustomEvent('start-form-tour'))"
                        class="btn btn-outline-secondary rounded-pill fw-bold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm"
                        style="border-color: var(--bs-border-color) !important; color: var(--bs-body-color); font-size: 0.85rem;">
                    <i class="bi bi-patch-question-fill" style="color: #F97316 !important;"></i>
                    <span>Panduan Pengisian</span>
                </button>
                <a href="{{ route('product') }}"
                   class="btn btn-back-premium d-none d-md-inline-flex"
                   wire:navigate>
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="alert alert-danger rounded-4 shadow-sm mb-4 border-0 border-start border-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row g-4">

            {{-- Sidebar / Mobile Tabs Navigation --}}
            <div class="col-md-3">
                <div class="position-sticky" style="top: 1.5rem; z-index: 10;">
                    <div class="mobile-tabs hide-scrollbar d-md-flex flex-md-column gap-md-2">
                        <button type="button" class="btn btn-tab text-start fw-bold p-3"
                                :class="tab === 'general' ? 'btn-primary shadow-sm' : ''"
                                @click="tab = 'general'">
                            <i class="bi bi-box-seam me-2"></i> Data Umum
                        </button>
                        <button type="button" class="btn btn-tab text-start fw-bold p-3"
                                :class="tab === 'pricing' ? 'btn-primary shadow-sm' : ''"
                                @click="tab = 'pricing'">
                            <i class="bi bi-tags me-2"></i> Harga & Varian
                        </button>
                        @if(tenant('store_type') === 'resto')
                            <button type="button" class="btn btn-tab text-start fw-bold p-3"
                                    :class="tab === 'extras' ? 'btn-primary shadow-sm' : ''"
                                    @click="tab = 'extras'">
                                <i class="bi bi-plus-circle-dotted me-2"></i> Add-ons
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Main Form Content --}}
            <div class="col-md-9 col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3 p-sm-4 p-lg-5">

                        {{-- TAB 1: GENERAL INFO --}}
                        <div x-show="tab === 'general'" x-transition.opacity>
                            <h5 class="fw-bold mb-4" style="color: var(--brand-caramel, #B67332);">
                                <i class="bi bi-info-square me-2"></i>Informasi Umum
                            </h5>

                            <div class="row g-4 flex-column-reverse flex-md-row">
                                <div class="col-md-8 col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                               class="form-control"
                                               wire:model="name" id="productName" placeholder="Nama Produk">
                                        <label for="productName" class="fw-medium">Nama Produk/Menu <span
                                                class="text-danger">*</span></label>
                                        @error('name') <span
                                            class="invalid-feedback d-block mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select
                                            class="form-select"
                                            wire:model.live="categoryId" id="categorySelect">
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <label for="categorySelect" class="fw-medium">Kategori <span
                                                class="text-danger">*</span></label>
                                        @error('categoryId') <span
                                            class="invalid-feedback d-block mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                        <textarea class="form-control"
                                                  wire:model="description" id="productDesc" style="height: 120px;"
                                                  placeholder="Deskripsi"></textarea>
                                        <label for="productDesc" class="fw-medium">Deskripsi Singkat (Opsional)</label>
                                    </div>
                                </div>

                                <div class="col-md-4 col-12">
                                    <div class="text-center mb-2 fw-bold text-secondary small">Foto Produk</div>
                                    <div class="upload-zone position-relative overflow-hidden mx-auto shadow-sm"
                                         style="width: 100%; aspect-ratio: 1/1; max-width: 230px;">
                                        @if ($image)
                                            @php try { $url = $image->temporaryUrl(); } catch (Exception $e) { $url = ''; } @endphp
                                            @if($url)
                                                <img src="{{ $url }}" class="object-fit-cover w-100 h-100" alt="">
                                            @endif
                                        @elseif($product && $product->image)
                                            <img src="{{ Storage::url($product->image) }}"
                                                 class="object-fit-cover w-100 h-100" alt="">
                                        @else
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-75">
                                                <i class="bi bi-camera fs-1 mb-2"></i>
                                                <small class="fw-bold">Ketuk untuk Upload</small>
                                            </div>
                                        @endif
                                        <input type="file" wire:model="image" accept="image/*"
                                               class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer">
                                    </div>
                                    <div wire:loading wire:target="image"
                                         class="mt-2 text-center w-100 small text-warning fw-bold">
                                        <i class="bi bi-arrow-repeat spin"></i> Mengunggah...
                                    </div>

                                    <div class="form-item-card d-flex justify-content-between align-items-center mt-4">
                                        <div>
                                            <div class="fw-bold small">Status Tampil</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">Tampilkan menu ini ke
                                                pelanggan
                                            </div>
                                        </div>
                                        <div class="form-check form-switch fs-4 m-0 p-0">
                                            <input class="form-check-input m-0 cursor-pointer shadow-none"
                                                   type="checkbox" role="switch" wire:model="isActive">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: PRICING & VARIANTS --}}
                        <div x-show="tab === 'pricing'" x-transition.opacity x-cloak>
                            <h5 class="fw-bold mb-4" style="color: var(--brand-caramel, #B67332);">
                                <i class="bi bi-tags me-2"></i>Harga & Varian
                            </h5>

                            {{-- Variant Toggle Card --}}
                            <div class="form-item-card mb-4 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="bg-body text-primary p-2 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                        style="width: 45px; height: 45px; border: 1px solid var(--bs-border-color);">
                                        <i class="bi bi-diagram-3 fs-5"
                                           style="color: var(--brand-caramel, #B67332);"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Gunakan Varian Produk</h6>
                                        <p class="text-secondary mb-0" style="font-size: 0.75rem;">Aktifkan jika punya
                                            Ukuran (S/M/L) atau Rasa.</p>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-3 m-0 p-0">
                                    <input class="form-check-input m-0 cursor-pointer shadow-none" type="checkbox"
                                           role="switch" wire:model.live="hasVariants">
                                </div>
                            </div>

                            {{-- Selection Mode --}}
                            @if($hasVariants && $selectedCategoryType === 'resto')
                                <div class="form-item-card mb-4 border-warning border-opacity-25"
                                     style="box-shadow: 0 4px 20px rgba(182, 115, 50, 0.04);">
                                    <h6 class="fw-bold small mb-3" style="color: var(--brand-caramel, #B67332);"><i
                                            class="bi bi-ui-checks-grid me-2"></i>Aturan Pilihan Pelanggan</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6 col-12">
                                            <div class="form-floating">
                                                <select class="form-select"
                                                        wire:model.live="selectionType" id="selType">
                                                    <option value="single">Pilih 1 (Radio) — Ex: Ukuran/Warna</option>
                                                    <option value="multiple">Pilih Banyak (Checkbox) — Ex: Rasa</option>
                                                </select>
                                                <label for="selType" class="fw-medium">Tipe Seleksi</label>
                                            </div>
                                        </div>
                                        @if($selectionType === 'multiple')
                                            <div class="col-md-6 col-12">
                                                <div class="form-floating">
                                                    <input type="number"
                                                           class="form-control"
                                                           wire:model="maxSelections" min="1" max="20" id="maxSel">
                                                    <label for="maxSel" class="fw-medium">Batas Maksimal Pilihan</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- NO VARIANTS: Simple Pricing --}}
                            @if(!$hasVariants)
                                <div class="row g-3 form-item-card mx-0">
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small fw-bold text-secondary mb-1">Modal / HPP</label>
                                        <input type="number" class="form-control bg-body"
                                               wire:model="baseCost" placeholder="Rp 0">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small fw-bold text-danger mb-1">Harga Jual *</label>
                                        <input type="number" class="form-control bg-body fw-bold"
                                               wire:model="basePrice" id="productPrice" placeholder="Rp 0" required
                                               style="color: var(--brand-caramel, #B67332);">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small fw-bold text-secondary mb-1">Stok Saat
                                            Ini</label>
                                        <input type="number" class="form-control bg-body"
                                               wire:model="baseStock" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small fw-bold text-secondary mb-1">Notif Stok
                                            Tipis</label>
                                        <input type="number" class="form-control bg-body"
                                               wire:model="baseMinStock" placeholder="0">
                                    </div>
                                </div>
                            @endif

                            {{-- YES VARIANTS: Card List Form --}}
                            @if($hasVariants)
                                <div class="d-none d-md-flex row fw-bold text-secondary small px-3 mb-2 mt-4">
                                    <div class="col-md-3">Nama Varian</div>
                                    <div class="col-md-3">Modal (Rp)</div>
                                    <div class="col-md-3">Harga Jual (Rp)</div>
                                    <div class="col-md-2">Stok</div>
                                    <div class="col-md-1 text-center"><i class="bi bi-gear"></i></div>
                                </div>

                                <div class="d-flex flex-column gap-2 mb-3">
                                    @foreach($variants as $index => $variant)
                                        <div class="form-item-card">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-12 col-md-3">
                                                    <label class="d-md-none small fw-bold text-secondary mb-1">Nama
                                                        Varian</label>
                                                    <input type="text"
                                                           class="form-control shadow-sm"
                                                           wire:model="variants.{{ $index }}.name"
                                                           placeholder="Misal: Large" required>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <label
                                                        class="d-md-none small fw-bold text-secondary mb-1">Modal</label>
                                                    <input type="number"
                                                           class="form-control shadow-sm"
                                                           wire:model="variants.{{ $index }}.cost" placeholder="0">
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <label class="d-md-none small fw-bold text-danger mb-1">Harga Jual
                                                        *</label>
                                                    <input type="number"
                                                           class="form-control shadow-sm fw-bold text-primary"
                                                           wire:model="variants.{{ $index }}.price" placeholder="0"
                                                           required>
                                                </div>
                                                <div class="col-10 col-md-2">
                                                    <label
                                                        class="d-md-none small fw-bold text-secondary mb-1">Stok</label>
                                                    <input type="number"
                                                           class="form-control shadow-sm"
                                                           wire:model="variants.{{ $index }}.stock" placeholder="0">
                                                </div>
                                                <div class="col-2 col-md-1 text-end text-md-center">
                                                    @if(count($variants) > 1)
                                                        <button type="button"
                                                                class="btn btn-glass-danger shadow-sm d-inline-flex align-items-center justify-content-center"
                                                                style="width: 38px; height: 38px; padding: 0;"
                                                                wire:click="removeVariant({{ $index }})">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button"
                                        class="btn btn-outline-brand fw-bold shadow-sm px-4 py-2 mt-2"
                                        wire:click="addVariant">
                                    <i class="bi bi-plus-circle-dotted me-1"></i> Tambah Varian Baru
                                </button>
                            @endif
                        </div>

                        @if($selectedCategoryType === 'resto')
                            {{-- TAB 3: EXTRAS / ADD-ONS --}}
                            <div x-show="tab === 'extras'" x-transition.opacity x-cloak>
                                <h5 class="fw-bold mb-4" style="color: var(--brand-caramel, #B67332);">
                                    <i class="bi bi-plus-circle me-2"></i>Add-ons / Ekstra
                                </h5>

                                <div class="d-none d-md-flex row fw-bold text-secondary small px-3 mb-2">
                                    <div class="col-md-5">Nama Add-on (Ex: Ekstra Keju)</div>
                                    <div class="col-md-3">Modal (Rp)</div>
                                    <div class="col-md-3">Harga Jual (Rp)</div>
                                    <div class="col-md-1 text-center"><i class="bi bi-gear"></i></div>
                                </div>

                                <div class="d-flex flex-column gap-2 mb-3">
                                    @foreach($extras as $index => $extra)
                                        <div class="form-item-card">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-12 col-md-5">
                                                    <label class="d-md-none small fw-bold text-secondary mb-1">Nama
                                                        Add-on</label>
                                                    <input type="text"
                                                           class="form-control shadow-sm"
                                                           wire:model="extras.{{ $index }}.name"
                                                           placeholder="Misal: Shot Espresso">
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <label
                                                        class="d-md-none small fw-bold text-secondary mb-1">Modal</label>
                                                    <input type="number"
                                                           class="form-control shadow-sm"
                                                           wire:model="extras.{{ $index }}.cost" placeholder="0">
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <label class="d-md-none small fw-bold text-danger mb-1">Harga
                                                        Jual</label>
                                                    <input type="number"
                                                           class="form-control shadow-sm fw-bold text-primary"
                                                           wire:model="extras.{{ $index }}.price" placeholder="0">
                                                </div>
                                                <div class="col-12 col-md-1 text-end text-md-center mt-2 mt-md-0">
                                                    <button type="button"
                                                            class="btn btn-glass-danger shadow-sm d-inline-flex align-items-center justify-content-center"
                                                            style="width: 38px; height: 38px; padding: 0;"
                                                            wire:click="removeExtra({{ $index }})">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button"
                                        class="btn btn-outline-brand fw-bold shadow-sm px-4 py-2 mt-2"
                                        wire:click="addExtra">
                                    <i class="bi bi-plus-circle-dotted me-1"></i> Tambah Add-on Baru
                                </button>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- Desktop Action Buttons (Hidden on mobile) --}}
        <div class="d-none d-md-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <button type="button" class="btn btn-back-premium shadow-sm"
                    x-show="tab !== 'general'" @click="tab = tab === 'extras' ? 'pricing' : 'general'">
                <i class="bi bi-chevron-left"></i> Kembali
            </button>
            <button type="button" class="btn btn-tab btn-primary fw-bold px-5 py-2 shadow-sm tour-btn-next-price" x-show="tab === 'general'"
                    @click="tab = 'pricing'">
                Lanjut Harga <i class="bi bi-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-tab btn-primary fw-bold px-5 py-2 shadow-sm tour-btn-next-addons"
                    x-show="tab === 'pricing'" @click="tab = 'extras'">
                Lanjut Add-ons <i class="bi bi-chevron-right"></i>
            </button>
            <button type="submit"
                    class="btn brand-gradient-btn fw-bold px-5 py-2 shadow-sm d-flex align-items-center gap-2 tour-btn-submit"
                    x-show="tab === 'extras'"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save"><i class="bi bi-check2-circle"></i> Simpan Produk</span>
                <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm"></span> Menyimpan...</span>
            </button>
        </div>

        {{-- Sticky Mobile Footer Actions (Visible only on mobile) --}}
        <div class="d-md-none sticky-mobile-footer bg-body">
            <a href="{{ route('product') }}"
               class="btn btn-back-circle shadow-sm flex-shrink-0"
               x-show="tab === 'general'"
               wire:navigate>
                <i class="bi bi-chevron-left"></i>
            </a>

            <button type="button"
                    class="btn btn-back-circle shadow-sm flex-shrink-0"
                    x-show="tab !== 'general'" @click="tab = tab === 'extras' ? 'pricing' : 'general'">
                <i class="bi bi-chevron-left"></i>
            </button>

            <button type="button" class="btn brand-gradient-btn fw-bold shadow-sm flex-grow-1 tour-btn-next-price"
                    x-show="tab === 'general'" @click="tab = 'pricing'">
                Lanjut <i class="bi bi-chevron-right"></i>
            </button>

            <button type="button" class="btn brand-gradient-btn fw-bold shadow-sm flex-grow-1 tour-btn-next-addons"
                    x-show="tab === 'pricing'" @click="tab = 'extras'">
                Lanjut <i class="bi bi-chevron-right"></i>
            </button>

            <button type="submit"
                    class="btn brand-gradient-btn fw-bold shadow-sm flex-grow-1 d-flex justify-content-center align-items-center gap-2 tour-btn-submit"
                    x-show="tab === 'extras'"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save"><i class="bi bi-check2-circle"></i> Simpan</span>
                <span wire:loading wire:target="save"><span
                        class="spinner-border spinner-border-sm"></span> Loading</span>
            </button>
        </div>

    </form>

    <!-- Product Form Interactive Guide Component -->
    <x-tour-guide-form />
</div>
