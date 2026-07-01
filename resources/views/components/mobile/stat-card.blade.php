<div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between active:scale-95 transition-transform duration-200">
    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $title }}</h3>
        <div class="mt-1 text-2xl font-bold text-gray-800">
            {{ $value }}
        </div>
    </div>
    <!-- Mobile specific: Simplified UI, bigger touch targets, no complex charts -->
    <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
    </div>
</div>
