<div class="flex h-full min-h-[640px] flex-col overflow-hidden rounded-[2rem] border border-emerald-800/15 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div x-show="isEditingOrder" class="border-b border-emerald-800/10 bg-emerald-50 px-4 py-3 text-center text-xs font-black text-emerald-800 dark:border-slate-800 dark:bg-emerald-500/10 dark:text-emerald-400" style="display: none;">
        Menambah ke <span x-text="editInvoiceCode"></span> (<span x-text="customerName || tableNumber"></span>)
        <button type="button" class="float-end text-slate-500" @click="isEditingOrder = false; @this.cancelEditOrder(); currentTab = 'queue';" title="Batal Edit">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="flex items-center justify-between border-b border-emerald-800/10 px-4 py-4 dark:border-slate-800">
        <button @click="isMobileCartOpen = false" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 lg:hidden">
            <i class="bi bi-arrow-left text-lg"></i>
        </button>
        <div>
            <div class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Receipt</div>
            <h5 class="mb-0 text-lg font-black text-slate-950 dark:text-white">Pesanan</h5>
        </div>
        <button @click="clearCart" class="flex h-10 w-10 items-center justify-center rounded-full border border-red-200 bg-red-50 text-red-600 shadow-sm transition hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400" x-show="cart.length > 0" title="Bersihkan">
            <i class="bi bi-trash3"></i>
        </button>
    </div>

    <div class="border-b border-emerald-800/10 p-4 dark:border-slate-800">
        <div class="mb-3 grid grid-cols-3 gap-2">
            @foreach($orderTypes as $type)
                <button @click="orderType = '{{ $type['id'] }}'; if('{{ $type['id'] }}' !== 'dinein') tableNumber = ''"
                        class="rounded-full border px-3 py-2 text-xs font-black transition"
                        :class="orderType === '{{ $type['id'] }}' ? 'border-emerald-800 bg-emerald-800 text-white dark:border-emerald-400 dark:bg-emerald-400 dark:text-slate-950' : 'border-emerald-800/20 bg-white text-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400'"
                        :disabled="isEditingOrder">
                    {{ $type['label'] }}
                </button>
            @endforeach
        </div>

        <div class="grid gap-2" :class="orderType === 'dinein' ? 'grid-cols-2' : 'grid-cols-1'">
            <input type="text" class="rounded-2xl border border-emerald-800/30 bg-white px-4 py-3 text-sm font-bold text-slate-900 outline-none focus:border-emerald-800 dark:border-slate-700 dark:bg-slate-950 dark:text-white" x-model="customerName" placeholder="Nama Pelanggan" :disabled="isEditingOrder">
            <input type="text" class="rounded-2xl border border-emerald-800/30 bg-white px-4 py-3 text-sm font-bold text-slate-900 outline-none focus:border-emerald-800 dark:border-slate-700 dark:bg-slate-950 dark:text-white" x-show="orderType === 'dinein'" x-model="tableNumber" placeholder="Meja" :disabled="isEditingOrder">
        </div>
    </div>

    <div id="tour-cart-items" class="min-h-0 flex-1 overflow-y-auto bg-[#fbfaf5] p-4 dark:bg-slate-950/60">
        <div x-show="cart.length === 0" style="display: none;">
            <div class="flex h-full min-h-[280px] flex-col items-center justify-center text-center text-slate-400">
                <i class="bi bi-bag-dash mb-3 text-5xl"></i>
                <p class="mb-0 text-sm font-black">Keranjang Kosong</p>
                <small>Pilih menu untuk memulai</small>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <template x-for="(item, index) in cart" :key="index">
                <div class="rounded-3xl border border-emerald-800/20 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h6 class="mb-1 truncate text-sm font-black text-slate-950 dark:text-white" x-text="item.name"></h6>
                            <template x-if="item.variant_name">
                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-300" x-text="item.variant_name"></span>
                            </template>
                        </div>
                        <button @click="removeFromCart(index)" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-500 dark:bg-red-500/10">
                            <i class="bi bi-x text-lg"></i>
                        </button>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-sm font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                        <div class="flex items-center rounded-full border border-slate-200 bg-slate-50 p-1 dark:border-slate-700 dark:bg-slate-950">
                            <button @click="decreaseQty(index)" class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm dark:bg-slate-800 dark:text-slate-200"><i class="bi bi-dash"></i></button>
                            <span class="px-3 text-sm font-black text-slate-900 dark:text-white" x-text="item.quantity"></span>
                            <button @click="increaseQty(index)" class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-800 text-white shadow-sm dark:bg-emerald-400 dark:text-slate-950" :disabled="item.quantity >= item.stock"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>

                    <input type="text" class="mt-3 w-full rounded-2xl border-0 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 outline-none dark:bg-slate-950 dark:text-slate-200" x-model="item.note" placeholder="Catatan (opsional)...">
                </div>
            </template>
        </div>
    </div>

    <div class="border-t border-emerald-800/10 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
        <div x-show="stockError" class="mb-3 rounded-2xl bg-red-50 p-3 text-center text-xs font-black text-red-600 dark:bg-red-500/10 dark:text-red-400" x-text="stockError"></div>

        <div class="mb-3 rounded-3xl border border-emerald-800/10 bg-[#fbfaf5] p-4 dark:border-slate-800 dark:bg-slate-950">
            <div class="mb-2 flex justify-between text-sm">
                <span class="font-semibold text-slate-500 dark:text-slate-400">Subtotal</span>
                <span class="font-black text-slate-900 dark:text-white" x-text="'Rp ' + formatRupiah(subTotal)"></span>
            </div>
            <div class="mb-2 flex justify-between text-sm">
                <span class="font-semibold text-slate-500 dark:text-slate-400">Biaya Layanan (<span x-text="serviceChargeRate"></span>%)</span>
                <span class="font-black text-slate-900 dark:text-white" x-text="'Rp ' + formatRupiah(serviceChargeAmount)"></span>
            </div>
            <div class="mb-2 flex justify-between text-sm">
                <span class="font-semibold text-slate-500 dark:text-slate-400">Pajak PB1 (<span x-text="taxRate"></span>%)</span>
                <span class="font-black text-slate-900 dark:text-white" x-text="'Rp ' + formatRupiah(taxAmount)"></span>
            </div>
            <div class="my-3 border-t border-emerald-800/10 dark:border-slate-800"></div>
            <div class="flex justify-between">
                <span class="font-black text-slate-950 dark:text-white">Total</span>
                <span class="font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(subTotalWithCharges)"></span>
            </div>
        </div>

        <div class="grid gap-2" :class="isEditingOrder ? 'grid-cols-1' : 'grid-cols-2'">
            <button id="tour-resto-save" @click="submitNewOrder" class="rounded-2xl bg-amber-400 px-4 py-3 text-sm font-black text-slate-950 shadow-sm transition hover:bg-amber-300" :disabled="cart.length === 0 || stockError !== '' || isSubmitting">
                <span x-text="isSubmitting ? 'Memproses...' : (isEditingOrder ? 'Simpan Tambahan' : 'Simpan Bill')"></span>
            </button>
            <button id="tour-resto-pay" @click="openDirectPaymentModal" class="rounded-2xl bg-emerald-800 px-4 py-3 text-sm font-black text-white shadow-sm transition hover:bg-emerald-700 dark:bg-emerald-500 dark:text-slate-950" x-show="!isEditingOrder" :disabled="cart.length === 0 || stockError !== '' || isSubmitting">
                Bayar <span x-text="formatRupiah(subTotalWithCharges)"></span>
            </button>
        </div>
    </div>
</div>
