@php
    $heroImage = $setting->hero_image ?? null;
    $heroTitle = $setting->hero_title ?? 'Selamat Datang di Toko Kami';
    $heroSubtitle = $setting->hero_subtitle ?? 'Temukan produk-produk unggulan dengan harga terbaik di sini.';
@endphp

<div class="relative w-full max-w-xl mx-auto mb-2 bg-white">
    @if($heroImage)
        <div class="w-full aspect-[21/9] sm:aspect-[3/1] relative overflow-hidden">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($heroImage) }}" alt="{{ $heroTitle }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex flex-col justify-end p-5">
                <h2 class="text-white font-bold text-xl leading-tight mb-1">{{ $heroTitle }}</h2>
                <p class="text-white/80 text-xs line-clamp-2 max-w-sm">{{ $heroSubtitle }}</p>
            </div>
        </div>
    @else
        <div class="px-5 pt-6 pb-2">
            <div class="bg-gradient-to-br from-[var(--primary-color)] to-blue-600 rounded-2xl p-5 text-white relative overflow-hidden shadow-lg shadow-blue-500/20">
                <div class="absolute top-0 right-0 -mr-6 -mt-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -ml-6 -mb-6 w-24 h-24 bg-black/10 rounded-full blur-xl"></div>
                
                <div class="relative z-10">
                    <span class="inline-block px-2.5 py-1 bg-white/20 backdrop-blur rounded-full text-[10px] font-black uppercase tracking-widest mb-3">Toko Online</span>
                    <h2 class="font-extrabold text-2xl leading-tight mb-1.5">{{ $heroTitle }}</h2>
                    <p class="text-white/90 text-xs leading-relaxed max-w-[280px]">{{ $heroSubtitle }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
