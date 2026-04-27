<div class="container-fluid py-4" x-data="{ tab: 'general' }">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold font-serif text-dark mb-1">Tambah Produk Baru</h2>
            <p class="text-muted small mb-0">Lengkapi informasi produk atau menu restoranmu.</p>
        </div>
        <a href="{{ route('product') }}" class="btn btn-light rounded-pill border shadow-sm px-4" wire:navigate>
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if (session()->has('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0">{{ session('error') }}</div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row g-4">

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 position-sticky top-0">
                    <div class="list-group list-group-flush rounded-4">
                        <button type="button"
                                class="list-group-item list-group-item-action py-3 border-bottom-0 fw-bold"
                                :class="tab === 'general' ? 'text-brand bg-light' : 'text-muted'"
                                @click="tab = 'general'">
                            <i class="bi bi-box-seam me-2"></i> 1. Data Umum
                        </button>
                        <button type="button"
                                class="list-group-item list-group-item-action py-3 border-bottom-0 fw-bold"
                                :class="tab === 'pricing' ? 'text-brand bg-light' : 'text-muted'"
                                @click="tab = 'pricing'">
                            <i class="bi bi-tags me-2"></i> 2. Harga & Varian
                        </button>

                        @if($selectedCategoryType === 'fnb')
                            <button type="button"
                                    class="list-group-item list-group-item-action py-3 border-bottom-0 fw-bold"
                                    :class="tab === 'extras' ? 'text-brand bg-light' : 'text-muted'"
                                    @click="tab = 'extras'">
                                <i class="bi bi-plus-circle-dotted me-2"></i> 3. Add-ons / Ekstra
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card border-0 shadow-sm rounded-4 p-4">

                    <div x-show="tab === 'general'" x-transition.opacity>
                        <h5 class="fw-bold mb-4 border-bottom pb-2">Informasi Umum</h5>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Nama Produk/Menu <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-pill px-3" wire:model="name"
                                           placeholder="Misal: Kopi Susu Gula Aren">
                                    @error('name') <span
                                        class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Kategori <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select rounded-pill px-3" wire:model.live="category_id">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <span
                                        class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Deskripsi (Opsional)</label>
                                    <textarea class="form-control rounded-4 p-3" wire:model="description" rows="3"
                                              placeholder="Penjelasan singkat menu ini..."></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted d-block text-center">Foto
                                    Produk</label>
                                <div
                                    class="ratio ratio-1x1 rounded-4 overflow-hidden position-relative border border-2 border-dashed mx-auto bg-light">
                                    @if ($image)
                                        <img src="{{ $image->temporaryUrl() }}" class="object-fit-cover w-100 h-100"
                                             alt="">
                                    @else
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50">
                                            <i class="bi bi-camera fs-1"></i><small>Upload Foto</small>
                                        </div>
                                    @endif
                                    <input type="file" wire:model="image" accept="image/*"
                                           class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer">
                                </div>
                                <div wire:loading wire:target="image" class="mt-2 text-center w-100">
                                    <small class="text-brand fw-bold">Mengunggah...</small>
                                </div>

                                <div class="bg-light p-3 rounded-4 border mt-4">
                                    <div
                                        class="form-check form-switch d-flex justify-content-between align-items-center p-0 m-0">
                                        <label class="form-check-label small fw-bold text-dark" for="activeSwitch">Tampilkan
                                            di Menu</label>
                                        <input class="form-check-input ms-0 fs-4 cursor-pointer" type="checkbox"
                                               role="switch" id="activeSwitch" wire:model="is_active">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-brand rounded-pill px-4" @click="tab = 'pricing'">
                                Lanjut ke Harga <i class="bi bi-arrow-right ms-1"></i></button>
                        </div>
                    </div>

                    <div x-show="tab === 'pricing'" x-transition.opacity x-cloak>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                            <h5 class="fw-bold mb-0">Harga & Varian</h5>

                            <div class="form-check form-switch bg-light border px-3 py-1 rounded-pill">
                                <input class="form-check-input cursor-pointer mt-1" type="checkbox" role="switch"
                                       id="variantSwitch" wire:model.live="has_variants">
                                <label class="form-check-label small fw-bold ms-2" for="variantSwitch">Punya Varian
                                    (Ukuran/Level)</label>
                            </div>
                        </div>

                        @if(!$has_variants)
                            <div class="row g-3 bg-light p-3 rounded-4 border">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">HPP / Harga Modal</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-white border-end-0 rounded-start-pill text-muted small">Rp</span>
                                        <input type="number" class="form-control border-start-0 rounded-end-pill"
                                               wire:model="base_cost" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Harga Jual Katalog <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-white border-end-0 rounded-start-pill text-muted small">Rp</span>
                                        <input type="number" class="form-control border-start-0 rounded-end-pill"
                                               wire:model="base_price" placeholder="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Stok Saat Ini</label>
                                    <input type="number" class="form-control rounded-pill px-3" wire:model="base_stock"
                                           placeholder="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Notif Minimal Stok</label>
                                    <input type="number" class="form-control rounded-pill px-3"
                                           wire:model="base_min_stock" placeholder="0">
                                </div>
                            </div>
                        @endif

                        @if($has_variants)
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle">
                                    <thead class="bg-light text-muted small font-serif">
                                    <tr>
                                        <th class="rounded-start-4">Nama Varian (M, L, Pedas)</th>
                                        <th>HPP (Rp)</th>
                                        <th>Harga Jual (Rp)</th>
                                        <th>Stok</th>
                                        <th class="rounded-end-4 text-center">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($variants as $index => $variant)
                                        <tr class="border-bottom">
                                            <td><input type="text" class="form-control rounded-3"
                                                       wire:model="variants.{{ $index }}.name"
                                                       placeholder="Misal: Large" required></td>
                                            <td><input type="number" class="form-control rounded-3"
                                                       wire:model="variants.{{ $index }}.cost" placeholder="0"></td>
                                            <td><input type="number" class="form-control rounded-3 border-brand"
                                                       wire:model="variants.{{ $index }}.price" placeholder="0"
                                                       required></td>
                                            <td><input type="number" class="form-control rounded-3"
                                                       wire:model="variants.{{ $index }}.stock" placeholder="0"
                                                       style="width: 80px;"></td>
                                            <td class="text-center">
                                                @if(count($variants) > 1)
                                                    <button type="button"
                                                            class="btn btn-sm btn-light text-danger rounded-circle"
                                                            wire:click="removeVariant({{ $index }})"><i
                                                            class="bi bi-trash"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <button type="button"
                                        class="btn btn-sm btn-outline-brand rounded-pill px-3 mt-2 fw-bold shadow-sm"
                                        wire:click="addVariant">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Varian Lain
                                </button>
                            </div>
                        @endif

                        <div class="text-end mt-5 border-top pt-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 me-2 border shadow-sm"
                                    @click="tab = 'general'">Kembali
                            </button>

                            @if($selectedCategoryType === 'fnb')
                                <button type="button" class="btn btn-brand rounded-pill px-4" @click="tab = 'extras'">
                                    Lanjut ke Add-ons <i class="bi bi-arrow-right ms-1"></i></button>
                            @else
                                <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="save"><i
                                            class="bi bi-check2-circle me-1"></i> Simpan Produk</span>
                                    <span wire:loading wire:target="save"><span
                                            class="spinner-border spinner-border-sm me-1"></span> Menyimpan...</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($selectedCategoryType === 'fnb')
                        <div x-show="tab === 'extras'" x-transition.opacity x-cloak>
                            <div class="mb-4 border-bottom pb-2">
                                <h5 class="fw-bold mb-1">Add-ons / Ekstra</h5>
                                <p class="text-muted small">Tambahan opsional seperti Topping, Extra Shot, dll.</p>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-borderless align-middle">
                                    <thead class="bg-light text-muted small font-serif">
                                    <tr>
                                        <th class="rounded-start-4 w-50">Nama Add-on</th>
                                        <th>HPP (Rp)</th>
                                        <th>Harga Jual Tambahan (Rp)</th>
                                        <th class="rounded-end-4 text-center">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($extras as $index => $extra)
                                        <tr class="border-bottom">
                                            <td><input type="text" class="form-control rounded-3"
                                                       wire:model="extras.{{ $index }}.name"
                                                       placeholder="Misal: Extra Shot Espresso"></td>
                                            <td><input type="number" class="form-control rounded-3"
                                                       wire:model="extras.{{ $index }}.cost" placeholder="0"></td>
                                            <td><input type="number" class="form-control rounded-3"
                                                       wire:model="extras.{{ $index }}.price" placeholder="0"></td>
                                            <td class="text-center">
                                                <button type="button"
                                                        class="btn btn-sm btn-light text-danger rounded-circle"
                                                        wire:click="removeExtra({{ $index }})"><i
                                                        class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <button type="button"
                                        class="btn btn-sm btn-outline-dark rounded-pill px-3 mt-2 fw-bold shadow-sm"
                                        wire:click="addExtra">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Add-on
                                </button>
                            </div>

                            <div class="text-end mt-5 border-top pt-4">
                                <button type="button" class="btn btn-light rounded-pill px-4 me-2 border shadow-sm"
                                        @click="tab = 'pricing'">Kembali
                                </button>

                                <button type="submit"
                                        class="btn btn-brand rounded-pill px-5 fw-bold shadow-sm d-inline-flex align-items-center gap-2"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="save"><i
                                            class="bi bi-cloud-arrow-up fs-5"></i> Simpan Produk & Menu</span>
                                    <span wire:loading wire:target="save"><span
                                            class="spinner-border spinner-border-sm"></span> Menyimpan...</span>
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </form>
</div>
