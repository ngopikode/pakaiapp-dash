<div id="categoryModal"
     x-data="{ open: false }"
     x-on:open-category-modal.window="open = true; window.dispatchEvent(new Event('modal-shown'));"
     :class="open ? 'flex' : 'hidden'"
     x-cloak
     class="fixed inset-0 z-[1055] items-end md:items-center justify-center">

    {{-- Overlay --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm"></div>

    {{-- Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
         class="relative w-full md:max-w-md bg-white dark:bg-slate-800 rounded-t-[1.5rem] md:rounded-2xl shadow-xl overflow-hidden"
         style="max-height: 90vh;">

        <div class="flex md:hidden justify-center pt-2 pb-1">
            <div class="h-1.5 w-10 rounded-full bg-slate-300 dark:bg-slate-600"></div>
        </div>

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-700">
            <h5 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ph-bold ph-folders text-orange-500 text-xl"></i>
                {{ $isEditing ? 'Edit Kategori' : 'Tambah Kategori' }}
            </h5>
            <button type="button" @click="open = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                <i class="ph-bold ph-x text-base"></i>
            </button>
        </div>

        <form wire:submit.prevent="save">
            <div class="px-5 py-6">
                <label for="categoryName" class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2 block">Nama Kategori</label>
                <input type="text"
                       class="w-full h-11 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-shadow @error('name') border-red-500 @enderror"
                       id="categoryName"
                       wire:model="name"
                       placeholder="cth: Roti, Minuman, Makanan">
                @error('name')
                    <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center gap-3 px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                <button type="button" @click="open = false"
                        class="flex-1 md:flex-none px-5 py-2.5 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 rounded-full bg-[#E65C2C] hover:bg-[#D44A1A] text-white text-sm font-bold shadow-sm transition-all duration-200 flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="save">
                        <i class="ph-bold ph-check text-base"></i> Simpan
                    </span>
                    <span wire:loading wire:target="save">
                        <i class="ph-bold ph-circle-notch text-base animate-spin"></i> Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@script
<script>
    $wire.on('show-category-modal', () => {
        if (typeof window.hideLoader === 'function') window.hideLoader();
        Alpine.evaluate(document.getElementById('categoryModal'), 'open = true');
        setTimeout(() => { if (typeof window.hideLoader === 'function') window.hideLoader(); }, 100);
    });
    $wire.on('hide-category-modal', () => {
        Alpine.evaluate(document.getElementById('categoryModal'), 'open = false');
    });
</script>
@endscript