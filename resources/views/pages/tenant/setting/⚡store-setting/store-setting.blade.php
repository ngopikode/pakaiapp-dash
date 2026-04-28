<div x-data="{ tab: 'basic' }">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end mb-4 gap-3">
        <div>
            <h2 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">Pengaturan Toko</h2>
            <p class="text-secondary mb-0 fw-medium">Kelola identitas, tampilan landing page, dan SEO tokomu.</p>
        </div>
        <div>
            <button wire:click="save"
                    class="btn btn-dark rounded-pill px-4 py-2 shadow-sm fw-bold d-flex align-items-center gap-2"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save"><i class="bi bi-save"></i> Simpan Perubahan</span>
                <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm"></span> Menyimpan...</span>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4  sticky-top" style="top: 2rem;">
                <div class="card-body p-3">
                    <div class="nav flex-column nav-pills gap-2" role="tablist" aria-orientation="vertical">
                        <button @click="tab = 'basic'"
                                :class="tab === 'basic' ? 'bg-primary text-white shadow-sm' : 'bg-transparent hover-'"
                                class="nav-link text-start rounded-3 fw-bold px-3 py-3 transition-all border-0">
                            <i class="bi bi-shop me-2"></i> Info Dasar
                        </button>
                        <button @click="tab = 'hero'"
                                :class="tab === 'hero' ? 'bg-primary text-white shadow-sm' : 'bg-transparent hover-'"
                                class="nav-link text-start rounded-3 fw-bold px-3 py-3 transition-all border-0">
                            <i class="bi bi-window-sidebar me-2"></i> Hero & Navbar
                        </button>
                        <button @click="tab = 'seo'"
                                :class="tab === 'seo' ? 'bg-primary text-white shadow-sm' : 'bg-transparent hover-'"
                                class="nav-link text-start rounded-3 fw-bold px-3 py-3 transition-all border-0">
                            <i class="bi bi-search me-2"></i> SEO & Meta
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 ">
                <div class="card-body p-4 p-xl-5">

                    <div x-show="tab === 'basic'" x-transition.opacity.duration.300ms style="display: none;">
                        <h4 class="fw-bold mb-4 border-bottom pb-2">Informasi Dasar Toko</h4>

                        <div class="row g-4 mb-4">
                            <div class="col-md-8">
                                <label class="form-label small text-muted fw-bold">Nama Toko <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control form-control-lg  rounded-3 @error('name') is-invalid @enderror"
                                       wire:model="name" placeholder="Misal: Roti Sedap">
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-bold">Warna Tema (Hex)</label>
                                <div class="input-group input-group-lg">
                                    <input type="color" class="form-control form-control-color  border-end-0"
                                           wire:model="theme_color" title="Pilih warna utama" style="max-width: 60px;">
                                    <input type="text" class="form-control " wire:model="theme_color">
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Logo Toko</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class=" border rounded-3 d-flex justify-content-center align-items-center overflow-hidden shadow-sm"
                                        style="width: 80px; height: 80px;">
                                        @if($new_logo)
                                            <img src="{{ $new_logo->temporaryUrl() }}"
                                                 class="w-100 h-100 object-fit-cover">
                                        @elseif($logo)
                                            <img src="/tenant_{{ tenant('id') }}/{{ $logo }}"
                                                 class="w-100 h-100 object-fit-cover">
                                        @else
                                            <i class="bi bi-image text-muted fs-3"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control" wire:model="new_logo" accept="image/*">
                                        <div wire:loading wire:target="new_logo" class="small text-primary mt-1">
                                            Mengunggah...
                                        </div>
                                        <small class="text-muted d-block mt-1">Format: JPG, PNG. Maks 2MB.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Status Toko</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="isActiveSwitch"
                                           wire:model="is_active" style="width: 3em; height: 1.5em;">
                                    <label
                                        class="form-check-label ms-2 mt-1 fw-bold {{ $is_active ? 'text-success' : 'text-danger' }}"
                                        for="isActiveSwitch">
                                        {{ $is_active ? 'Aktif (Buka)' : 'Nonaktif (Tutup)' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Nomor WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text "><i class="bi bi-whatsapp text-success"></i></span>
                                    <input type="text" class="form-control " wire:model="whatsapp_number"
                                           placeholder="081234567890">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Alamat Lengkap</label>
                                <textarea class="form-control " wire:model="address" rows="2"
                                          placeholder="Jl. Sudirman No. 123..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div x-show="tab === 'hero'" x-transition.opacity.duration.300ms style="display: none;">
                        <h4 class="fw-bold mb-4 border-bottom pb-2">Hero Section & Navbar</h4>

                        <div class=" bg-opacity-50 p-4 rounded-4 border border-light mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-menu-button-wide me-1"></i> Pengaturan
                                Navbar</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-bold">Brand Text</label>
                                    <input type="text" class="form-control " wire:model="navbar_brand_text"
                                           placeholder="Misal: Ez">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-bold">Title</label>
                                    <input type="text" class="form-control " wire:model="navbar_title"
                                           placeholder="Opsional">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-bold">Subtitle</label>
                                    <input type="text" class="form-control " wire:model="navbar_subtitle"
                                           placeholder="Menu Digital">
                                </div>
                            </div>
                        </div>

                        <div class=" bg-opacity-50 p-4 rounded-4 border border-light">
                            <h6 class="fw-bold mb-3"><i class="bi bi-image-alt me-1"></i> Teks Banner / Hero
                            </h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Headline (Judul Utama)</label>
                                    <input type="text" class="form-control " wire:model="hero_headline"
                                           placeholder="Enjoy & Dine">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Tagline (Sub-judul)</label>
                                    <input type="text" class="form-control " wire:model="hero_tagline"
                                           placeholder="Nikmati menu spesial kami.">
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-bold">Promo Text (Badge)</label>
                                    <input type="text" class="form-control " wire:model="hero_promo_text"
                                           placeholder="Promo">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-bold">Status Text</label>
                                    <input type="text" class="form-control " wire:model="hero_status_text"
                                           placeholder="Buka Sekarang">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-bold">Link Instagram</label>
                                    <input type="text" class="form-control " wire:model="hero_instagram_url"
                                           placeholder="https://instagram.com/...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="tab === 'seo'" x-transition.opacity.duration.300ms style="display: none;">
                        <h4 class="fw-bold mb-4 border-bottom pb-2">Optimasi SEO & Meta</h4>

                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <label class="form-label small text-muted fw-bold">SEO Title</label>
                                <input type="text" class="form-control " wire:model="seo_title"
                                       placeholder="Roti Sedap - Roti Bakar Terbaik di Jakarta">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small text-muted fw-bold">SEO Description</label>
                                <textarea class="form-control " wire:model="seo_description" rows="2"
                                          placeholder="Deskripsi singkat toko untuk pencarian Google..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small text-muted fw-bold">SEO Keywords (Pisahkan dengan
                                    koma)</label>
                                <input type="text" class="form-control " wire:model="seo_keywords"
                                       placeholder="roti bakar, cafe jakarta, tempat nongkrong">
                            </div>
                        </div>

                        <hr class="border-secondary border-opacity-25 my-4">

                        <h6 class="fw-bold mb-3">Open Graph (Tampilan saat link di-share ke WA/Sosmed)</h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label small text-muted fw-bold">OG Title</label>
                                    <input type="text" class="form-control " wire:model="og_title"
                                           placeholder="Cek Menu Roti Sedap Sekarang!">
                                </div>
                                <div>
                                    <label class="form-label small text-muted fw-bold">OG Description</label>
                                    <textarea class="form-control " wire:model="og_description" rows="3"
                                              placeholder="Pesan langsung tanpa antre melalui menu digital kami."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">OG Image (Thumbnail Link)</label>
                                <div class="card  border-0">
                                    <div class="card-body p-3">
                                        <div
                                            class=" border rounded-3 d-flex justify-content-center align-items-center overflow-hidden mb-2 shadow-sm"
                                            style="width: 100%; height: 140px;">
                                            @if($new_og_image)
                                                <img src="{{ $new_og_image->temporaryUrl() }}"
                                                     class="w-100 h-100 object-fit-cover">
                                            @elseif($og_image)
                                                <img src="/tenant_{{ tenant('id') }}/{{ $og_image }}"
                                                     class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="text-center text-muted">
                                                    <i class="bi bi-image fs-1 d-block mb-1"></i>
                                                    <small>1200 x 630px disarankan</small>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" class="form-control form-control-sm"
                                               wire:model="new_og_image" accept="image/*">
                                        <div wire:loading wire:target="new_og_image" class="small text-primary mt-1">
                                            Mengunggah...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
