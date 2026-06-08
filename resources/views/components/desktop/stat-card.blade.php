<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ $title }}</h3>
        <!-- Desktop specific: Maybe some complex chart icon or actions -->
        <span class="text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </span>
    </div>
    <div class="mt-4 flex items-baseline text-3xl font-bold text-gray-900">
        {{ $value }}
    </div>
    <!-- Desktop specific: Additional details shown only on desktop -->
    <div class="mt-4 text-sm text-gray-500">
        <span class="text-green-500 font-medium">↑ 12%</span> vs bulan lalu
        <br>
        <span class="text-xs text-gray-400 mt-1 block">Rata-rata interaksi harian stabil.</span>
    </div>
</div>
