{{-- ===== CONTACT MODAL ===== --}}
<div
    x-data="{ contactOpen: false }"
    @open-contact-modal.window="contactOpen = true"
    @keydown.escape.window="contactOpen = false; $dispatch('close-contact-modal')"
    x-show="contactOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-[var(--foreground)]/80 backdrop-blur-sm z-[200] flex items-center justify-center p-6"
    style="display:none"
>
    <div @click.outside="contactOpen = false; $dispatch('close-contact-modal')"
         class="bg-[var(--surface)] w-full max-w-sm rounded-3xl p-6 text-center relative overflow-hidden shadow-2xl">
        <div
            class="w-12 h-12 bg-[var(--bg-soft)] rounded-full flex items-center justify-center mx-auto mb-4 text-[var(--foreground)]">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
        </div>
        <h3 class="font-bold text-[var(--foreground)] mb-1 text-lg">Hubungi Kami</h3>
        <p class="text-[var(--text-secondary)] text-sm mb-6">{{ $setting->name ?: 'Nama Toko' }}</p>

        <div class="bg-[var(--background)] rounded-2xl p-4 space-y-4 text-left">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 text-[var(--text-secondary)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-[var(--text-secondary)] uppercase tracking-wider">
                        Telepon / WA</p>
                    <p class="text-sm font-medium text-[var(--foreground)]">{{ $setting->whatsapp_number ?: '-' }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="mt-0.5 text-[var(--text-secondary)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-[var(--text-secondary)] uppercase tracking-wider">
                        Alamat</p>
                    <p class="text-sm font-medium text-[var(--foreground)]">{{ $setting->address ?: '-' }}</p>
                </div>
            </div>
            @if($setting->hero_instagram_url)
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 text-[var(--text-secondary)]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-[var(--text-secondary)] uppercase tracking-wider">
                            Instagram</p>
                        <a href="{{ $setting->hero_instagram_url }}" target="_blank" rel="noreferrer"
                           class="text-sm font-medium text-[var(--primary-color)] hover:brightness-90 underline decoration-[var(--primary-color)]/30 underline-offset-2">{{ $setting->hero_instagram_url }}</a>
                    </div>
                </div>
            @endif
            <div class="flex items-start gap-3">
                <div class="mt-0.5 text-[var(--text-secondary)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect width="20" height="16" x="2" y="4" rx="2"/>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-[var(--text-secondary)] uppercase tracking-wider">Email
                        Support</p>
                    <a href="mailto:support@pakaiapp.online"
                       class="text-sm font-medium text-[var(--primary-color)] hover:brightness-90 underline decoration-[var(--primary-color)]/30 underline-offset-2">support@pakaiapp.online</a>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-[var(--border)]">
            <p class="text-[10px] text-[var(--text-secondary)]">Platform dikembangkan oleh <span
                    class="font-bold text-[var(--text-secondary)]">pakaiapp.online</span></p>
        </div>

        <button @click="contactOpen = false; $dispatch('close-contact-modal')"
                class="mt-6 w-full bg-[var(--foreground)] text-[var(--background)] py-3.5 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-zinc-700 transition-colors">
            Tutup
        </button>
    </div>
</div>

{{-- ===== GLOBAL TOAST ===== --}}
<div
    class="fixed top-4 left-4 right-4 z-[9999] sm:left-1/2 sm:right-auto sm:-translate-x-1/2 sm:top-6 sm:w-auto sm:min-w-[280px] bg-[var(--foreground)] text-[var(--background)] px-5 py-3.5 rounded-2xl sm:rounded-full shadow-2xl shadow-zinc-900/30 transition-all duration-500 ease-out flex items-center justify-center sm:justify-start gap-3 border border-white/5 backdrop-blur-xl pointer-events-none -translate-y-8 opacity-0 scale-95"
    :class="toast.show ? 'translate-y-0 opacity-100 scale-100' : '-translate-y-8 opacity-0 scale-95'"
>
    <div
        class="bg-emerald-500 rounded-full p-1 text-[var(--background)] shrink-0 shadow-lg shadow-emerald-500/30">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <path d="m9 11 3 3L22 4"/>
        </svg>
    </div>
    <span class="text-xs font-bold tracking-wide text-left flex-1 break-words" x-text="toast.message"></span>
</div>

{{-- ===== QR MODAL ===== --}}
<div
    x-show="qrOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="qrOpen = false"
    class="fixed inset-0 bg-[var(--foreground)]/80 backdrop-blur-sm z-[200] flex items-center justify-center p-6"
    style="display:none"
>
    <div @click.stop
         class="bg-[var(--surface)] w-full max-w-xs rounded-2xl p-8 text-center relative overflow-hidden">
        <div
            class="absolute top-0 left-0 w-full h-32 bg-[var(--primary-color)]/10 rounded-b-[50%] -translate-y-1/2"></div>
        <div class="relative z-10">
            <div
                class="bg-[var(--primary-color)] w-14 h-14 rounded-2xl rotate-3 flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-[var(--foreground)]">
                    <rect width="5" height="5" x="3" y="3" rx="1"/>
                    <rect width="5" height="5" x="16" y="3" rx="1"/>
                    <rect width="5" height="5" x="3" y="16" rx="1"/>
                    <path d="M21 16h-3a2 2 0 0 0-2 2v3"/>
                    <path d="M21 21v.01"/>
                    <path d="M12 7v3a2 2 0 0 1-2 2H7"/>
                    <path d="M3 12h.01"/>
                    <path d="M12 3h.01"/>
                    <path d="M12 16v.01"/>
                    <path d="M16 12h1"/>
                    <path d="M21 12v.01"/>
                    <path d="M12 21v-1"/>
                </svg>
            </div>
            <h2 class="text-xl font-black text-[var(--foreground)] mb-1">SCAN MENU</h2>
            <p class="text-[10px] text-[var(--text-secondary)] font-bold uppercase tracking-widest mb-6">Buka di
                Ponsel
                Anda</p>
            <div class="bg-[var(--surface)] p-2 rounded-xl border-2 border-dashed border-[var(--border)] mb-6">
                <img :src="qrUrl" alt="QR Code Menu" class="w-full aspect-square rounded-lg opacity-90"/>
            </div>
            <button @click="downloadQr()"
                    class="w-full bg-[var(--primary-color)] text-black py-3.5 rounded-xl text-xs font-black uppercase tracking-wider hover:brightness-110 transition-colors mb-3 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" x2="12" y1="15" y2="3"/>
                </svg>
                Download QR
            </button>
            <button @click="qrOpen = false"
                    class="w-full bg-[var(--foreground)] text-[var(--background)] py-3.5 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-zinc-700 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
