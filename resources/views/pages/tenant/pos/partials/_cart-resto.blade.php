@php($isSheet = $isSheet ?? false)
<div class="flex h-full min-h-0 flex-col overflow-hidden {{ $isSheet ? 'bg-white dark:bg-slate-900 lg:rounded-[2rem]' : 'rounded-[2rem] bg-white shadow-lg dark:bg-slate-900' }}">
    <div x-show="isEditingOrder" class="border-b border-emerald-800/10 bg-emerald-50 px-4 py-3 text-center text-xs font-black text-emerald-800 dark:border-slate-800 dark:bg-emerald-500/10 dark:text-emerald-400" style="display: none;">
        Menambah ke <span x-text="editInvoiceCode"></span> (<span x-text="customerName || tableNumber"></span>)
        <button type="button" class="float-end text-slate-500" @click="isEditingOrder = false; @this.cancelEditOrder(); currentTab = 'queue';" title="Batal Edit">
            <i class="ph-bold ph-x"></i>
        </button>
    </div>

        <div class="flex items-center justify-between px-4 py-2">
            <button @click="isMobileCartOpen = false" class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-800 text-white shadow-sm dark:bg-emerald-500 dark:text-slate-950 lg:hidden">
                <i class="ph-bold ph-caret-left text-lg"></i>
            </button>
            <div class="hidden h-10 w-10 lg:block"></div>
            <div class="flex-1 text-center">
                <h5 class="mb-0 text-base font-black text-slate-950 dark:text-white">Purchase Receipt</h5>
                <div class="text-xs font-bold text-slate-500">#<span x-text="editInvoiceCode || 'New Order'"></span></div>
            </div>
            <button @click="clearCart" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-red-50 hover:text-red-600 hover:border-red-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300" title="Bersihkan">
                <i class="ph-bold ph-trash text-lg"></i>
            </button>
        </div>

    <div class="px-4 pb-2">
        <div class="flex overflow-hidden rounded-full border border-emerald-800 bg-white p-0.5 dark:border-slate-700 dark:bg-slate-900">
            @foreach($orderTypes as $type)
                <button @click="orderType = '{{ $type['id'] }}'; if('{{ $type['id'] }}' !== 'dinein') tableNumber = ''"
                        class="flex-1 rounded-full py-1.5 text-xs font-bold transition"
                        :class="orderType === '{{ $type['id'] }}' ? 'bg-emerald-800 text-white shadow-sm dark:bg-emerald-500 dark:text-slate-950' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800'"
                        :disabled="isEditingOrder">
                    {{ $type['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="grid gap-2 px-4 pb-2" :class="orderType === 'dinein' ? 'grid-cols-2' : 'grid-cols-1'">
        <div>
            <label class="mb-1 block text-[10px] font-bold text-slate-500">Customer name</label>
            <input type="text" class="w-full rounded-2xl border border-emerald-800/50 bg-white px-3 py-2 text-xs font-bold text-slate-900 outline-none focus:border-emerald-800 dark:border-slate-700 dark:bg-slate-950 dark:text-white" x-model="customerName" placeholder="Nama Pelanggan" :disabled="isEditingOrder">
        </div>
        <div x-show="orderType === 'dinein'">
            <label class="mb-1 block text-[10px] font-bold text-slate-500">Table</label>
            <input type="text" class="w-full rounded-2xl border border-emerald-800/50 bg-white px-3 py-2 text-xs font-bold text-slate-900 outline-none focus:border-emerald-800 dark:border-slate-700 dark:bg-slate-950 dark:text-white" x-model="tableNumber" placeholder="Meja" :disabled="isEditingOrder">
        </div>
    </div>

    <div id="tour-cart-items" class="h-0 min-h-0 flex-1 overflow-y-auto bg-slate-50/50 p-4 dark:bg-slate-950/60">
        <div x-show="cart.length === 0" style="display: none;">
            <div class="flex h-full flex-col items-center justify-center text-center text-slate-400">
                <i class="ph-bold ph-shopping-bag mb-3 text-5xl opacity-50"></i>
                <p class="mb-0 text-sm font-black">Keranjang Kosong</p>
                <small>Pilih menu untuk memulai</small>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <template x-for="(item, index) in cart" :key="index">
                <div class="rounded-[1.5rem] bg-white p-3 shadow-sm border border-slate-100 dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex min-w-0 flex-1 items-center gap-3">
                            <template x-if="item.image_url">
                                <img :src="item.image_url" class="h-12 w-12 shrink-0 rounded-2xl object-cover border border-slate-100 dark:border-slate-800">
                            </template>
                            <template x-if="!item.image_url">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                                    <i class="ph-bold ph-image text-slate-300 dark:text-slate-600"></i>
                                </div>
                            </template>
                            <div class="min-w-0 flex-1">
                                <h6 class="mb-1 truncate text-sm font-black text-slate-950 dark:text-white" x-text="item.name"></h6>
                                <template x-if="item.variant_name">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-300" x-text="item.variant_name"></span>
                                </template>
                                <div class="mt-1">
                                    <span class="text-xs font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col items-end gap-2">
                            <button @click="removeFromCart(index)" class="text-slate-300 transition hover:text-red-500"><i class="ph-fill ph-x-circle text-lg"></i></button>
                            <div class="flex items-center rounded-full border border-slate-100 bg-slate-50 p-1 shadow-sm dark:border-slate-700 dark:bg-slate-950">
                                <button @click="decreaseQty(index)" class="flex h-6 w-6 items-center justify-center rounded-full text-slate-500 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800"><i class="ph-bold ph-minus"></i></button>
                                <span class="px-2 text-xs font-black text-slate-900 dark:text-white" x-text="item.quantity"></span>
                                <button @click="increaseQty(index)" class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-800 text-white shadow-sm dark:bg-emerald-400 dark:text-slate-950" :disabled="item.quantity >= item.stock"><i class="ph-bold ph-plus text-xs"></i></button>
                            </div>
                        </div>
                    </div>
                    <input type="text" class="mt-2 w-full rounded-full border-0 bg-slate-50 px-4 py-2 text-[11px] font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-emerald-800/10 dark:bg-slate-950 dark:text-slate-200" x-model="item.note" placeholder="Catatan (opsional)...">
                </div>
            </template>
        </div>
    </div>

    <div class="border-t border-slate-100 bg-white p-2 dark:border-slate-800 dark:bg-slate-900">
        <div x-show="stockError" class="mb-2 rounded-2xl bg-red-50 p-2 text-center text-[11px] font-black text-red-600 dark:bg-red-500/10 dark:text-red-400" x-text="stockError"></div>

        <div class="mb-2 rounded-[1.5rem] bg-slate-50 p-2 dark:bg-slate-950">
            <div class="mb-1 flex justify-between text-xs">
                <span class="font-semibold text-slate-500 dark:text-slate-400">Subtotal</span>
                <span class="font-black text-slate-900 dark:text-white" x-text="'Rp ' + formatRupiah(subTotal)"></span>
            </div>
            <div class="mb-1 flex justify-between text-xs">
                <span class="font-semibold text-slate-500 dark:text-slate-400">Biaya Layanan (<span x-text="serviceChargeRate"></span>%)</span>
                <span class="font-black text-slate-900 dark:text-white" x-text="'Rp ' + formatRupiah(serviceChargeAmount)"></span>
            </div>
            <div class="mb-1 flex justify-between text-xs">
                <span class="font-semibold text-slate-500 dark:text-slate-400">Pajak PB1 (<span x-text="taxRate"></span>%)</span>
                <span class="font-black text-slate-900 dark:text-white" x-text="'Rp ' + formatRupiah(taxAmount)"></span>
            </div>
            <div class="mb-1 flex justify-between text-xs" x-show="applicationFeeAmount > 0">
                <span class="font-semibold text-slate-500 dark:text-slate-400">Biaya Aplikasi</span>
                <span class="font-black text-slate-900 dark:text-white" x-text="'Rp ' + formatRupiah(applicationFeeAmount)"></span>
            </div>
            <div class="my-2 border-t border-slate-200 dark:border-slate-800"></div>
            <div class="flex items-center justify-between">
                <span class="font-black text-slate-950 dark:text-white">Total</span>
                <span class="text-base font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(subTotalWithCharges)"></span>
            </div>
        </div>

        <div class="grid gap-2" :class="isEditingOrder ? 'grid-cols-1' : 'grid-cols-2'">
            <button id="tour-resto-save" @click="submitNewOrder" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-black text-slate-950 shadow-sm transition hover:bg-amber-300" :disabled="cart.length === 0 || stockError !== '' || isSubmitting">
                <span x-text="isSubmitting ? 'Memproses...' : (isEditingOrder ? 'Simpan Tambahan' : 'Simpan Bill')"></span>
            </button>
            <button id="tour-resto-pay" @click="openDirectPaymentModal" class="flex items-center justify-center gap-2 rounded-full bg-emerald-800 px-4 py-2 text-sm font-black text-white shadow-sm transition hover:bg-emerald-700 dark:bg-emerald-500 dark:text-slate-950" x-show="!isEditingOrder" :disabled="cart.length === 0 || stockError !== '' || isSubmitting">
                <span>Place Order</span>
                <i class="ph-fill ph-arrow-circle-right text-lg opacity-80"></i>
            </button>
        </div>
    </div>
</div>
