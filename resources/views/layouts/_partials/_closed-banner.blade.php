@if(!$isOpenNow)
    <div class="sticky top-0 z-40 bg-amber-50 dark:bg-amber-950/40 border-b border-amber-200 dark:border-amber-900/50 px-4 py-2.5 w-full max-w-xl mx-auto shadow-sm backdrop-blur-md">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 text-amber-500">
                <i class="ph-fill ph-warning-circle text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-400">
                    Toko Sedang Tutup
                </p>
                <p class="text-xs text-amber-700/80 dark:text-amber-500/80 mt-0.5 leading-relaxed">
                    @if($todayHours['is_closed'])
                        Kami tutup hari ini. Silakan kembali besok.
                    @else
                        Kami buka jam <span class="font-bold">{{ $todayHours['open'] }}</span>.
                    @endif
                    Menu dapat dilihat, tapi order tidak dapat diproses saat ini.
                </p>
            </div>
        </div>
    </div>
@endif
