<div class="relative flex flex-col h-full" x-data="{ 
    tab: 'general',
    variants: $wire.entangle('variants'),
    baseRecipes: $wire.entangle('baseRecipes'),
    extras: $wire.entangle('extras'),
    hasVariants: $wire.entangle('hasVariants')
}">
    <div class="shrink-0 px-4 md:px-6 py-3 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-between">
        <div>
            <h2 class="text-base font-black text-slate-900 dark:text-white">
                {{ $product ? 'Edit Produk' : 'Tambah Produk' }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Lengkapi informasi produk</p>
        </div>
        <div class="flex items-center gap-2">
            <span x-show="$wire.$dirty()" x-cloak
                  class="text-[10px] font-bold text-orange-500 bg-orange-50 dark:bg-orange-500/10 px-2 py-1 rounded-full">
                Belum tersimpan
            </span>
            <button type="button" @click="$dispatch('close-product-form')"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                <i class="ph-bold ph-x text-lg"></i>
            </button>
        </div>
    </div>

    <div wire:loading.delay class="shrink-0 px-4 md:px-6 py-2 bg-orange-50 dark:bg-orange-500/5 border-b border-orange-200 dark:border-orange-500/20">
        <div class="flex items-center gap-2 text-xs font-bold text-orange-600 dark:text-orange-400">
            <div class="w-3 h-3 rounded-full border-2 border-orange-400 border-t-transparent animate-spin"></div>
            Sinkronisasi...
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="shrink-0 px-4 md:px-6 py-2 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex gap-1 overflow-x-auto [scrollbar-width:none]">
        <button type="button" @click="tab = 'general'"
                class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors whitespace-nowrap"
                :class="tab === 'general' ? 'bg-white dark:bg-slate-800 text-orange-500 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
            <i class="ph-bold ph-info me-1"></i> Data Umum
        </button>
        <button type="button" @click="tab = 'pricing'"
                class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors whitespace-nowrap"
                :class="tab === 'pricing' ? 'bg-white dark:bg-slate-800 text-orange-500 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
            <i class="ph-bold ph-tag me-1"></i> Harga & Varian
        </button>
        <button type="button" @click="tab = 'recipe'"
                class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors whitespace-nowrap"
                :class="tab === 'recipe' ? 'bg-white dark:bg-slate-800 text-orange-500 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
            <i class="ph-bold ph-package me-1"></i> Resep
        </button>
        <button type="button" @click="tab = 'extras'"
                class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors whitespace-nowrap"
                :class="tab === 'extras' ? 'bg-white dark:bg-slate-800 text-orange-500 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
            <i class="ph-bold ph-plus-circle me-1"></i> Add-ons
        </button>
    </div>

    {{-- Scrollable Body --}}
    <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-4 md:px-6 py-4">
        <form wire:submit.prevent="save" id="product-form">
            {{-- TAB: GENERAL --}}
            <div x-show="tab === 'general'" x-transition.opacity.duration.200ms>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="name"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none"
                                   placeholder="Nama menu/produk">
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select wire:model="categoryId"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                            @error('categoryId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Deskripsi</label>
                            <textarea wire:model="description" rows="3"
                                      class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none resize-none"
                                      placeholder="Deskripsi singkat produk"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Status Tampil</label>
                            <label class="relative inline-flex items-center cursor-pointer h-6 w-11 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors">
                                <input type="checkbox" class="sr-only peer" wire:model="isActive">
                                <div class="w-5 h-5 bg-white rounded-full peer peer-checked:translate-x-[20px] transition-transform shadow-sm absolute left-[2px] peer-checked:bg-emerald-500 border border-slate-300 peer-checked:border-emerald-600"></div>
                            </label>
                            <span x-text="$wire.isActive ? 'Aktif' : 'Tidak aktif'" class="text-xs text-slate-500 dark:text-slate-400 ms-2"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1 text-center">Foto Produk</label>
                        <div class="relative mx-auto w-full max-w-[200px] aspect-square rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 overflow-hidden cursor-pointer hover:border-orange-500/50 transition-colors">
                            @if ($image)
                                @php try { $url = $image->temporaryUrl(); } catch (Exception $e) { $url = ''; } @endphp
                                @if($url)
                                    <img src="{{ $url }}" class="w-full h-full object-cover" alt="">
                                @endif
                            @elseif($product && $product->image)
                                <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover" alt="">
                            @else
                                <div class="flex flex-col items-center justify-center h-full text-slate-300">
                                    <i class="ph-bold ph-camera text-3xl mb-1"></i>
                                    <span class="text-[10px] font-bold">Upload</span>
                                </div>
                            @endif
                            <input type="file" wire:model="image" accept="image/*"
                                   class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                        <div wire:loading wire:target="image"
                             class="mt-2 text-center text-xs font-bold text-orange-500">
                            <i class="ph-bold ph-spinner animate-spin"></i> Mengunggah...
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB: PRICING & VARIANTS --}}
            <div x-show="tab === 'pricing'" x-transition.opacity.duration.200ms x-cloak>
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 mb-4">
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Gunakan Varian</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Ukuran (S/M/L), Rasa, dll.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer h-6 w-11 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors">
                        <input type="checkbox" class="sr-only peer" x-model="hasVariants">
                        <div class="w-5 h-5 bg-white rounded-full peer peer-checked:translate-x-[20px] transition-transform shadow-sm absolute left-[2px] peer-checked:bg-orange-500 border border-slate-300 peer-checked:border-orange-600"></div>
                    </label>
                </div>

                <div x-show="$wire.hasVariants" x-cloak>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 mb-4">
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mb-2"><i class="ph-bold ph-check-square me-1"></i>Aturan Pilihan Pelanggan</p>
                        <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 mb-1 block">Tipe Seleksi</label>
                                    <select x-model="$wire.selectionType" wire:model="selectionType"
                                            class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none">
                                        <option value="single">Pilih 1 (Radio)</option>
                                        <option value="multiple">Pilih Banyak</option>
                                    </select>
                                </div>
                                <div x-show="$wire.selectionType === 'multiple'">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 mb-1 block">Maks Pilihan</label>
                                    <input type="number" wire:model="maxSelections" min="1" max="20"
                                           class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- No Variants: Simple Fields --}}
                <div x-show="!$wire.hasVariants" x-cloak>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 mb-1 block">Modal / HPP</label>
                            <input type="number" wire:model="baseCost"
                                   class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                   placeholder="0">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-red-500 mb-1 block">Harga Jual *</label>
                            <input type="number" wire:model="basePrice"
                                   class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none font-bold"
                                   placeholder="0">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 mb-1 block">Stok</label>
                            <input type="number" wire:model="baseStock"
                                   class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                   placeholder="0">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 mb-1 block">Min Stok</label>
                            <input type="number" wire:model="baseMinStock"
                                   class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                   placeholder="0">
                        </div>
                    </div>
                </div>

                {{-- Has Variants: Card List --}}
                <div x-show="hasVariants" x-cloak>
                    <div class="hidden md:grid grid-cols-12 gap-2 text-[10px] font-bold text-slate-500 mb-2 px-1">
                        <div class="col-span-4">Nama Varian</div>
                        <div class="col-span-3">Modal</div>
                        <div class="col-span-2">Harga</div>
                        <div class="col-span-2">Stok</div>
                        <div class="col-span-1 text-center"><i class="ph-bold ph-gear"></i></div>
                    </div>
                    <div class="space-y-2 mb-3">
                        <template x-for="(variant, index) in variants" :key="index">
                            <div class="grid grid-cols-2 md:grid-cols-12 gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 items-center">
                                <div class="col-span-2 md:col-span-4">
                                    <label class="md:hidden text-[10px] font-bold text-slate-500 mb-1 block">Nama</label>
                                    <input type="text" x-model="variant.name"
                                           class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                           placeholder="Large">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="md:hidden text-[10px] font-bold text-slate-500 mb-1 block">Modal</label>
                                    <input type="number" x-model="variant.cost"
                                           class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                           placeholder="0">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="md:hidden text-[10px] font-bold text-red-500 mb-1 block">Harga</label>
                                    <input type="number" x-model="variant.price"
                                           class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none font-bold"
                                           placeholder="0">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="md:hidden text-[10px] font-bold text-slate-500 mb-1 block">Stok</label>
                                    <input type="number" x-model="variant.stock"
                                           class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                           placeholder="0">
                                </div>
                                <div class="md:col-span-1 text-center">
                                    <button type="button" x-show="variants.length > 1"
                                            @click="variants.splice(index, 1)"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10 text-red-500 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                                        <i class="ph-bold ph-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button type="button"
                            @click="variants.push({ id: null, name: '', cost: '', price: '', stock: '', minStock: '', recipes: [] })"
                            class="w-full py-2 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-500 hover:text-orange-500 hover:border-orange-500/50 transition-colors">
                        <i class="ph-bold ph-plus me-1"></i> Tambah Varian
                    </button>
                </div>
            </div>

            {{-- TAB: RECIPE (BOM) --}}
                <div x-show="tab === 'recipe'" x-transition.opacity.duration.200ms x-cloak>
                    <div x-show="!hasVariants" x-cloak>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 mb-4">
                            <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mb-3">Bahan Baku untuk Produk Ini</p>
                            <template x-for="(recipe, rIndex) in baseRecipes" :key="rIndex">
                                <div class="grid grid-cols-12 gap-2 items-center mb-2">
                                    <div class="col-span-6">
                                        <select x-model="recipe.raw_material_id"
                                                class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none">
                                            <option value="">-- Pilih --</option>
                                            @foreach($rawMaterials as $rm)
                                                <option value="{{ $rm['id'] }}">{{ $rm['name'] }} ({{ $rm['unit'] }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-4">
                                        <input type="number" step="0.01" x-model="recipe.quantity_used"
                                               class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                               placeholder="Takaran">
                                    </div>
                                        <div class="col-span-2 text-center">
                                            <button type="button" @click="baseRecipes.splice(rIndex, 1)"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10 text-red-500 hover:bg-red-100 transition-colors text-xs">
                                                <i class="ph-bold ph-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <button type="button"
                                        @click="baseRecipes.push({ id: null, raw_material_id: '', quantity_used: '' })"
                                    class="mt-2 text-xs font-bold text-orange-500 hover:text-orange-600 transition-colors">
                                <i class="ph-bold ph-plus me-1"></i> Tambah Bahan
                            </button>
                        </div>
                    </div>
                    <div x-show="hasVariants" x-cloak>
                        <template x-for="(variant, vIndex) in variants" :key="vIndex">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 mb-4">
                                <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mb-3">
                                    Bahan Baku: <span class="text-orange-500" x-text="variant.name || 'Varian ' + (vIndex + 1)"></span>
                                </p>
                                <template x-for="(recipe, rIndex) in variant.recipes" :key="rIndex">
                                    <div class="grid grid-cols-12 gap-2 items-center mb-2">
                                        <div class="col-span-6">
                                            <select x-model="recipe.raw_material_id"
                                                    class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none">
                                                <option value="">-- Pilih --</option>
                                                @foreach($rawMaterials as $rm)
                                                    <option value="{{ $rm['id'] }}">{{ $rm['name'] }} ({{ $rm['unit'] }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-4">
                                            <input type="number" step="0.01" x-model="recipe.quantity_used"
                                                   class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                                   placeholder="Takaran">
                                        </div>
                                        <div class="col-span-2 text-center">
                                            <button type="button" @click="variant.recipes.splice(rIndex, 1)"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10 text-red-500 hover:bg-red-100 transition-colors text-xs">
                                                <i class="ph-bold ph-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <button type="button"
                                        @click="if(!variant.recipes) variant.recipes = []; variant.recipes.push({ id: null, raw_material_id: '', quantity_used: '' })"
                                        class="mt-2 text-xs font-bold text-orange-500 hover:text-orange-600 transition-colors">
                                    <i class="ph-bold ph-plus me-1"></i> Tambah Bahan
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- TAB: EXTRAS / ADD-ONS --}}
                <div x-show="tab === 'extras'" x-transition.opacity.duration.200ms x-cloak>
                    <div class="hidden md:grid grid-cols-12 gap-2 text-[10px] font-bold text-slate-500 mb-2 px-1">
                        <div class="col-span-5">Nama Add-on</div>
                        <div class="col-span-3">Modal</div>
                        <div class="col-span-3">Harga Jual</div>
                        <div class="col-span-1 text-center"><i class="ph-bold ph-gear"></i></div>
                    </div>
                    <div class="space-y-2 mb-3">
                        <template x-for="(extra, index) in extras" :key="index">
                            <div class="grid grid-cols-2 md:grid-cols-12 gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 items-center">
                                <div class="col-span-2 md:col-span-5">
                                    <label class="md:hidden text-[10px] font-bold text-slate-500 mb-1 block">Nama</label>
                                    <input type="text" x-model="extra.name"
                                           class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                           placeholder="Ekstra Keju">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="md:hidden text-[10px] font-bold text-slate-500 mb-1 block">Modal</label>
                                    <input type="number" x-model="extra.cost"
                                           class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none"
                                           placeholder="0">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="md:hidden text-[10px] font-bold text-red-500 mb-1 block">Harga</label>
                                    <input type="number" x-model="extra.price"
                                           class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none font-bold"
                                           placeholder="0">
                                </div>
                                <div class="md:col-span-1 text-center">
                                    <button type="button" @click="extras.splice(index, 1)"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10 text-red-500 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                                        <i class="ph-bold ph-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button type="button"
                            @click="extras.push({ id: null, name: '', cost: '', price: '' })"
                            class="w-full py-2 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-500 hover:text-orange-500 hover:border-orange-500/50 transition-colors">
                        <i class="ph-bold ph-plus me-1"></i> Tambah Add-on
                    </button>
                </div>
        </form>
    </div>

    {{-- Sticky Footer --}}
    <div class="shrink-0 px-4 md:px-6 py-3 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-between gap-2">
        <div class="flex gap-2">
            <button type="button" x-show="tab !== 'general'" @click="tab = tab === 'extras' ? 'recipe' : (tab === 'recipe' ? 'pricing' : 'general')"
                    class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <i class="ph-bold ph-caret-left me-1"></i> Kembali
            </button>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    x-show="tab === 'general'"
                    @click="tab = 'pricing'"
                    class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold transition-colors">
                Lanjut <i class="ph-bold ph-caret-right"></i>
            </button>
            <button type="button"
                    x-show="tab === 'pricing'"
                    @click="tab = 'recipe'"
                    class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold transition-colors">
                Lanjut <i class="ph-bold ph-caret-right"></i>
            </button>
            <button type="button"
                    x-show="tab === 'recipe'"
                    @click="tab = 'extras'"
                    class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold transition-colors">
                Lanjut <i class="ph-bold ph-caret-right"></i>
            </button>
            <button type="submit" form="product-form"
                    x-show="tab === 'extras'"
                    :class="$wire.$dirty() ? 'bg-orange-500 ring-4 ring-orange-500/30 hover:bg-orange-600' : 'bg-emerald-600 hover:bg-emerald-700'"
                    class="px-5 py-2 rounded-xl text-white text-sm font-bold transition-all shadow-sm"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save"><i class="ph-bold ph-check me-1"></i> Simpan</span>
                <span wire:loading wire:target="save" class="flex items-center gap-1"><i class="ph-bold ph-spinner animate-spin"></i> Menyimpan...</span>
            </button>
        </div>
    </div>
</div>