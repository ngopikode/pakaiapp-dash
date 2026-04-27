<div class="container-fluid py-4" x-data="{ tab: 'general' }">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold font-serif text-primary mb-1">
                {{ $product ? 'Edit Produk' : 'Tambah Produk Baru' }}
            </h2>
            <p class="text-muted small mb-0">Lengkapi informasi produk atau menu restoranmu.</p>
        </div>
        <a href="{{ route('product') }}" class="btn btn-outline-secondary px-4" style="border-radius: 0.75rem;"
           wire:navigate>
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if (session()->has('error'))
        <div class="alert alert-danger" style="border-radius: 1rem;">{{ session('error') }}</div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row g-4">

            <div class="col-md-3">
                <div class="card position-sticky top-0 p-2">
                    <div class="list-group list-group-flush">
                        <button type="button" class="list-group-item list-group-item-action"
                                :class="tab === 'general' ? 'active' : ''" @click="tab = 'general'">
                            <i class="bi bi-box-seam"></i> 1. Data Umum
                        </button>
                        <button type="button" class="list-group-item list-group-item-action"
                                :class="tab === 'pricing' ? 'active' : ''" @click="tab = 'pricing'">
                            <i class="bi bi-tags"></i> 2. Harga & Varian
                        </button>
                        @if($selectedCategoryType === 'fnb')
                            <button type="button" class="list-group-item list-group-item-action"
                                    :class="tab === 'extras' ? 'active' : ''" @click="tab = 'extras'">
                                <i class="bi bi-plus-circle-dotted"></i> 3. Add-ons
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card p-4">

                    <div x-show="tab === 'general'" x-transition.opacity>
                        <h5 class="fw-bold mb-4 border-bottom pb-2 font-serif text-primary">Informasi Umum</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Nama Produk/Menu <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="name"
                                           placeholder="Misal: Kopi Susu Gula Aren">
                                    @error('name') <span
                                        class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Kategori <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" wire:model.live="categoryId">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('categoryId') <span
                                        class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Deskripsi (Opsional)</label>
                                    <textarea class="form-control" wire:model="description" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted d-block text-center">Foto
                                    Produk</label>
                                <div
                                    class="ratio ratio-1x1 overflow-hidden position-relative border border-2 border-dashed mx-auto bg-body-tertiary"
                                    style="border-radius: 1.25rem;">

                                    @if ($image)
                                        @php
                                            try { $url = $image->temporaryUrl(); }
                                            catch (Exception $e) { $url = ''; }
                                        @endphp
                                        @if($url)
                                            <img src="{{ $url }}" class="object-fit-cover w-100 h-100" alt="">
                                        @else
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-center h-100 text-success">
                                                <i class="bi bi-check-circle fs-1"></i><small>Foto Dipilih</small>
                                            </div>
                                        @endif
                                    @elseif($product && $product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                             class="object-fit-cover w-100 h-100" alt="">
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
                                    <small class="text-primary fw-bold">Mengunggah...</small>
                                </div>
                                <div class="bg-body-tertiary p-3 border mt-4" style="border-radius: 1rem;">
                                    <div
                                        class="form-check form-switch d-flex justify-content-between align-items-center p-0 m-0">
                                        <label class="form-check-label small fw-bold" for="activeSwitch">Tampilkan di
                                            Menu</label>
                                        <input class="form-check-input ms-0 fs-4 cursor-pointer" type="checkbox"
                                               role="switch" id="activeSwitch" wire:model="isActive">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-primary" @click="tab = 'pricing'">Lanjut ke Harga <i
                                    class="bi bi-arrow-right ms-1"></i></button>
                        </div>
                    </div>

                    <div x-show="tab === 'pricing'" x-transition.opacity x-cloak>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                            <h5 class="fw-bold mb-0 font-serif text-primary">Harga & Varian</h5>
                        </div>

                        <div class="rounded-4 border mb-4" style="border-color: #e9ecef !important;">
                            <div class="d-flex align-items-center justify-content-between py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-brand-light text-brand p-2 rounded-3">
                                        <i class="bi bi-diagram-2"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold small">Gunakan Varian</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Aktifkan untuk ukuran,
                                            warna, atau rasa.</p>
                                    </div>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input cursor-pointer shadow-none" type="checkbox"
                                           role="switch"
                                           id="variantSwitch" wire:model.live="hasVariants"
                                           style="transform: scale(1.4);">
                                </div>
                            </div>
                        </div>
                        @if(!$hasVariants)
                            <div class="row g-3 bg-body-tertiary p-3 border" style="border-radius: 1rem;">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">HPP / Modal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-card text-muted small">Rp</span>
                                        <input type="number" class="form-control" wire:model="baseCost" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Harga Jual <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-card text-muted small">Rp</span>
                                        <input type="number" class="form-control" wire:model="basePrice" placeholder="0"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Stok Saat Ini</label>
                                    <input type="number" class="form-control" wire:model="baseStock" placeholder="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Notif Minimal Stok</label>
                                    <input type="number" class="form-control" wire:model="baseMinStock" placeholder="0">
                                </div>
                            </div>
                        @endif

                        @if($hasVariants)
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle">
                                    <thead class="bg-body-tertiary text-muted small font-serif">
                                    <tr>
                                        <th style="border-top-left-radius: 1rem; border-bottom-left-radius: 1rem;">Nama
                                            Varian
                                        </th>
                                        <th>HPP (Rp)</th>
                                        <th>Harga Jual (Rp)</th>
                                        <th>Stok</th>
                                        <th class="text-center"
                                            style="border-top-right-radius: 1rem; border-bottom-right-radius: 1rem;">
                                            Aksi
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($variants as $index => $variant)
                                        <tr class="border-bottom">
                                            <td><input type="text" class="form-control"
                                                       wire:model="variants.{{ $index }}.name" required></td>
                                            <td><input type="number" class="form-control"
                                                       wire:model="variants.{{ $index }}.cost" placeholder="0"></td>
                                            <td><input type="number" class="form-control border-primary"
                                                       wire:model="variants.{{ $index }}.price" placeholder="0"
                                                       required></td>
                                            <td><input type="number" class="form-control"
                                                       wire:model="variants.{{ $index }}.stock" placeholder="0"
                                                       style="width: 80px;"></td>
                                            <td class="text-center">
                                                @if(count($variants) > 1)
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            style="border-radius: 0.5rem;"
                                                            wire:click="removeVariant({{ $index }})"><i
                                                            class="bi bi-trash"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold"
                                        style="border-radius: 0.75rem;" wire:click="addVariant">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Varian
                                </button>
                            </div>
                        @endif

                        <div class="text-end mt-5 border-top pt-4">
                            <button type="button" class="btn btn-outline-secondary me-2" @click="tab = 'general'">
                                Kembali
                            </button>
                            @if($selectedCategoryType === 'fnb')
                                <button type="button" class="btn btn-primary" @click="tab = 'extras'">Lanjut ke Add-ons
                                    <i class="bi bi-arrow-right ms-1"></i></button>
                            @else
                                <button type="submit" class="btn btn-primary fw-bold" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="save"><i
                                            class="bi bi-check2-circle me-1"></i> Simpan</span>
                                    <span wire:loading wire:target="save"><span
                                            class="spinner-border spinner-border-sm me-1"></span> Menyimpan...</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($selectedCategoryType === 'fnb')
                        <div x-show="tab === 'extras'" x-transition.opacity x-cloak>
                            <div class="mb-4 border-bottom pb-2">
                                <h5 class="fw-bold mb-1 font-serif text-primary">Add-ons / Ekstra</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle">
                                    <thead class="bg-body-tertiary text-muted small font-serif">
                                    <tr>
                                        <th class="w-50"
                                            style="border-top-left-radius: 1rem; border-bottom-left-radius: 1rem;">Nama
                                            Add-on
                                        </th>
                                        <th>HPP (Rp)</th>
                                        <th>Harga Jual (Rp)</th>
                                        <th class="text-center"
                                            style="border-top-right-radius: 1rem; border-bottom-right-radius: 1rem;">
                                            Aksi
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($extras as $index => $extra)
                                        <tr class="border-bottom">
                                            <td><input type="text" class="form-control"
                                                       wire:model="extras.{{ $index }}.name"></td>
                                            <td><input type="number" class="form-control"
                                                       wire:model="extras.{{ $index }}.cost" placeholder="0"></td>
                                            <td><input type="number" class="form-control"
                                                       wire:model="extras.{{ $index }}.price" placeholder="0"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        style="border-radius: 0.5rem;"
                                                        wire:click="removeExtra({{ $index }})"><i
                                                        class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold"
                                        style="border-radius: 0.75rem;" wire:click="addExtra">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Add-on
                                </button>
                            </div>

                            <div class="text-end mt-5 border-top pt-4">
                                <button type="button" class="btn btn-outline-secondary me-2" @click="tab = 'pricing'">
                                    Kembali
                                </button>
                                <button type="submit"
                                        class="btn btn-primary fw-bold d-inline-flex align-items-center gap-2"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="save"><i
                                            class="bi bi-cloud-arrow-up fs-5"></i> Simpan</span>
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
