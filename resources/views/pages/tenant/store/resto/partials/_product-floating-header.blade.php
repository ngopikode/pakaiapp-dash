{{-- Floating header: transparan → solid saat scroll, tombol back + share --}}
<header
    class="fixed top-0 left-0 right-0 z-[110] transition-all duration-300 px-4 py-3 flex items-center justify-between max-w-xl mx-auto"
    :class="scrolled ? 'bg-[var(--surface)]/90 backdrop-blur-xl border-b border-[var(--border)] shadow-sm' : 'bg-transparent'"
>
    <a
        href="/"
        wire:navigate.hover
        class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border"
        :class="scrolled ? 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)]' : 'bg-black/20 backdrop-blur-md text-white border-white/20'"
        aria-label="Kembali ke menu"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m12 19-7-7 7-7"/>
            <path d="M19 12H5"/>
        </svg>
    </a>

    <h2
        class="text-sm font-black text-[var(--foreground)] truncate max-w-[200px] transition-all duration-300"
        :class="scrolled ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'"
        x-text="product.name"
    ></h2>

    <div class="flex items-center gap-2">
        {{-- Share ke WhatsApp Story --}}
        <a
            :href="'{{ route('product.story', $product) }}'"
            target="_blank" rel="noreferrer"
            class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border hover:bg-[#25D366] hover:text-[var(--background)] hover:border-[#25D366]"
            :class="scrolled ? 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)]' : 'bg-black/20 backdrop-blur-md text-white border-white/20'"
            aria-label="Bagikan ke WhatsApp"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.489-1.761-1.663-2.06-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
        </a>

        {{-- Share link (native share / clipboard) --}}
        <button
            @click.prevent.stop="$store.utils.shareProduct({{ json_encode($productData) }}, '{{ $storeName }}')"
            class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border"
            :class="scrolled ? 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)]' : 'bg-black/20 backdrop-blur-md text-white border-white/20'"
            aria-label="Bagikan link"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="18" cy="5" r="3"/>
                <circle cx="6" cy="12" r="3"/>
                <circle cx="18" cy="19" r="3"/>
                <line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/>
                <line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/>
            </svg>
        </button>
    </div>
</header>
