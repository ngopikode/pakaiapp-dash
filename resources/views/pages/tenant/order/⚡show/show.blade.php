<div class="bg-zinc-50 min-h-screen text-zinc-900 font-sans antialiased pb-24"
     @if($order->status === 'pending') wire:poll.15s.visible="refreshOrder" @endif>

    {{-- Fixed Top Header --}}
    <header
        class="fixed top-0 left-0 right-0 z-[110] bg-white/90 backdrop-blur-xl border-b border-zinc-100 shadow-sm px-4 py-3 flex items-center justify-between max-w-xl mx-auto">
        <button
            onclick="window.location.href = '/';"
            class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border bg-white text-zinc-900 border-zinc-200"
            aria-label="Tutup halaman"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </button>
        <h1 class="text-base font-black text-zinc-900">Detail Pesanan</h1>
        <div class="w-10"></div> {{-- Spacer to center the title --}}
    </header>

    {{-- Main Content --}}
    <main class="relative z-10 max-w-xl mx-auto pt-20 px-4">

        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-zinc-500">Invoice</span>
            <span
                class="px-3 py-1 bg-white border border-zinc-200 rounded-full text-xs font-mono font-bold text-zinc-900 shadow-sm">
                {{ $order->invoice_code }}
            </span>
        </div>

        {{-- Status Banner --}}
        <div
            class="bg-white rounded-3xl shadow-sm border border-zinc-100 p-6 mb-4 flex flex-col items-center text-center">
            @if($order->status === 'pending')
                <div
                    class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mb-4 animate-[pulse_2s_ease-in-out_infinite]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <h2 class="text-lg font-black text-zinc-900 mb-1">Menunggu Pembayaran</h2>
                <p class="text-sm text-zinc-500">Silakan selesaikan pembayaran untuk memproses pesanan ini.</p>
            @elseif($order->status === 'paid')
                <div class="w-16 h-16 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <path d="m9 11 3 3L22 4"/>
                    </svg>
                </div>
                <h2 class="text-lg font-black text-zinc-900 mb-1">Pembayaran Berhasil</h2>
                <p class="text-sm text-zinc-500">Pesanan sedang menunggu disiapkan oleh kasir.</p>
            @elseif($order->status === 'progress')
                <div
                    class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-4 animate-[pulse_2s_ease-in-out_infinite]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <h2 class="text-lg font-black text-zinc-900 mb-1">Sedang Diproses</h2>
                <p class="text-sm text-zinc-500">Pesanan Anda sedang disiapkan. Mohon ditunggu!</p>
            @elseif($order->status === 'completed')
                <div
                    class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </div>
                <h2 class="text-lg font-black text-zinc-900 mb-1">Pesanan Selesai</h2>
                <p class="text-sm text-zinc-500">Terima kasih telah berbelanja!</p>
            @elseif($order->status === 'cancelled')
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" x2="9" y1="9" y2="15"/>
                        <line x1="9" x2="15" y1="9" y2="15"/>
                    </svg>
                </div>
                <h2 class="text-lg font-black text-zinc-900 mb-1">Pesanan Dibatalkan</h2>
                <p class="text-sm text-zinc-500">Pesanan ini telah dibatalkan.</p>
            @endif
        </div>

        {{-- Order Info --}}
        <div class="bg-white rounded-3xl shadow-sm border border-zinc-100 p-5 mb-4 space-y-3">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 mb-2">Informasi Pesanan</h3>

            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Tanggal</span>
                <span class="font-bold text-zinc-900">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Tipe Pesanan</span>
                <span class="font-bold text-zinc-900 uppercase">
                    {{ $order->order_type }}
                    @if($order->table_number)
                        <span class="text-zinc-500 ml-1">(Meja: {{ $order->table_number }})</span>
                    @endif
                </span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Pembayaran</span>
                <span
                    class="font-bold text-zinc-900 uppercase">{{ $order->payment_method === 'cash' ? 'Manual/Tunai' : 'Digital/Online' }}</span>
            </div>
            @if($order->notes)
                <div class="flex justify-between items-start text-sm pt-2 border-t border-zinc-50 mt-2">
                    <span class="text-zinc-500 shrink-0">Catatan</span>
                    <span class="font-bold text-zinc-900 text-right">{{ $order->notes }}</span>
                </div>
            @endif
        </div>

        {{-- Product List --}}
        <div class="bg-white rounded-3xl shadow-sm border border-zinc-100 p-5 mb-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 mb-4">Daftar Produk</h3>

            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-start gap-3">
                        <div class="flex gap-3">
                            <span class="font-black text-zinc-900 text-sm mt-0.5">{{ $item->quantity }}x</span>
                            <div>
                                <h4 class="font-bold text-zinc-900 text-sm">{{ $item->product_name }}</h4>
                                @if($item->variant_name)
                                    <p class="text-xs text-zinc-500 mt-0.5">{{ $item->variant_name }}</p>
                                @endif
                                @if($item->note)
                                    <p class="text-xs text-zinc-500 mt-0.5 italic">Catatan: {{ $item->note }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="font-black text-zinc-900 text-sm tabular-nums shrink-0">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Payment Summary --}}
        <div class="bg-white rounded-3xl shadow-sm border border-zinc-100 p-5 mb-6 space-y-3">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 mb-2">Ringkasan Pembayaran</h3>

            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Subtotal</span>
                <span
                    class="font-bold text-zinc-900 tabular-nums">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>

            @if(($order->service_charge_amount ?? 0) > 0)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-500">Biaya Layanan ({{ number_format($order->service_charge_percentage ?? 5) }}%)</span>
                    <span
                        class="font-bold text-zinc-900 tabular-nums">Rp {{ number_format($order->service_charge_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if(($order->tax_amount ?? 0) > 0)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-500">Pajak PB1 ({{ number_format($order->tax_percentage ?? 10) }}%)</span>
                    <span
                        class="font-bold text-zinc-900 tabular-nums">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="flex justify-between items-center pt-3 border-t border-zinc-100 mt-2">
                <span class="font-black text-zinc-900 uppercase tracking-widest text-sm">Total Tagihan</span>
                <span
                    class="font-black text-lg text-[var(--primary-color)] tabular-nums font-mono">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

    </main>

    {{-- Fixed Bottom Actions --}}
    <div
        class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-zinc-100 px-5 py-4 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
        <div class="max-w-xl mx-auto flex flex-col gap-2.5">
            <div class="flex gap-2">
                @if($this->getWaUrl())
                    <a href="{{ $this->getWaUrl() }}" target="_blank"
                       class="flex-1 bg-[#25D366] hover:bg-[#20b858] text-white rounded-2xl py-3.5 px-4 font-black uppercase tracking-widest text-xs flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md shadow-[#25D366]/20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="currentColor">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.489-1.761-1.663-2.06-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        Hubungi Resto
                    </a>
                @endif
                <a href="{{ route('invoice.show', $order->invoice_code) }}" target="_blank"
                   class="flex-[0.8] bg-zinc-900 hover:bg-zinc-800 text-white rounded-2xl py-3.5 px-4 font-black uppercase tracking-widest text-xs flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/>
                        <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                        <path d="M12 17.5v-11"/>
                    </svg>
                    Struk
                </a>
            </div>
        </div>
    </div>
</div>
