<div @if($order->status === 'pending') wire:poll.5s="refreshOrder" @endif class="mx-auto max-w-lg px-3 py-4">
    <div id="custom-toast" class="fixed left-1/2 top-5 z-[9999] hidden -translate-x-1/2 rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-bold text-white shadow-lg transition-opacity duration-300">
        Tersalin!
    </div>

    <div class="no-print mb-4 flex flex-nowrap items-center justify-center gap-2">
        <a href="{{ url('/receipt/' . $order->invoice_code) }}"
           class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-slate-900 px-3 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-slate-800">
            <i class="ph-bold ph-printer"></i> Lihat Struk
        </a>
        <a href="https://wa.me/?text={{ urlencode(url()->current()) }}" target="_blank"
           class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-emerald-700">
            <i class="ph-bold ph-whatsapp-logo"></i> Bagikan
        </a>
    </div>

    @if($order->status === 'pending' && ($order->duitku_va_number || $order->duitku_payment_url))
        @php
            $duitkuDetails = $this->getPaymentMethodDetails();
            $instructions = $this->getPaymentInstructions();
        @endphp
        <div class="no-print mx-auto mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" x-data="{ open: null }">
            <div class="mb-3 flex items-center justify-between">
                <span class="animate-pulse rounded-md bg-amber-100 px-2.5 py-1 text-[0.65rem] font-bold uppercase tracking-wider text-amber-700">Menunggu Pembayaran</span>
                <span class="flex items-center gap-1 text-[0.7rem] text-slate-400">
                    <i class="ph-bold ph-clock"></i> Cek otomatis...
                </span>
            </div>

            @if(config('duitku.sandbox'))
                <div class="mb-3 flex gap-2.5 rounded-xl border-l-4 border-amber-400 bg-amber-50 p-3">
                    <i class="ph-fill ph-warning mt-0.5 shrink-0 text-lg text-amber-500"></i>
                    <div>
                        <p class="mb-0 text-xs font-bold text-amber-800">Mode Uji Coba (Sandbox)</p>
                        <p class="mt-0.5 text-[0.65rem] leading-tight text-amber-700">
                            Website ini sedang dalam tahap uji coba pembayaran. Jangan gunakan kartu kredit atau rekening asli.
                        </p>
                    </div>
                </div>
            @endif

            <div class="mb-4 flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                <img src="{{ $duitkuDetails['logo'] }}" alt="{{ $duitkuDetails['name'] }}"
                     class="rounded bg-white shadow-sm" style="width: 55px; height: auto; padding: 2px;">
                <div>
                    <p class="text-[0.8rem] font-bold text-slate-900">{{ $duitkuDetails['name'] }}</p>
                    <p class="text-[0.7rem] text-slate-400">Pembayaran digital via Duitku</p>
                </div>
            </div>

            <div class="mb-4 overflow-hidden rounded-xl bg-slate-900 py-3 text-center text-white">
                <p class="mb-1 text-[0.65rem] font-bold uppercase tracking-wider text-zinc-400">Total Tagihan</p>
                <p class="mb-2 font-black font-mono text-amber-400" style="font-size: 1.45rem;">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </p>
                <button onclick="copyToClipboard('{{ $order->total_price }}', 'Nominal Berhasil Disalin!')"
                        class="rounded-full border border-zinc-600 bg-transparent px-3 py-1 text-[0.65rem] font-bold text-white transition-colors hover:bg-zinc-800">
                    <i class="ph-bold ph-clipboard mr-1"></i> Salin Nominal
                </button>
            </div>

            @if($order->duitku_va_number)
                <div class="mb-4">
                    <label class="mb-1.5 text-[0.65rem] font-bold uppercase tracking-wider text-slate-400">Nomor Virtual Account</label>
                    <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <span class="font-bold tracking-widest text-slate-900 font-mono">{{ $order->duitku_va_number }}</span>
                        <button onclick="copyToClipboard('{{ $order->duitku_va_number }}', 'Nomor VA Berhasil Disalin!')"
                                class="shrink-0 flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm">
                            <i class="ph-bold ph-clipboard"></i> Salin
                        </button>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 text-[0.65rem] font-bold uppercase tracking-wider text-slate-400">Petunjuk Pembayaran</label>
                    @foreach($instructions as $title => $steps)
                        @php $slug = \Illuminate\Support\Str::slug($title); @endphp
                        <div class="mb-2 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                            <button @click="open === '{{ $slug }}' ? open = null : open = '{{ $slug }}'"
                                    class="flex w-full items-center justify-between px-3 py-3 text-left text-[0.8rem] font-bold text-slate-900">
                                {{ $title }}
                                <i class="ph-bold ph-caret-down shrink-0 transition-transform duration-200"
                                   :class="open === '{{ $slug }}' ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open === '{{ $slug }}'" x-collapse>
                                <div class="border-t border-slate-100 bg-slate-50 px-3 py-3 text-[0.7rem] text-slate-500">
                                    <ol class="list-inside list-decimal space-y-2">
                                        @foreach($steps as $step)
                                            <li>{!! $step !!}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-2">
                    <p class="mb-4 text-[0.7rem] leading-relaxed text-slate-400">
                        Silakan klik tombol di bawah ini untuk memproses pembayaran digital Anda via portal pembayaran aman Duitku.
                    </p>
                    <a href="{{ $order->duitku_payment_url }}" target="_blank"
                       class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-sm transition-colors hover:bg-slate-800">
                        <i class="ph-bold ph-wallet"></i> Bayar Sekarang via Duitku
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if($order->status === 'pending' && $order->midtrans_snap_token)
        <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

        <div class="no-print mx-auto mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <span class="animate-pulse rounded-md bg-amber-100 px-2.5 py-1 text-[0.65rem] font-bold uppercase tracking-wider text-amber-700">Menunggu Pembayaran</span>
                <span class="flex items-center gap-1 text-[0.7rem] text-slate-400">
                    <i class="ph-bold ph-clock"></i> Cek otomatis...
                </span>
            </div>

            <div class="mb-4 flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                <div class="flex h-[40px] w-[55px] shrink-0 items-center justify-center rounded bg-white shadow-sm">
                    <i class="ph-fill ph-credit-card text-xl text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-[0.8rem] font-bold text-slate-900">Pembayaran Digital</p>
                    <p class="text-[0.7rem] text-slate-400">Midtrans (QRIS, VA, E-Wallet)</p>
                </div>
            </div>

            <div class="mb-4 overflow-hidden rounded-xl bg-slate-900 py-3 text-center text-white">
                <p class="mb-1 text-[0.65rem] font-bold uppercase tracking-wider text-zinc-400">Total Tagihan</p>
                <p class="mb-2 font-black font-mono text-amber-400" style="font-size: 1.45rem;">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </p>
                <button onclick="copyToClipboard('{{ $order->total_price }}', 'Nominal Berhasil Disalin!')"
                        class="rounded-full border border-zinc-600 bg-transparent px-3 py-1 text-[0.65rem] font-bold text-white transition-colors hover:bg-zinc-800">
                    <i class="ph-bold ph-clipboard mr-1"></i> Salin Nominal
                </button>
            </div>

            <div class="text-center py-2">
                <p class="mb-4 text-[0.7rem] leading-relaxed text-slate-400">
                    Jika popup pembayaran sebelumnya tertutup, Anda dapat melanjutkannya dengan menekan tombol di bawah.
                </p>
                <button onclick="window.snap.pay('{{ $order->midtrans_snap_token }}', { onSuccess: function(){ location.reload() } })"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-sm transition-colors hover:bg-emerald-700">
                    <i class="ph-bold ph-credit-card"></i> Lanjutkan Pembayaran
                </button>
            </div>
        </div>
    @endif

    @if(($store->store_type ?? 'resto') === 'resto')
        <div id="back-to-menu-btn" class="no-print mx-auto mb-4 max-w-lg">
            <a href="{{ url('/') }}"
               class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition-colors hover:bg-slate-50">
                <i class="ph-bold ph-house"></i> Kembali ke Menu Utama
            </a>
        </div>
        <script>
            if (window.self !== window.top) {
                document.getElementById('back-to-menu-btn')?.remove();
            }
        </script>
    @endif

    @if($order->status !== 'pending')
        <div class="no-print mx-auto mb-4 max-w-lg rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm">
            <div class="mx-auto mb-3 flex h-[60px] w-[60px] items-center justify-center rounded-full bg-emerald-50">
                <i class="ph-fill ph-check-circle text-3xl text-emerald-500"></i>
            </div>
            <h5 class="mb-1 text-lg font-bold text-slate-900">
                Pesanan {{ $order->status === 'paid' ? 'Dibayar' : ($order->status === 'completed' ? 'Selesai' : 'Diproses') }}
            </h5>
            <p class="mb-3 text-[0.8rem] text-slate-500">
                @if($order->status === 'paid') Pembayaran sukses! Pesanan sedang disiapkan.
                @elseif($order->status === 'progress') Pesanan sedang diproses.
                @elseif($order->status === 'completed') Pesanan selesai. Terima kasih!
                @elseif($order->status === 'cancelled') Pesanan dibatalkan.
                    @if($order->cancellation_note)<br><span class="mt-1 block text-xs font-bold text-red-600">Alasan: {{ $order->cancellation_note }}</span>@endif
                @endif
            </p>
            @if(in_array($order->status, ['paid', 'completed', 'progress']))
                <a href="{{ url('/receipt/' . $order->invoice_code) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-slate-800">
                    <i class="ph-bold ph-receipt"></i> Lihat Struk Pembayaran
                </a>
            @endif
        </div>
    @endif
</div>

@assets
<script>
    function copyToClipboard(text, successMessage) {
        navigator.clipboard.writeText(text).then(function () {
            const toast = document.getElementById('custom-toast');
            toast.textContent = successMessage;
            toast.classList.remove('hidden');
            toast.style.opacity = '1';
            setTimeout(function () {
                toast.style.opacity = '0';
                setTimeout(function () {
                    toast.classList.add('hidden');
                }, 300);
            }, 2000);
        }).catch(function (err) {
            console.error('Gagal menyalin: ', err);
        });
    }
</script>
@endassets
