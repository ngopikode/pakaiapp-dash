@php use App\Tenant\Models\Core\StoreSetting; @endphp
<aside id="{{ $elementId }}" class="flex flex-col h-full bg-transparent">

    @if($elementId !== 'mobile-sidebar-wrapper')
        <div class="px-5 py-4 flex items-center min-h-[72px]">
                <h5 class="m-0 font-sans font-black text-lg tracking-tight text-slate-800 dark:text-white flex items-center gap-2">
                <div
                    class="w-8 h-8 rounded-lg bg-gradient-to-tr from-orange-500 to-orange-400 flex items-center justify-center text-white shadow-sm shrink-0">
                    <i class="ph-bold ph-storefront text-[18px]"></i>
                </div>
                {{ StoreSetting::value('navbar_brand_text') ?? 'Navigasi Toko' }}
            </h5>
        </div>
    @endif

    <nav class="flex-1 overflow-y-auto overflow-x-hidden p-3.5 pt-1">
        @foreach($this->menuSections as $section)
            <div
                class="px-3.5 mb-2 mt-5 text-[11px] font-bold tracking-[1px] uppercase text-slate-400 dark:text-slate-500">
                {{ $section['title'] }}
            </div>

            @foreach($section['items'] as $item)
                <x-layouts.sidebar-item
                    :route="$item['route']"
                    :icon="$item['icon']"
                    :label="$item['label']"
                    :active-route="request()->route()->getName()"
                />
            @endforeach
        @endforeach
    </nav>

    <div class="p-4">
        <button type="button" onclick="if(window.installPwa) window.installPwa()"
                class="sidebar-pwa-install-btn hidden w-full items-center justify-center gap-2 py-2.5 rounded-xl font-bold transition-all shadow-sm mb-2 bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 hover:bg-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 dark:hover:bg-emerald-500/20 text-[13px]">
            <i class="ph-bold ph-download-simple text-[18px]"></i> Install App
        </button>

        <button type="button" wire:click="logout"
                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold transition-all shadow-sm bg-slate-100 text-slate-600 border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 dark:bg-slate-800/50 dark:text-slate-400 dark:border-slate-700 dark:hover:bg-red-500/10 dark:hover:text-red-400 dark:hover:border-red-500/20 text-[13px] group">
            <i class="ph-bold ph-sign-out text-[18px] group-hover:-translate-x-0.5 transition-transform"></i> Log Out
        </button>
    </div>
</aside>
