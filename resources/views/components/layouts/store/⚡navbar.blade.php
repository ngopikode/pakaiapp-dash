<?php

use Livewire\Component;

new class extends Component {
    // Ini pengganti object 'restaurant' di React props
    public string $brandText = 'Ez';
    public string $title = 'Sama Roti Kukus (Lokal)';
    public string $subtitle = 'Menu Digital';

    // Opsional: Kalau data restorannya nanti mau diambil dari database
    // public function mount($restaurant) {
    //     $this->brandText = $restaurant->brand_text ?? 'Ez';
    //     $this->title = $restaurant->title ?? 'Sama Roti Kukus (Lokal)';
    //     $this->subtitle = $restaurant->subtitle ?? 'Menu Digital';
    // }
};
?>

<nav
    class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-zinc-100/50 shadow-sm shadow-zinc-100/50">
    <div class="max-w-xl mx-auto flex justify-between items-center px-5 py-3">
        <div class="flex items-center gap-3">
            <div
                class="bg-gradient-to-br from-[var(--primary-color)] to-[var(--primary-color)] w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-[var(--primary-color)]/20 border border-black/5 rotate-[-2deg] hover:rotate-0 transition-transform duration-300">
                <span class="font-brand text-zinc-900 text-lg leading-none pt-0.5">{{ $brandText }}</span>
            </div>
            <div
                onclick="handleScrollToHero"
                class="cursor-pointer active:scale-95 transition-transform"
            >
                <h1 class="font-extrabold text-sm leading-none tracking-tight uppercase text-zinc-900">{{ $title }}</h1>
                <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-[0.2em] mt-0.5">{{ $subtitle }}</p>
            </div>
        </div>
        <button onclick="onQRClick"
                class="p-2.5 bg-zinc-50 hover:bg-[var(--primary-color)] transition-all duration-300 rounded-xl active:scale-90 hover:shadow-lg hover:shadow-[var(--primary-color)]/20 border border-zinc-100 hover:border-[var(--primary-color)]">
            <QrCode class="w-4 h-4 text-zinc-900"/>
        </button>
    </div>
</nav>
