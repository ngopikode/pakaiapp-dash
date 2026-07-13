@php
    $isPosNavbar = is_array($header) && ($header['mode'] ?? null) === 'pos';
    $navbarTitle = $isPosNavbar ? ($header['title'] ?? 'PakaiApp POS') : ($header ?? 'Dashboard');
@endphp

<nav
    class="sticky top-0 z-40 w-full bg-white/85 dark:bg-[#0B1120]/85 backdrop-blur-xl border-b border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between gap-3 px-4 lg:px-8 h-16 lg:h-[72px] transition-colors"
    id="mainNavbar">

    <div class="flex min-w-0 items-center gap-3">
        <button
            class="xl:hidden p-2 -ml-2 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 transition-colors"
            type="button"
            @click="$dispatch('open-mobile-sidebar')">
            <i class="ph-bold ph-squares-four text-2xl"></i>
        </button>
        <span
            class="lg:hidden font-sans font-extrabold text-slate-900 dark:text-white truncate max-w-[150px] text-lg tracking-tight"
            title="{{ $navbarTitle }}">
            {{ $navbarTitle }}
        </span>

        <button
            class="hidden xl:block p-2.5 -ml-3 text-slate-400 dark:text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/80 dark:hover:bg-slate-800/50 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 transition-colors"
            @click="showDesktopSidebar = !showDesktopSidebar">
            <i class="ph-bold ph-squares-four text-[22px]"></i>
        </button>
        @if($isPosNavbar)
            <div class="hidden min-w-0 flex-col leading-tight lg:flex"
                 x-data="{ now: new Date(), init() { setInterval(() => this.now = new Date(), 1000) }, formatted() { const d=this.now,p=n=>String(n).padStart(2,'0'),w=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'],m=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; return w[d.getDay()]+', '+p(d.getDate())+' '+m[d.getMonth()]+' '+d.getFullYear()+' pukul '+p(d.getHours())+':'+p(d.getMinutes())+':'+p(d.getSeconds()) } }">
                <div class="flex items-center gap-2 text-emerald-800 dark:text-emerald-400">
                    <i class="ph-fill ph-coffee text-2xl"></i>
                    <div class="text-xl font-black tracking-tight">{{ $navbarTitle }}</div>
                </div>
                <div class="mt-0.5 text-xs font-bold text-emerald-800/70 dark:text-emerald-400/70"
                     x-text="formatted()"></div>
            </div>
        @else
            <h5 class="hidden lg:block m-0 font-sans font-black text-slate-900 dark:text-white truncate max-w-[300px] text-xl tracking-tight">
                {{ $navbarTitle }}
            </h5>
        @endif
    </div>

    @if($isPosNavbar)
        <div class="flex flex-1 items-center justify-center gap-3"
             x-data="{ tab: 'cashier', change(v) { this.tab = v; window.dispatchEvent(new CustomEvent('pos-change-tab', { detail: v })) } }"
             @force-cashier-tab.window="tab = 'cashier'">
            <span class="hidden text-sm font-bold text-emerald-800/80 dark:text-emerald-400/80 sm:inline">Total: {{ $this->pendingOrdersCount }} Orders</span>
            <div class="inline-flex rounded-full bg-slate-100 p-1 shadow-inner dark:bg-slate-900">
                <button type="button" @click="change('cashier')"
                        class="rounded-full px-3 py-2 text-xs font-black transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 lg:px-4 lg:text-sm"
                        :class="tab === 'cashier' ? 'bg-white text-emerald-800 shadow-sm dark:bg-slate-800 dark:text-emerald-400' : 'text-slate-600 hover:text-emerald-800 dark:text-slate-300 dark:hover:text-emerald-400'">
                    Kasir
                </button>
                <button type="button" @click="change('queue')"
                        class="relative rounded-full px-3 py-2 text-xs font-black transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 lg:px-4 lg:text-sm"
                        :class="tab === 'queue' ? 'bg-white text-emerald-800 shadow-sm dark:bg-slate-800 dark:text-emerald-400' : 'text-slate-600 hover:text-emerald-800 dark:text-slate-300 dark:hover:text-emerald-400'">
                    Pesanan
                    @if($this->pendingOrdersCount > 0)
                        <span
                            class="absolute -right-1 -top-1 rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] text-white">{{ $this->pendingOrdersCount }}</span>
                    @endif
                </button>
            </div>
            <button id="tour-pos-help" type="button"
                    @click="window.dispatchEvent(new CustomEvent('force-cashier-tab')); setTimeout(() => window.dispatchEvent(new CustomEvent('start-pos-tour')), 300)"
                    class="hidden h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-emerald-800 transition hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 sm:flex dark:bg-slate-900 dark:text-emerald-400 dark:hover:bg-slate-800"
                    title="Panduan & Tutorial Penggunaan">
                <i class="ph-bold ph-lightbulb text-lg"></i>
            </button>
            <button x-data="{ theme: localStorage.getItem('theme') || 'light' }"
                    @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', theme); document.documentElement.classList.toggle('dark', theme === 'dark')"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 transition hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 dark:bg-slate-900 dark:hover:bg-slate-800"
                    title="Ganti Tema">
                <i x-show="theme === 'dark'" class="ph-fill ph-sun text-[18px] text-orange-400" x-cloak></i>
                <i x-show="theme === 'light'" class="ph-fill ph-moon text-[18px] text-slate-500" x-cloak></i>
            </button>
        </div>
    @endif

    <div class="flex items-center gap-1.5 lg:gap-3">
        @if(!$isPosNavbar)
            @island(name: 'notification-badge', defer: true)
            @placeholder
            <div class="relative">
                <button
                    class="relative p-2 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-100/80 dark:hover:bg-slate-800/50 transition-colors focus:outline-none"
                    disabled>
                    <i class="ph-bold ph-bell text-[22px] opacity-40"></i>
                </button>
            </div>
            @endplaceholder

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false"
                        class="relative p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 rounded-xl hover:bg-slate-100/80 dark:hover:bg-slate-800/50 transition-colors flex items-center focus:outline-none">
                    <i class="ph-bold ph-bell text-[22px]"></i>
                    @if($this->pendingOrdersCount > 0)
                        <span
                            class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-[#0B1120] animate-pulse">
                            {{ $this->pendingOrdersCount }}
                        </span>
                    @endif
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute right-0 mt-3 w-[320px] bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200/60 dark:border-slate-700/60 z-50 overflow-hidden"
                     style="display: none;">
                    <div
                        class="px-5 py-4 border-b border-border flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                        <span class="font-bold text-slate-800 dark:text-slate-200 tracking-tight text-[13px] uppercase">Notifikasi</span>
                        @if($this->pendingOrdersCount > 0)
                            <span
                                class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400 text-[11px] font-bold">{{ $this->pendingOrdersCount }} Baru</span>
                        @endif
                    </div>
                    <div class="max-h-[300px] overflow-y-auto">
                        @if($this->pendingOrdersCount > 0)
                            <a href="{{ route('order') }}" wire:navigate.hover
                               class="block p-5 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                                <div class="flex justify-between items-start mb-1.5">
                                    <span
                                        class="font-bold text-slate-800 dark:text-slate-200 flex items-center text-sm">
                                        <div
                                            class="w-7 h-7 rounded-full bg-orange-500/10 dark:bg-orange-500/20 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                            <i class="ph-fill ph-bag text-orange-600 dark:text-orange-400 text-base"></i>
                                        </div>
                                        Pesanan Baru
                                    </span>
                                    <i class="ph-bold ph-caret-right text-slate-300 dark:text-slate-600 group-hover:text-orange-500 transition-colors"></i>
                                </div>
                                <p class="text-[13px] text-slate-500 dark:text-slate-400 leading-relaxed m-0 pl-10">
                                    Ada {{ $this->pendingOrdersCount }} pesanan yang menunggu konfirmasi pembayaran.
                                    Segera cek!</p>
                            </a>
                        @else
                            <div class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                <div
                                    class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center mx-auto mb-3">
                                    <i class="ph-fill ph-check-circle text-2xl opacity-60"></i>
                                </div>
                                <p class="text-[13px] font-bold m-0 text-slate-500 dark:text-slate-400">Semua pesanan
                                    sudah diurus</p>
                                <p class="text-[12px] text-slate-400 mt-0.5">Tidak ada antrean baru.</p>
                            </div>
                        @endif
                    </div>
                    <div class="border-t border-border bg-slate-50/80 dark:bg-slate-900/80 p-3">
                        <a href="{{ route('order') }}" wire:navigate.hover
                           class="block text-center py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 font-bold text-[13px] hover:text-orange-600 hover:border-orange-500/30 dark:hover:text-orange-400 transition-all shadow-sm">
                            Lihat Semua Pesanan
                        </a>
                    </div>
                </div>
            </div>
            @endisland
        @endif

        <ul class="flex items-center gap-1.5 lg:gap-3 m-0 p-0 list-none">
            @if(!$isPosNavbar)
                <li>
                    <button
                        x-data="{ theme: localStorage.getItem('theme') || 'light' }"
                        @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', theme); document.documentElement.classList.toggle('dark', theme === 'dark')"
                        class="relative p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 rounded-xl hover:bg-slate-100/80 dark:hover:bg-slate-800/50 transition-colors focus:outline-none"
                        title="Ganti Tema"
                    >
                        <i x-show="theme === 'dark'" class="ph-fill ph-sun text-[22px] text-orange-400" x-cloak></i>
                        <i x-show="theme === 'light'" class="ph-fill ph-moon text-[22px] text-slate-600" x-cloak></i>
                    </button>
                </li>
            @endif

            <li x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-2 p-1.5 pr-2.5 lg:pr-3 rounded-full border border-transparent hover:border-slate-200 dark:hover:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors focus:outline-none ml-1">
                    <div
                        class="w-8 h-8 lg:w-9 lg:h-9 rounded-full bg-gradient-to-tr from-slate-800 to-slate-600 dark:from-slate-700 dark:to-slate-500 text-white flex items-center justify-center shadow-sm shrink-0 font-bold text-[14px]">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="text-left leading-tight hidden lg:block mr-1">
                        <div class="font-bold text-[13px] text-slate-800 dark:text-slate-200 truncate max-w-[120px]">
                            {{ Auth::user()->name ?? 'User' }}
                        </div>
                        <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Admin</div>
                    </div>
                    <i class="ph-bold ph-caret-down text-slate-400 dark:text-slate-500 hidden lg:block text-[10px] transition-transform duration-200"
                       :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute right-0 mt-3 w-56 bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200/60 dark:border-slate-700/60 z-50 p-2"
                     style="display: none;">
                    <div class="px-3 py-3 border-b border-border mb-1 lg:hidden flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-tr from-slate-800 to-slate-600 dark:from-slate-700 dark:to-slate-500 text-white flex items-center justify-center shadow-sm shrink-0 font-bold text-lg">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <span
                                class="font-bold block text-slate-800 dark:text-slate-200 text-sm">{{ Auth::user()->name ?? 'User' }}</span>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Admin</span>
                        </div>
                    </div>
                    <a href="{{ route('profile') }}" wire:navigate.hover
                       class="flex items-center px-3 py-2.5 text-[13px] font-medium text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-100/80 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <i class="ph-fill ph-user-circle mr-2.5 text-[18px] text-slate-400 dark:text-slate-500"></i>
                        Edit Profil
                    </a>
                    <div class="my-1 border-t border-border mx-2"></div>
                    <button type="button" wire:click="logout()"
                            class="w-full flex items-center px-3 py-2.5 text-[13px] font-bold text-red-600 rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors text-left group">
                        <i class="ph-bold ph-sign-out mr-2.5 text-[18px] group-hover:-translate-x-0.5 transition-transform"></i>
                        Log Out
                    </button>
                </div>
            </li>
        </ul>
    </div>
</nav>
