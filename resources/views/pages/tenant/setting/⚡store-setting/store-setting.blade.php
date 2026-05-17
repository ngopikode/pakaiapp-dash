<div x-data="{ tab: 'basic' }">

    <!-- Custom CSS untuk hide scrollbar di mobile tab & percantik UI -->
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .setting-switch {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .setting-switch:last-child {
            border-bottom: none;
        }

        .upload-zone {
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }

        .upload-zone:hover {
            border-color: #b45309;
            background-color: #faf8f5;
        }
    </style>

    <!-- Header & Action Button -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bolder mb-1" style="color: #451a03; letter-spacing: -0.5px;">Pengaturan Toko</h2>
            <p class="text-secondary small mb-0 fw-medium">Kelola identitas, tampilan beranda, dan SEO tokomu.</p>
        </div>
        <div class="d-grid d-md-block">
            <button wire:click="save"
                    class="btn rounded-pill px-4 py-2 shadow-sm fw-bold d-flex align-items-center justify-content-center gap-2"
                    style="background: linear-gradient(135deg, #ca8a04, #b45309); color: white; border: none;"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save"><i
                        class="bi bi-check2-circle fs-5"></i> Simpan Perubahan</span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm"></span> Menyimpan...
                </span>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation (Responsive: Swipeable on Mobile, Vertical on Desktop) -->
        <div class="col-lg-3">
            <div class="position-sticky" style="top: 2rem;">
                <div
                    class="nav flex-row flex-lg-column nav-pills gap-2 flex-nowrap overflow-x-auto hide-scrollbar pb-2 pb-lg-0"
                    role="tablist">
                    <button @click="tab = 'basic'"
                            :class="tab === 'basic' ? 'shadow-sm' : 'bg-transparent text-secondary'"
                            class="nav-link text-start rounded-4 fw-bold px-4 py-3 transition-all border-0 flex-shrink-0"
                            :style="tab === 'basic' ? 'background-color: #451a03; color: white;' : ''">
                        <i class="bi bi-shop me-2"></i> Info Dasar
                    </button>
                    <button @click="tab = 'hero'"
                            :class="tab === 'hero' ? 'shadow-sm' : 'bg-transparent text-secondary'"
                            class="nav-link text-start rounded-4 fw-bold px-4 py-3 transition-all border-0 flex-shrink-0"
                            :style="tab === 'hero' ? 'background-color: #451a03; color: white;' : ''">
                        <i class="bi bi-window-sidebar me-2"></i> Hero & Navbar
                    </button>
                    <button @click="tab = 'seo'"
                            :class="tab === 'seo' ? 'shadow-sm' : 'bg-transparent text-secondary'"
                            class="nav-link text-start rounded-4 fw-bold px-4 py-3 transition-all border-0 flex-shrink-0"
                            :style="tab === 'seo' ? 'background-color: #451a03; color: white;' : ''">
                        <i class="bi bi-search me-2"></i> SEO & Meta
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4" style="background: rgba(255, 255, 255, 0.95);">
                <div class="card-body p-4 p-xl-5">

                    <!-- TAB 1: INFO DASAR -->
                    <div x-show="tab === 'basic'" x-transition.opacity.duration.300ms style="display: none;">
                        <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-info-square text-warning me-2"></i>Informasi
                            Dasar</h5>

                        <div class="row g-4 mb-4">
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text"
                                           class="form-control rounded-3 bg-light border-0 @error('name') is-invalid @enderror"
                                           id="storeName" wire:model="name" placeholder="Nama Toko">
                                    <label for="storeName" class="text-muted fw-medium">Nama Toko <span
                                            class="text-danger">*</span></label>
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-bold mb-1">Warna Tema Utama</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <input type="color" class="form-control form-control-color border-0 p-1"
                                           wire:model="theme_color" style="max-width: 60px; height: 58px;">
                                    <input type="text" class="form-control border-0 bg-light fw-bold"
                                           wire:model="theme_color" placeholder="#HEX">
                                </div>
                            </div>
                        </div>

                        <!-- Upload Logo -->
                        <div class="upload-zone rounded-4 p-4 mb-4 text-center">
                            <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-4">
                                <div
                                    class="rounded-circle overflow-hidden shadow-sm border bg-white d-flex align-items-center justify-content-center"
                                    style="width: 90px; height: 90px;">
                                    @if($new_logo)
                                        <img src="{{ $new_logo->temporaryUrl() }}" class="w-100 h-100 object-fit-cover">
                                    @elseif($logo)
                                        <img src="/tenant_{{ tenant('id') }}/{{ $logo }}"
                                             class="w-100 h-100 object-fit-cover">
                                    @else
                                        <i class="bi bi-image text-muted fs-2"></i>
                                    @endif
                                </div>
                                <div class="text-md-start text-center">
                                    <h6 class="fw-bold mb-1">Logo Toko</h6>
                                    <p class="small text-muted mb-2">Format JPG/PNG. Maksimal 2MB. Resolusi 1:1
                                        direkomendasikan.</p>
                                    <input type="file" class="form-control form-control-sm rounded-3"
                                           wire:model="new_logo" accept="image/*" style="max-width: 250px;">
                                    <div wire:loading wire:target="new_logo" class="small text-warning mt-1 fw-bold"><i
                                            class="bi bi-arrow-repeat spin"></i> Mengunggah...
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 bg-light border-0" id="waNumber"
                                           wire:model="whatsapp_number" placeholder="Nomor WhatsApp">
                                    <label for="waNumber" class="text-muted fw-medium"><i
                                            class="bi bi-whatsapp text-success me-1"></i> Nomor WhatsApp</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control rounded-3 bg-light border-0" id="address"
                                              wire:model="address" style="height: 58px;"
                                              placeholder="Alamat Lengkap"></textarea>
                                    <label for="address" class="text-muted fw-medium"><i
                                            class="bi bi-geo-alt text-danger me-1"></i> Alamat Lengkap</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Store Settings (iOS Style) -->
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-4 h-100">
                                    <h6 class="fw-bold small text-muted mb-3 text-uppercase">Tipe & Status</h6>
                                    <div class="form-floating mb-3">
                                        <select class="form-select rounded-3 border-0 shadow-sm" id="storeType"
                                                wire:model="store_type" disabled>
                                            <option value="resto">Restoran / Cafe</option>
                                            <option value="retail">Toko Retail</option>
                                            <option value="service">Jasa</option>
                                        </select>
                                        <label for="storeType" class="fw-bold">Kategori Bisnis</label>
                                    </div>
                                    <div class="setting-switch pt-2">
                                        <div>
                                            <div class="fw-bold text-dark">Status Toko</div>
                                            <div class="small text-muted">Buka/tutup pesanan online</div>
                                        </div>
                                        <div class="form-check form-switch fs-4 mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                   wire:model="is_active">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Methods -->
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-4 h-100">
                                    <h6 class="fw-bold small text-muted mb-3 text-uppercase">Metode Pesanan</h6>

                                    @if($store_type === 'resto')
                                        <div class="setting-switch">
                                            <span class="fw-bold text-dark"><i
                                                    class="bi bi-cup-hot me-2 text-warning"></i>Dine-in (Makan Sini)</span>
                                            <div class="form-check form-switch fs-5 mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                       wire:model="is_dinein_active">
                                            </div>
                                        </div>
                                    @endif

                                    <div class="setting-switch">
                                        <span class="fw-bold text-dark"><i class="bi bi-bag me-2 text-primary"></i>Takeaway (Bungkus)</span>
                                        <div class="form-check form-switch fs-5 mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                   wire:model="is_takeaway_active">
                                        </div>
                                    </div>

                                    <div class="setting-switch">
                                        <span class="fw-bold text-dark"><i
                                                class="bi bi-motorcycle me-2 text-success"></i>Delivery (Diantar)</span>
                                        <div class="form-check form-switch fs-5 mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                   wire:model="is_delivery_active">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: HERO & NAVBAR -->
                    <div x-show="tab === 'hero'" x-transition.opacity.duration.300ms style="display: none;">
                        <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-window text-primary me-2"></i>Hero & Navbar
                        </h5>

                        <div class="bg-light p-4 rounded-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-menu-up me-2 text-secondary"></i>Pengaturan Navbar
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3 border-0" id="navBrand"
                                               wire:model="navbar_brand_text" placeholder="Brand Text">
                                        <label for="navBrand" class="text-muted fw-medium">Brand Text</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3 border-0" id="navTitle"
                                               wire:model="navbar_title" placeholder="Title">
                                        <label for="navTitle" class="text-muted fw-medium">Title (Opsional)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3 border-0" id="navSubtitle"
                                               wire:model="navbar_subtitle" placeholder="Subtitle">
                                        <label for="navSubtitle" class="text-muted fw-medium">Subtitle</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light p-4 rounded-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-image-alt me-2 text-secondary"></i>Teks Banner /
                                Hero</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3 border-0 fw-bold"
                                               id="heroHeadline" wire:model="hero_headline" placeholder="Headline">
                                        <label for="heroHeadline" class="text-muted fw-medium">Headline (Judul
                                            Utama)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3 border-0" id="heroTagline"
                                               wire:model="hero_tagline" placeholder="Tagline">
                                        <label for="heroTagline" class="text-muted fw-medium">Tagline
                                            (Sub-judul)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3 border-0" id="promoText"
                                               wire:model="hero_promo_text" placeholder="Promo">
                                        <label for="promoText" class="text-muted fw-medium">Promo Text (Badge)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3 border-0" id="statusText"
                                               wire:model="hero_status_text" placeholder="Status">
                                        <label for="statusText" class="text-muted fw-medium">Status Text</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3 border-0" id="igLink"
                                               wire:model="hero_instagram_url" placeholder="Instagram">
                                        <label for="igLink" class="text-muted fw-medium"><i
                                                class="bi bi-instagram text-danger me-1"></i> Link Instagram</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: SEO & META -->
                    <div x-show="tab === 'seo'" x-transition.opacity.duration.300ms style="display: none;">
                        <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-google text-success me-2"></i>Optimasi SEO &
                            Meta</h5>

                        <div class="row g-4 mb-5">
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 bg-light border-0" id="seoTitle"
                                           wire:model="seo_title" placeholder="SEO Title">
                                    <label for="seoTitle" class="text-muted fw-medium">SEO Title</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea class="form-control rounded-3 bg-light border-0" id="seoDesc"
                                              wire:model="seo_description" style="height: 80px;"
                                              placeholder="SEO Desc"></textarea>
                                    <label for="seoDesc" class="text-muted fw-medium">SEO Description (Meta)</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 bg-light border-0" id="seoKeywords"
                                           wire:model="seo_keywords" placeholder="Keywords">
                                    <label for="seoKeywords" class="text-muted fw-medium">SEO Keywords (Pisahkan dengan
                                        koma)</label>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-4"
                             style="background: rgba(202, 138, 4, 0.05); border: 1px solid rgba(202, 138, 4, 0.2);">
                            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-share me-2"></i>Open Graph (Tampilan
                                Share Sosmed/WA)</h6>
                            <div class="row g-4 align-items-center">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control rounded-3 bg-white border-0 shadow-sm"
                                               id="ogTitle" wire:model="og_title" placeholder="OG Title">
                                        <label for="ogTitle" class="text-muted fw-medium">OG Title</label>
                                    </div>
                                    <div class="form-floating">
                                        <textarea class="form-control rounded-3 bg-white border-0 shadow-sm" id="ogDesc"
                                                  wire:model="og_description" style="height: 100px;"
                                                  placeholder="OG Desc"></textarea>
                                        <label for="ogDesc" class="text-muted fw-medium">OG Description</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="upload-zone rounded-4 p-3 bg-white text-center">
                                        <div
                                            class="rounded-3 overflow-hidden d-flex justify-content-center align-items-center bg-light mx-auto mb-3"
                                            style="width: 100%; max-width: 280px; height: 140px;">
                                            @if($new_og_image)
                                                <img src="{{ $new_og_image->temporaryUrl() }}"
                                                     class="w-100 h-100 object-fit-cover">
                                            @elseif($og_image)
                                                <img src="/tenant_{{ tenant('id') }}/{{ $og_image }}"
                                                     class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="text-center text-muted">
                                                    <i class="bi bi-image fs-1 d-block mb-1"></i>
                                                    <small class="fw-bold">OG Image</small><br>
                                                    <small>1200 x 630px</small>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" class="form-control form-control-sm mx-auto"
                                               wire:model="new_og_image" accept="image/*" style="max-width: 250px;">
                                        <div wire:loading wire:target="new_og_image"
                                             class="small text-warning mt-1 fw-bold"><i
                                                class="bi bi-arrow-repeat spin"></i> Mengunggah...
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
