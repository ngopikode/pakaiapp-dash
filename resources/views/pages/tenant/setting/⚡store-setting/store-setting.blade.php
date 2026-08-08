<div class="relative w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 sm:mt-10 font-sans pb-28">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10 pb-6 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Pengaturan Toko</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola identitas, jam operasional, tampilan, dan SEO tokomu.</p>
        </div>
    </div>

    <div class="space-y-12 sm:space-y-16">

        {{-- ═══════════════════════════════════════════════════════════
             1. INFORMASI DASAR
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <i class="ph ph-storefront text-xl"></i>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Info Dasar</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Informasi utama toko yang ditampilkan ke pelanggan, beserta logo dan kontak.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] p-5 sm:p-6 space-y-6">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Toko <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="name" placeholder="Nama Toko" 
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 dark:focus:ring-orange-400 transition-shadow">
                            @error('name') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Warna Tema</label>
                            <div class="flex items-center h-[46px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-orange-500 transition-shadow">
                                <input type="color" wire:model="theme_color" class="w-12 h-full p-1 cursor-pointer bg-transparent border-0 shrink-0">
                                <input type="text" wire:model="theme_color" class="w-full h-full bg-transparent border-0 px-2 text-sm text-slate-900 dark:text-white font-mono uppercase focus:ring-0">
                            </div>
                            @error('theme_color') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Logo Toko</label>
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                            <label class="relative w-24 h-24 shrink-0 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden cursor-pointer group hover:border-orange-500 transition-colors">
                                @if($new_logo)
                                    <img src="{{ $new_logo->temporaryUrl() }}" class="w-full h-full object-cover" alt="New Logo">
                                @elseif($logo)
                                    <img src="/tenant_{{ tenant('id') }}/{{ $logo }}" class="w-full h-full object-cover" alt="Current Logo">
                                @else
                                    <i class="ph ph-image text-3xl text-slate-400"></i>
                                @endif
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="ph-fill ph-camera text-white text-2xl"></i>
                                </div>
                                <input type="file" class="hidden" wire:model.live="new_logo" accept="image/*">
                            </label>
                            <div class="text-center sm:text-left flex-1">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Format JPG/PNG. Maksimal 2MB. Resolusi 1:1 direkomendasikan.</p>
                                <label class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    Pilih Gambar Baru
                                    <input type="file" class="hidden" wire:model.live="new_logo" accept="image/*">
                                </label>
                                <div wire:loading wire:target="new_logo" class="block mt-2 text-xs font-semibold text-orange-500">
                                    <i class="ph ph-spinner animate-spin"></i> Mengunggah...
                                </div>
                                @error('new_logo') <span class="text-xs text-red-500 mt-2 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                     {{-- Upload QRIS Statis --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Gambar QRIS Statis Toko (Opsional)
                        </label>
                        <div class="flex flex-col sm:flex-row items-start gap-4">
                            <label class="relative block w-full sm:w-48 aspect-square rounded-xl bg-slate-50 dark:bg-slate-800 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-orange-500 dark:hover:border-orange-500 overflow-hidden cursor-pointer group transition-colors shrink-0">
                                @if($new_qris_image)
                                    <img src="{{ $new_qris_image->temporaryUrl() }}" class="w-full h-full object-contain p-2" alt="QRIS baru">
                                @elseif($qris_image)
                                    <img src="/tenant_{{ tenant('id') }}/{{ $qris_image }}" class="w-full h-full object-contain p-2" alt="QRIS toko">
                                @else
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                                        <i class="ph ph-qr-code text-4xl mb-2"></i>
                                        <span class="text-xs font-semibold">Klik untuk upload QRIS</span>
                                        <span class="text-[10px] mt-1">DANA / BCA / OVO / GoPay</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="ph-fill ph-camera text-white text-2xl"></i>
                                </div>
                                <input type="file" class="hidden" wire:model.live="new_qris_image" accept="image/*">
                            </label>
                            <div class="flex-1 space-y-3">
                                <p class="text-xs text-slate-500 dark:text-slate-400">Gambar ini ditampilkan ke pelanggan saat memilih metode QRIS atau saat Checkout WhatsApp aktif.</p>
                                <div wire:loading wire:target="new_qris_image" class="text-xs font-semibold text-orange-500">
                                    <i class="ph ph-spinner animate-spin"></i> Mengunggah...
                                </div>
                                @error('new_qris_image') <span class="text-xs text-red-500 block font-medium">{{ $message }}</span> @enderror
                                @if($qris_image && !$new_qris_image)
                                    <button type="button" wire:click="deleteQrisImage"
                                            wire:confirm="Yakin hapus gambar QRIS? Gambar akan dihapus permanen."
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                                        <i class="ph ph-trash"></i> Hapus Gambar QRIS
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nomor WhatsApp</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ph ph-whatsapp-logo text-slate-400"></i>
                                </div>
                                <input type="text" wire:model="whatsapp_number" placeholder="Contoh: 62812..." 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Alamat Lengkap</label>
                            <div class="relative">
                                <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                    <i class="ph ph-map-pin text-slate-400"></i>
                                </div>
                                <textarea wire:model="address" placeholder="Jalan..." rows="2"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="block text-sm font-semibold text-slate-900 dark:text-white">Status Toko Online</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">Buka/tutup pesanan secara keseluruhan</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                            </label>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700/50 flex items-center justify-between"
                             x-show="!$wire.is_wa_checkout_active">
                            <div>
                                <span class="block text-sm font-semibold text-slate-900 dark:text-white">Sesi Shift Kasir</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">Wajibkan kasir buka shift dan opname saat tutup laci</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="$wire.is_shift_active" wire:model="is_shift_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                            </label>
                        </div>

                        @if($store_type === 'resto')
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700/50 flex items-center justify-between"
                             x-show="!$wire.is_wa_checkout_active">
                            <div>
                                <span class="block text-sm font-semibold text-slate-900 dark:text-white">Layar Dapur (KDS)</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">Aktifkan kitchen display system</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_kitchen_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                            </label>
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="!$wire.is_wa_checkout_active" x-cloak>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700/50">
                            <h6 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Metode Pesanan</h6>
                            <div class="space-y-4">
                                @if($store_type === 'resto')
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        <i class="ph ph-coffee text-orange-500 text-lg"></i> Dine-in
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="is_dinein_active" class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                                    </label>
                                </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        <i class="ph ph-bag text-blue-500 text-lg"></i> Takeaway
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="is_takeaway_active" class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        <i class="ph ph-moped text-emerald-500 text-lg"></i> Delivery
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="is_delivery_active" class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        @if($store_type === 'resto')
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700/50">
                            <h6 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Pajak & Layanan</h6>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Pajak PB1</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="is_tax_active" class="sr-only peer">
                                            <div class="w-9 h-5 bg-slate-200 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                                        </label>
                                    </div>
                                    <div x-show="$wire.is_tax_active" class="relative">
                                        <input type="number" step="0.01" wire:model="tax_rate" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-orange-500">
                                        <span class="absolute right-3 top-2 text-sm text-slate-400">%</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Service Charge Restoran</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="is_service_charge_active" class="sr-only peer">
                                            <div class="w-9 h-5 bg-slate-200 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                                        </label>
                                    </div>
                                    <div x-show="$wire.is_service_charge_active" class="relative">
                                        <input type="number" step="0.01" wire:model="service_charge_rate" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-orange-500">
                                        <span class="absolute right-3 top-2 text-sm text-slate-400">%</span>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-slate-200 dark:border-slate-700/50">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Biaya Aplikasi</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="is_application_fee_passed" class="sr-only peer">
                                            <div class="w-9 h-5 bg-slate-200 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                                        </label>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Bebankan biaya transaksi PakaiApp ke pelanggan.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </section>

        <hr class="border-slate-200 dark:border-slate-800">

        {{-- ═══════════════════════════════════════════════════════════
             2. JAM OPERASIONAL
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <i class="ph ph-clock text-xl"></i>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Jam Operasional</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Atur jadwal buka dan tutup toko. Pelanggan tidak bisa membuat pesanan di luar jam ini.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] p-5 sm:p-6 space-y-6">
                    
                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700/50">
                        <div>
                            <span class="block text-sm font-semibold text-slate-900 dark:text-white">Jadwal sama setiap hari</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400">Gunakan jam buka & tutup yang sama untuk Senin - Minggu</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="use_same_hours" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-orange-500"></div>
                        </label>
                    </div>

                    <!-- Mode: Same Hours -->
                    <div x-show="$wire.use_same_hours" class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 p-4 sm:p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl transition-opacity"
                             x-bind:class="{ 'bg-slate-50 dark:bg-slate-800/50 border-dashed': $wire.operating_hours.default.is_closed }">
                            <div class="flex items-center justify-between sm:w-28 shrink-0">
                                <span class="text-sm font-bold" x-bind:class="$wire.operating_hours.default.is_closed ? 'text-slate-400 dark:text-slate-500' : 'text-slate-700 dark:text-slate-300'">Semua Hari</span>
                                <div class="sm:hidden flex items-center gap-2">
                                    <span class="text-xs font-semibold text-slate-500" x-show="$wire.operating_hours.default.is_closed">Tutup</span>
                                    <label class="relative inline-flex items-center cursor-pointer" title="Tandai Tutup">
                                        <input type="checkbox" x-model="$wire.operating_hours.default.is_closed" class="sr-only peer">
                                        <div class="w-9 h-5 bg-orange-500 rounded-full peer dark:bg-orange-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-slate-200 dark:peer-checked:bg-slate-700"></div>
                                    </label>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center gap-2">
                                <input type="time" wire:model="operating_hours.default.open" 
                                       x-bind:disabled="$wire.operating_hours.default.is_closed"
                                       x-bind:class="{ 'opacity-50 cursor-not-allowed': $wire.operating_hours.default.is_closed }"
                                       class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500">
                                <span class="text-slate-400 text-sm">-</span>
                                <input type="time" wire:model="operating_hours.default.close" 
                                       x-bind:disabled="$wire.operating_hours.default.is_closed"
                                       x-bind:class="{ 'opacity-50 cursor-not-allowed': $wire.operating_hours.default.is_closed }"
                                       class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div class="hidden sm:flex shrink-0 items-center gap-2 min-w-[70px] justify-end">
                                <span class="text-xs font-semibold text-slate-500" x-show="$wire.operating_hours.default.is_closed">Tutup</span>
                                <label class="relative inline-flex items-center cursor-pointer" title="Tandai Tutup">
                                    <input type="checkbox" x-model="$wire.operating_hours.default.is_closed" class="sr-only peer">
                                    <div class="w-9 h-5 bg-orange-500 rounded-full peer dark:bg-orange-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-slate-200 dark:peer-checked:bg-slate-700"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Mode: Different Hours -->
                    <div x-show="!$wire.use_same_hours" class="space-y-3">
                        @php
                            $dayLabels = [
                                'monday'    => 'Senin',
                                'tuesday'   => 'Selasa',
                                'wednesday' => 'Rabu',
                                'thursday'  => 'Kamis',
                                'friday'    => 'Jumat',
                                'saturday'  => 'Sabtu',
                                'sunday'    => 'Minggu',
                            ];
                        @endphp
                        
                        @foreach($dayLabels as $key => $label)
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 p-4 sm:p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl transition-opacity"
                             x-bind:class="{ 'bg-slate-50 dark:bg-slate-800/50 border-dashed': $wire.operating_hours.{{ $key }}.is_closed }">
                            <div class="flex items-center justify-between sm:w-28 shrink-0">
                                <span class="text-sm font-bold" x-bind:class="$wire.operating_hours.{{ $key }}.is_closed ? 'text-slate-400 dark:text-slate-500' : 'text-slate-700 dark:text-slate-300'">{{ $label }}</span>
                                <div class="sm:hidden flex items-center gap-2">
                                    <span class="text-xs font-semibold text-slate-500" x-show="$wire.operating_hours.{{ $key }}.is_closed">Libur</span>
                                    <label class="relative inline-flex items-center cursor-pointer" title="Tandai Libur">
                                        <input type="checkbox" x-model="$wire.operating_hours.{{ $key }}.is_closed" class="sr-only peer">
                                        <div class="w-9 h-5 bg-orange-500 rounded-full peer dark:bg-orange-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-slate-200 dark:peer-checked:bg-slate-700"></div>
                                    </label>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center gap-2">
                                <input type="time" wire:model="operating_hours.{{ $key }}.open" 
                                       x-bind:disabled="$wire.operating_hours.{{ $key }}.is_closed"
                                       x-bind:class="{ 'opacity-50 cursor-not-allowed': $wire.operating_hours.{{ $key }}.is_closed }"
                                       class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-2 sm:px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500">
                                <span class="text-slate-400 text-sm">-</span>
                                <input type="time" wire:model="operating_hours.{{ $key }}.close" 
                                       x-bind:disabled="$wire.operating_hours.{{ $key }}.is_closed"
                                       x-bind:class="{ 'opacity-50 cursor-not-allowed': $wire.operating_hours.{{ $key }}.is_closed }"
                                       class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-2 sm:px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div class="hidden sm:flex shrink-0 items-center gap-2 min-w-[70px] justify-end">
                                <span class="text-xs font-semibold text-slate-500" x-show="$wire.operating_hours.{{ $key }}.is_closed">Libur</span>
                                <label class="relative inline-flex items-center cursor-pointer" title="Tandai Libur">
                                    <input type="checkbox" x-model="$wire.operating_hours.{{ $key }}.is_closed" class="sr-only peer">
                                    <div class="w-9 h-5 bg-orange-500 rounded-full peer dark:bg-orange-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-slate-200 dark:peer-checked:bg-slate-700"></div>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </section>

        <hr class="border-slate-200 dark:border-slate-800">

        {{-- ═══════════════════════════════════════════════════════════
             3. HERO & NAVBAR
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <i class="ph ph-browser text-xl"></i>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Tampilan Beranda</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Ubah teks sambutan, promo, dan tautan sosial media di halaman utama toko.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] p-5 sm:p-6 space-y-8">
                    
                    <div>
                        <h6 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 flex items-center gap-2">                <i class="ph ph-list"></i> Navbar Atas</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Brand Text</label>
                                <input type="text" wire:model="navbar_brand_text" placeholder="Singkatan / Brand" 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Title</label>
                                <input type="text" wire:model="navbar_title" placeholder="Judul Navbar" 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Subtitle</label>
                                <input type="text" wire:model="navbar_subtitle" placeholder="Sub-judul" 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-200 dark:border-slate-800">
                        <h6 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 flex items-center gap-2"><i class="ph ph-image"></i> Banner Utama</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Headline</label>
                                <input type="text" wire:model="hero_headline" placeholder="Judul Utama Besar" 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 font-bold focus:ring-2 focus:ring-orange-500 transition-shadow">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tagline</label>
                                <input type="text" wire:model="hero_tagline" placeholder="Deskripsi Singkat" 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Teks Promo</label>
                                <input type="text" wire:model="hero_promo_text" placeholder="Badge Promo" 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Teks Status</label>
                                <input type="text" wire:model="hero_status_text" placeholder="Cth: Buka Sekarang" 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Link Instagram</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="ph ph-instagram-logo text-slate-400"></i>
                                    </div>
                                    <input type="text" wire:model="hero_instagram_url" placeholder="https://ig..." 
                                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <hr class="border-slate-200 dark:border-slate-800">

        {{-- ═══════════════════════════════════════════════════════════
             4. SEO & META
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <i class="ph ph-magnifying-glass text-xl"></i>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">SEO & Meta Share</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Optimalkan pencarian di Google dan percantik tampilan link saat dibagikan ke WhatsApp / Sosmed.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] p-5 sm:p-6 space-y-8">
                    
                    <div class="space-y-5">
                        <h6 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2 flex items-center gap-2"><i class="ph ph-google-logo"></i> Search Engine</h6>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">SEO Title</label>
                            <input type="text" wire:model="seo_title" placeholder="Judul Halaman di Google" 
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">SEO Description</label>
                            <textarea wire:model="seo_description" placeholder="Deskripsi yang muncul di bawah judul Google..." rows="3"
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">SEO Keywords</label>
                            <input type="text" wire:model="seo_keywords" placeholder="Contoh: cafe murah, kopi enak, tempat nongkrong" 
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                            <p class="text-xs text-slate-500 mt-1.5">Pisahkan dengan koma.</p>
                        </div>
                    </div>

                    <div class="p-5 bg-orange-50 dark:bg-orange-500/5 border border-orange-200 dark:border-orange-500/20 rounded-2xl">
                        <h6 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2"><i class="ph ph-share-network"></i> Open Graph (WhatsApp / Sosmed)</h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">OG Title</label>
                                    <input type="text" wire:model="og_title" placeholder="Judul saat dishare" 
                                           class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">OG Description</label>
                                    <textarea wire:model="og_description" placeholder="Deskripsi singkat..." rows="4"
                                           class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow resize-none"></textarea>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">OG Image</label>
                                <label class="relative block w-full aspect-video rounded-xl bg-white dark:bg-slate-800 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-orange-500 dark:hover:border-orange-500 overflow-hidden cursor-pointer group transition-colors">
                                    @if($new_og_image)
                                        <img src="{{ $new_og_image->temporaryUrl() }}" class="w-full h-full object-cover" alt="New OG">
                                    @elseif($og_image)
                                        <img src="/tenant_{{ tenant('id') }}/{{ $og_image }}" class="w-full h-full object-cover" alt="Current OG">
                                    @else
                                        <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                                            <i class="ph ph-image text-3xl mb-2"></i>
                                            <span class="text-xs font-semibold">Klik untuk upload</span>
                                            <span class="text-[10px] mt-1">Rekomendasi: 1200x630px</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="ph-fill ph-camera text-white text-2xl"></i>
                                    </div>
                                    <input type="file" class="hidden" wire:model.live="new_og_image" accept="image/*">
                                </label>
                                <div wire:loading wire:target="new_og_image" class="block mt-2 text-xs font-semibold text-orange-500 text-center">
                                    <i class="ph ph-spinner animate-spin"></i> Mengunggah...
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <hr class="border-slate-200 dark:border-slate-800">

        {{-- ═══════════════════════════════════════════════════════════
             5. PRE-ORDER & PENGIRIMAN TERJADWAL
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <i class="ph ph-whatsapp-logo text-xl"></i>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Checkout WhatsApp</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Metode checkout katalog langsung ke WhatsApp (tanpa payment gateway). Cocok untuk UMKM retail. Fitur POS Kasir akan otomatis dinonaktifkan.</p>
            </div>

            <div class="md:col-span-8 lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] p-5 sm:p-6 space-y-6">

                    {{-- Toggle Mode WA --}}
                    <div class="flex items-center justify-between gap-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/50">
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Aktifkan Checkout WhatsApp</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pelanggan mengirim daftar pesanan via pesan WA ke nomor admin.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" class="sr-only peer"
                                   wire:model="is_wa_checkout_active">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-500 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>

                </div>
            </div>
        </section>

        <hr class="border-slate-200 dark:border-slate-800">

        {{-- ═══════════════════════════════════════════════════════════
             6. PENGIRIMAN TERJADWAL (PRE-ORDER)
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8" x-show="$wire.is_wa_checkout_active" x-cloak>
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <i class="ph ph-package text-xl"></i>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Pengiriman Terjadwal</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Fitur Pre-Order: mewajibkan pelanggan memilih tanggal pengiriman dan slot waktu di keranjang belanja. Menyediakan juga agregasi rekap pasar harian.</p>
            </div>

            <div class="md:col-span-8 lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] p-5 sm:p-6 space-y-6">

                    {{-- Toggle Mode PreOrder --}}
                    <div class="flex items-center justify-between gap-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/50">
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Aktifkan Pre-Order</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Keranjang akan menambah tahap pemilihan Jadwal Kirim & Zona Ongkir.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" class="sr-only peer"
                                   wire:model="is_preorder_active">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-500 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>

                    {{-- Fields hanya tampil saat mode preorder aktif --}}
                    <div x-show="$wire.is_preorder_active" x-cloak class="space-y-5">

                        {{-- Jam Cut-Off --}}
                        <div x-data="{
                                rawTime: $wire.entangle('cutoff_time'),
                                formatTime(e) {
                                    let val = e.target.value.replace(/[^0-9]/g, '');
                                    if (val.length >= 3) {
                                        val = val.substring(0,2) + ':' + val.substring(2,4);
                                    }
                                    if (val.length > 5) val = val.substring(0,5);
                                    
                                    // Validasi jam 00-23 dan menit 00-59
                                    if (val.length === 5) {
                                        let [h, m] = val.split(':');
                                        if (parseInt(h) > 23) h = '23';
                                        if (parseInt(m) > 59) m = '59';
                                        val = h + ':' + m;
                                    }
                                    
                                    this.rawTime = val;
                                }
                             }">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Jam Batas Pesan (Cut-Off)
                            </label>
                            <div class="relative w-full sm:w-48">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ph ph-clock text-slate-400"></i>
                                </div>
                                <input type="text"
                                       x-model="rawTime"
                                       @input="formatTime"
                                       placeholder="HH:MM (misal: 04:00)"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-orange-500 transition-shadow font-mono">
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                                Atur jam batas (misal: 04:00).<br>
                                <span class="font-medium text-slate-700 dark:text-slate-300">Pesan sebelum jam ini:</span> pembeli bisa minta kirim di hari yang sama.<br>
                                <span class="font-medium text-slate-700 dark:text-slate-300">Pesan lewat jam ini:</span> pengiriman paling cepat baru bisa besok hari.<br>
                                <i>Kosongkan jika sistem pre-order Anda selalu kirim H+1.</i>
                            </p>
                            @error('cutoff_time') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        {{-- Tombol Buka Halaman Pengaturan --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ route('delivery-zones.index') }}" wire:navigate
                               class="p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-orange-500 transition-colors flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-600">
                                        <i class="ph-bold ph-map-pin text-xl"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">Atur Zona Ongkir</p>
                                        <p class="text-xs text-slate-500">{{ $total_active_zones }} zona aktif</p>
                                    </div>
                                </div>
                                <i class="ph-bold ph-caret-right text-slate-400 group-hover:text-orange-500 transition-colors"></i>
                            </a>
                            
                            <a href="{{ route('delivery-slots.index') }}" wire:navigate
                               class="p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-orange-500 transition-colors flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-600">
                                        <i class="ph-bold ph-clock text-xl"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">Atur Slot Waktu</p>
                                        <p class="text-xs text-slate-500">{{ $total_active_slots }} slot aktif</p>
                                    </div>
                                </div>
                                <i class="ph-bold ph-caret-right text-slate-400 group-hover:text-orange-500 transition-colors"></i>
                            </a>
                        </div>

                        {{-- Link ke halaman pre-order dashboard --}}
                        <div class="p-4 bg-orange-50 dark:bg-orange-500/5 border border-orange-200 dark:border-orange-500/20 rounded-2xl flex items-center gap-3 mt-4">
                            <i class="ph ph-arrow-square-out text-orange-500 text-xl shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">Kelola Pesanan Terjadwal</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Lihat daftar pesanan & rekap belanja pasar di dashboard khusus.</p>
                            </div>
                            <a href="{{ route('pre-order.index') }}" class="shrink-0 text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline">Buka →</a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    </div>

    <!-- Floating Save Bar -->
    <div class="fixed bottom-0 left-0 right-0 z-[100] xl:left-64 pointer-events-none">
        <div class="px-4 pb-4 sm:pb-6 pt-8 bg-gradient-to-t from-slate-50 via-slate-50/90 to-transparent dark:from-[#0B1120] dark:via-[#0B1120]/90 pointer-events-auto">
            <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl rounded-2xl p-4 transition-all">
                <div class="flex items-center gap-3">
                    <!-- Icon Status -->
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl transition-colors bg-slate-100 dark:bg-slate-700"
                         wire:dirty.class.remove="bg-slate-100 dark:bg-slate-700"
                         wire:dirty.class="bg-orange-100 dark:bg-orange-500/10"
                         x-bind:class="($wire.new_logo || $wire.new_og_image || $wire.new_qris_image) ? 'bg-orange-100 dark:bg-orange-500/10' : ''">
                        
                        <i class="ph ph-check-circle text-xl text-slate-400 dark:text-slate-500" 
                           wire:dirty.class="hidden"
                           x-show="!$wire.new_logo && !$wire.new_og_image && !$wire.new_qris_image"></i>
                        
                        <i class="ph ph-warning-circle text-xl text-orange-600 dark:text-orange-400 hidden" 
                           wire:dirty.class.remove="hidden"
                           x-show="$wire.new_logo || $wire.new_og_image || $wire.new_qris_image" x-cloak></i>
                    </div>
                    <!-- Text Status -->
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">
                            Status Pengaturan
                        </p>
                        <p class="text-xs font-medium transition-colors duration-300 text-slate-500 dark:text-slate-400"
                           wire:dirty.class.remove="text-slate-500 dark:text-slate-400"
                           wire:dirty.class="text-orange-600 dark:text-orange-400"
                           x-bind:class="($wire.new_logo || $wire.new_og_image || $wire.new_qris_image) ? '!text-orange-600 !dark:text-orange-400' : ''">
                            
                            <span wire:dirty.class="hidden" x-show="!$wire.new_logo && !$wire.new_og_image && !$wire.new_qris_image">Semua perubahan telah tersimpan</span>
                            <span class="hidden" wire:dirty.class.remove="hidden" x-show="$wire.new_logo || $wire.new_og_image || $wire.new_qris_image" x-cloak>Ada perubahan yang belum disimpan</span>
                        </p>
                    </div>
                </div>

                <button wire:click="save"
                        class="w-full sm:w-auto px-8 py-2.5 text-sm font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 shrink-0"
                        :class="($wire.$dirty() || $wire.new_logo || $wire.new_og_image || $wire.new_qris_image) 
                                ? 'bg-orange-500 hover:bg-orange-600 hover:shadow-orange-500/25 hover:shadow-lg text-white' 
                                : 'bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-400 opacity-50 cursor-not-allowed'"
                        :disabled="!$wire.$dirty() && !$wire.new_logo && !$wire.new_og_image && !$wire.new_qris_image">
                    <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                        <i class="ph-fill ph-dot text-lg animate-pulse" 
                           x-show="$wire.$dirty() || $wire.new_logo || $wire.new_og_image || $wire.new_qris_image" x-cloak></i>
                        Simpan Perubahan
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <i class="ph ph-spinner animate-spin text-lg"></i> Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>

</div>