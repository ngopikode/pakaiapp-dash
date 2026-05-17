<div class="container-fluid py-4 pb-5 pb-md-4" x-data="{ tab: 'general' }">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bolder mb-1" style="color: var(--brand-caramel, #b45309); letter-spacing: -0.5px;">
                {{ $product ? 'Edit Produk' : 'Tambah Produk Baru' }}
            </h2>
            <p class="text-secondary small mb-0 fw-medium">Lengkapi informasi dan harga untuk menu/produkmu.</p>
        </div>
        <a href="{{ route('product') }}"
           class="btn btn-outline-secondary border shadow-sm rounded-pill px-3 px-md-4 d-none d-md-flex align-items-center"
           wire:navigate>
            <i class="bi bi-arrow-left me-1"></i> Batal
        </a>
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
                <div class="position-sticky" style="top: 1.5rem;">
                    <div class="mobile-tabs hide-scrollbar d-md-flex flex-md-column gap-md-2">
                        <button type="button" class="btn btn-tab text-start fw-bold p-3 transition-all"
                                :class="tab === 'general' ? 'btn-primary shadow-sm' : 'border text-secondary bg-body'"
                                @click="tab = 'general'" style="border-radius: 1rem;">
                            <i class="bi bi-box-seam me-2"></i> Data Umum
                        </button>
                        <button type="button" class="btn btn-tab text-start fw-bold p-3 transition-all"
                                :class="tab === 'pricing' ? 'btn-primary shadow-sm' : 'border text-secondary bg-body'"
                                @click="tab = 'pricing'" style="border-radius: 1rem;">
                            <i class="bi bi-tags me-2"></i> Harga & Varian
                        </button>
                        @if($selectedCategoryType === 'resto')
                            <button type="button" class="btn btn-tab text-start fw-bold p-3 transition-all"
                                    :class="tab === 'extras' ? 'btn-primary shadow-sm' : 'border text-secondary bg-body'"
                                    @click="tab = 'extras'" style="border-radius: 1rem;">
                                <i class="bi bi-plus-circle-dotted me-2"></i> Add-ons
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Main Form Content --}}
            <div class="col-md-9">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-lg-5">

                        {{-- TAB 1: GENERAL INFO --}}
                        <div x-show="tab === 'general'" x-transition.opacity>
                            <h5 class="fw-bold mb-4"><i class="bi bi-info-square text-warning me-2"></i>Informasi Umum
                            </h5>

                            <div class="row g-4 flex-column-reverse flex-md-row">
                                <div class="col-md-8">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                               class="form-control rounded-3 bg-body-tertiary border-0 @error('name') is-invalid @enderror"
                                               wire:model="name" id="productName" placeholder="Nama Produk">
                                        <label for="productName" class="fw-medium text-muted">Nama Produk/Menu <span
                                                class="text-danger">*</span></label>
                                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select
                                            class="form-select rounded-3 bg-body-tertiary border-0 @error('categoryId') is-invalid @enderror"
                                            wire:model.live="categoryId" id="categorySelect">
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <label for="categorySelect" class="fw-medium text-muted">Kategori <span
                                                class="text-danger">*</span></label>
                                        @error('categoryId') <span
                                            class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                        <textarea class="form-control rounded-3 bg-body-tertiary border-0"
                                                  wire:model="description" id="productDesc" style="height: 100px;"
                                                  placeholder="Deskripsi"></textarea>
                                        <label for="productDesc" class="fw-medium text-muted">Deskripsi Singkat
                                            (Opsional)</label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-center mb-2 fw-bold text-muted small">Foto Produk</div>
                                    <div
                                        class="upload-zone position-relative overflow-hidden mx-auto shadow-sm bg-body-tertiary"
                                        style="width: 100%; aspect-ratio: 1/1; max-width: 250px; border: 2px dashed var(--bs-border-color); border-radius: 1.25rem;">
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

                                    <div
                                        class="p-3 bg-body-tertiary rounded-4 mt-4 d-flex justify-content-between align-items-center border shadow-sm">
                                        <div>
                                            <div class="fw-bold small">Status Tampil</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Sembunyikan jika kosong
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
                            <h5 class="fw-bold mb-4"><i class="bi bi-tags text-primary me-2"></i>Harga & Varian</h5>

                            {{-- Variant Toggle Card --}}
                            <div
                                class="bg-body-tertiary p-3 rounded-4 border shadow-sm mb-4 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="bg-body text-primary p-2 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                        style="width: 45px; height: 45px;">
                                        <i class="bi bi-diagram-3 fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Gunakan Varian Produk</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Aktifkan jika punya
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
                                <div class="p-3 p-md-4 rounded-4 border mb-4 border-warning border-opacity-50"
                                     style="box-shadow: 0 4px 15px rgba(202, 138, 4, 0.05);">
                                    <h6 class="fw-bold small mb-3 text-warning"><i
                                            class="bi bi-ui-checks-grid me-2"></i>Aturan Pilihan Pelanggan</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select bg-body-tertiary border-0 rounded-3"
                                                        wire:model.live="selectionType" id="selType">
                                                    <option value="single">Pilih 1 (Radio) — Ex: Ukuran/Warna</option>
                                                    <option value="multiple">Pilih Banyak (Checkbox) — Ex: Rasa</option>
                                                </select>
                                                <label for="selType" class="fw-medium text-muted">Tipe Seleksi</label>
                                            </div>
                                        </div>
                                        @if($selectionType === 'multiple')
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="number"
                                                           class="form-control bg-body-tertiary border-0 rounded-3"
                                                           wire:model="maxSelections" min="1" max="20" id="maxSel">
                                                    <label for="maxSel" class="fw-medium text-muted">Batas Maksimal
                                                        Pilihan</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- NO VARIANTS: Simple Pricing --}}
                            @if(!$hasVariants)
                                <div class="row g-3 p-3 p-md-4 border rounded-4 shadow-sm bg-body-tertiary">
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Modal / HPP</label>
                                        <input type="number" class="form-control bg-body border-0 rounded-3"
                                               wire:model="baseCost" placeholder="Rp 0">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small fw-bold text-danger mb-1">Harga Jual *</label>
                                        <input type="number" class="form-control bg-body border-0 rounded-3 fw-bold"
                                               wire:model="basePrice" placeholder="Rp 0" required
                                               style="color: var(--brand-caramel, #b45309);">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Stok Saat Ini</label>
                                        <input type="number" class="form-control bg-body border-0 rounded-3"
                                               wire:model="baseStock" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Notif Stok Tipis</label>
                                        <input type="number" class="form-control bg-body border-0 rounded-3"
                                               wire:model="baseMinStock" placeholder="0">
                                    </div>
                                </div>
                            @endif

                            {{-- YES VARIANTS: Card List Form --}}
                            @if($hasVariants)
                                <div class="d-none d-md-flex row fw-bold text-muted small px-3 mb-2 mt-4">
                                    <div class="col-md-3">Nama Varian</div>
                                    <div class="col-md-3">Modal (Rp)</div>
                                    <div class="col-md-3">Harga Jual (Rp)</div>
                                    <div class="col-md-2">Stok</div>
                                    <div class="col-md-1 text-center"><i class="bi bi-gear"></i></div>
                                </div>

                                <div class="d-flex flex-column gap-2 mb-3">
                                    @foreach($variants as $index => $variant)
                                        <div class="position-relative bg-body-tertiary p-3 rounded-4 border">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-12 col-md-3">
                                                    <label class="d-md-none small fw-bold text-muted mb-1">Nama
                                                        Varian</label>
                                                    <input type="text"
                                                           class="form-control bg-body border-0 shadow-sm rounded-3"
                                                           wire:model="variants.{{ $index }}.name"
                                                           placeholder="Misal: Large" required>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <label class="d-md-none small fw-bold text-muted mb-1">Modal</label>
                                                    <input type="number"
                                                           class="form-control bg-body border-0 shadow-sm rounded-3"
                                                           wire:model="variants.{{ $index }}.cost" placeholder="0">
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <label class="d-md-none small fw-bold text-danger mb-1">Harga Jual
                                                        *</label>
                                                    <input type="number"
                                                           class="form-control bg-body border-0 shadow-sm rounded-3 fw-bold text-primary"
                                                           wire:model="variants.{{ $index }}.price" placeholder="0"
                                                           required>
                                                </div>
                                                <div class="col-10 col-md-2">
                                                    <label class="d-md-none small fw-bold text-muted mb-1">Stok</label>
                                                    <input type="number"
                                                           class="form-control bg-body border-0 shadow-sm rounded-3"
                                                           wire:model="variants.{{ $index }}.stock" placeholder="0">
                                                </div>
                                                <div class="col-2 col-md-1 text-end text-md-center mt-4 mt-md-0">
                                                    @if(count($variants) > 1)
                                                        <button type="button"
                                                                class="btn btn-body text-danger shadow-sm rounded-circle p-2"
                                                                wire:click="removeVariant({{ $index }})"><i
                                                                class="bi bi-trash3"></i></button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button"
                                        class="btn btn-outline-secondary border fw-bold rounded-pill shadow-sm px-4"
                                        wire:click="addVariant">
                                    <i class="bi bi-plus-circle-dotted me-1"></i> Tambah Varian Baru
                                </button>
                            @endif
                        </div>

                        {{-- TAB 3: EXTRAS / ADD-ONS (F&B Only) --}}
                        @if($selectedCategoryType === 'resto')
                            <div x-show="tab === 'extras'" x-transition.opacity x-cloak>
                                <h5 class="fw-bold mb-4"><i class="bi bi-plus-circle text-success me-2"></i>Add-ons /
                                    Ekstra</h5>

                                <div class="d-none d-md-flex row fw-bold text-muted small px-3 mb-2">
                                    <div class="col-md-5">Nama Add-on (Ex: Ekstra Keju)</div>
                                    <div class="col-md-3">Modal (Rp)</div>
                                    <div class="col-md-3">Harga Jual (Rp)</div>
                                    <div class="col-md-1 text-center"><i class="bi bi-gear"></i></div>
                                </div>

                                <div class="d-flex flex-column gap-2 mb-3">
                                    @foreach($extras as $index => $extra)
                                        <div class="position-relative bg-body-tertiary p-3 rounded-4 border">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-12 col-md-5">
                                                    <label class="d-md-none small fw-bold text-muted mb-1">Nama
                                                        Add-on</label>
                                                    <input type="text"
                                                           class="form-control bg-body border-0 shadow-sm rounded-3"
                                                           wire:model="extras.{{ $index }}.name"
                                                           placeholder="Misal: Shot Espresso">
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <label class="d-md-none small fw-bold text-muted mb-1">Modal</label>
                                                    <input type="number"
                                                           class="form-control bg-body border-0 shadow-sm rounded-3"
                                                           wire:model="extras.{{ $index }}.cost" placeholder="0">
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <label class="d-md-none small fw-bold text-danger mb-1">Harga
                                                        Jual</label>
                                                    <input type="number"
                                                           class="form-control bg-body border-0 shadow-sm rounded-3 fw-bold text-primary"
                                                           wire:model="extras.{{ $index }}.price" placeholder="0">
                                                </div>
                                                <div
                                                    class="col-12 col-md-1 text-end text-md-center mt-3 mt-md-0 border-top border-md-none pt-2 pt-md-0">
                                                    <button type="button"
                                                            class="btn btn-sm btn-body text-danger shadow-sm rounded-pill w-100 w-md-auto"
                                                            wire:click="removeExtra({{ $index }})"><i
                                                            class="bi bi-trash3 d-md-none me-1"></i> Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button"
                                        class="btn btn-outline-success border fw-bold rounded-pill shadow-sm px-4"
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
            <button type="button" class="btn btn-outline-secondary border fw-bold rounded-pill px-4 shadow-sm"
                    x-show="tab !== 'general'" @click="tab = tab === 'extras' ? 'pricing' : 'general'">
                <i class="bi bi-arrow-left"></i> Kembali
            </button>
            <button type="button" class="btn btn-primary fw-bold rounded-pill px-5 shadow-sm" x-show="tab === 'general'"
                    @click="tab = 'pricing'">
                Lanjut Harga <i class="bi bi-arrow-right"></i>
            </button>
            @if($selectedCategoryType === 'resto')
                <button type="button" class="btn btn-primary fw-bold rounded-pill px-5 shadow-sm"
                        x-show="tab === 'pricing'" @click="tab = 'extras'">
                    Lanjut Add-ons <i class="bi bi-arrow-right"></i>
                </button>
            @endif
            <button type="submit"
                    class="btn fw-bold rounded-pill px-5 shadow-sm d-flex align-items-center gap-2 text-white"
                    style="background: linear-gradient(135deg, #ca8a04, #b45309); border: none;"
                    x-show="tab === '{{ $selectedCategoryType === 'resto' ? 'extras' : 'pricing' }}'"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save"><i class="bi bi-check2-circle"></i> Simpan Produk</span>
                <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm"></span> Menyimpan...</span>
            </button>
        </div>

        {{-- Sticky Mobile Footer Actions (Visible only on mobile) --}}
        <div class="d-md-none sticky-mobile-footer bg-body"
             style="position: fixed; bottom: 0; left: 0; width: 100%; padding: 1rem; box-shadow: 0 -4px 20px rgba(0,0,0,0.1); z-index: 1030; display: flex; gap: 0.5rem; border-top: 1px solid var(--bs-border-color);">
            <button type="button"
                    class="btn btn-outline-secondary border fw-bold rounded-pill shadow-sm flex-shrink-0 px-3"
                    x-show="tab !== 'general'" @click="tab = tab === 'extras' ? 'pricing' : 'general'">
                <i class="bi bi-arrow-left"></i>
            </button>

            <button type="button" class="btn btn-primary fw-bold rounded-pill shadow-sm flex-grow-1"
                    x-show="tab === 'general'" @click="tab = 'pricing'">
                Lanjut <i class="bi bi-arrow-right"></i>
            </button>

            @if($selectedCategoryType === 'resto')
                <button type="button" class="btn btn-primary fw-bold rounded-pill shadow-sm flex-grow-1"
                        x-show="tab === 'pricing'" @click="tab = 'extras'">
                    Lanjut <i class="bi bi-arrow-right"></i>
                </button>
            @endif

            <button type="submit"
                    class="btn fw-bold rounded-pill shadow-sm flex-grow-1 d-flex justify-content-center align-items-center gap-2 text-white"
                    style="background: linear-gradient(135deg, #ca8a04, #b45309); border: none;"
                    x-show="tab === '{{ $selectedCategoryType === 'resto' ? 'extras' : 'pricing' }}'"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save"><i class="bi bi-check2-circle"></i> Simpan</span>
                <span wire:loading wire:target="save"><span
                        class="spinner-border spinner-border-sm"></span> Loading</span>
            </button>
        </div>

    </form>
</div>
