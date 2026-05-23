<div x-show="historyOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="historyOpen = false" class="fixed inset-0 bg-zinc-900/60 backdrop-blur-md z-[200] flex items-end sm:items-center justify-center p-0 sm:p-4" style="display: none;">
    <div @click.stop x-show="historyOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0" x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100" x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0" class="bg-zinc-50 w-full max-w-md rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl border border-zinc-200/50 flex flex-col max-h-[90vh] sm:max-h-[85vh] overflow-hidden">
        <div class="flex justify-center py-3 sm:hidden"><div class="w-12 h-1.5 bg-zinc-300 rounded-full"></div></div>

        <div class="px-6 pb-4 pt-2 sm:pt-6 border-b border-zinc-100 flex items-center justify-between shrink-0">
            <div>
                <h2 class="text-lg font-black text-zinc-900 leading-none">Riwayat Belanja</h2>
                <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider mt-1.5">Toko Online Kamu</p>
            </div>
            <button @click="historyOpen = false" class="p-2 bg-white hover:bg-zinc-100 border border-zinc-200/50 hover:border-zinc-300 text-zinc-500 hover:text-zinc-800 transition-all rounded-xl active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
            <template x-if="orderHistory.length === 0 && !historyLoading">
                <div class="py-12 px-4 text-center flex flex-col items-center justify-center">
                    <div class="w-20 h-20 rounded-[2rem] bg-zinc-100 border border-zinc-200/40 flex items-center justify-center mb-5 rotate-6 hover:rotate-0 transition-transform duration-300 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h3 class="text-sm font-black text-zinc-900 mb-1">Belum Ada Transaksi</h3>
                    <p class="text-[11px] text-zinc-400 max-w-[240px] leading-relaxed font-medium mb-6">
                        Setiap belanjaan yang kamu pesan akan otomatis tercatat di sini.
                    </p>
                    <button @click="historyOpen = false" class="px-5 py-3 bg-zinc-900 hover:bg-zinc-800 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-zinc-900/20 active:scale-95 transition-all">
                        Mulai Belanja
                    </button>
                </div>
            </template>

            <template x-if="historyLoading">
                <div class="py-12 text-center flex flex-col items-center justify-center">
                    <svg class="animate-spin w-8 h-8 text-[var(--primary-color)] mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Memuat Terbaru...</p>
                </div>
            </template>

            <template x-if="orderHistory.length > 0 && !historyLoading">
                <div class="space-y-4 pr-0.5">
                    <template x-for="order in orderHistory" :key="order.invoiceCode">
                        <div class="bg-white rounded-2xl p-4 border border-zinc-100 shadow-sm shadow-zinc-100/40 hover:shadow-md hover:border-[var(--primary-color)]/30 hover:scale-[1.01] transition-all duration-300 flex flex-col gap-3 group">
                            <div class="flex items-start justify-between pb-3 border-b border-zinc-50">
                                <div>
                                    <span class="text-xs font-black tracking-tight text-zinc-900 uppercase block" x-text="order.invoiceCode"></span>
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wide mt-1 block" x-text="new Date(order.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false}).replace('.', ':')"></span>
                                </div>
                                <div class="flex flex-col gap-1.5 items-end shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-wider shadow-sm" :class="{'bg-amber-50 text-amber-600 border border-amber-200/60': !order.status || order.status === 'pending', 'bg-sky-50 text-sky-600 border border-sky-200/60': order.status === 'paid', 'bg-blue-50 text-blue-600 border border-blue-200/60': order.status === 'progress', 'bg-emerald-50 text-emerald-600 border border-emerald-200/60': order.status === 'completed', 'bg-rose-50 text-rose-600 border border-rose-200/60': order.status === 'cancelled'}" x-text="order.status === 'paid' ? 'Menunggu Disiapkan' : (order.status === 'progress' ? 'Sedang Diproses' : (order.status === 'completed' ? 'Selesai' : (order.status === 'cancelled' ? 'Batal' : 'Menunggu Pembayaran')))"></span>
                                    <div class="flex items-center gap-1">
                                        <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-wider" :class="{'bg-amber-50 text-amber-600 border border-amber-100/50': order.orderType === 'takeaway', 'bg-emerald-50 text-emerald-600 border border-emerald-100/50': order.orderType === 'delivery'}" x-text="order.orderType === 'takeaway' ? 'Ambil Sendiri' : 'Dikirim'"></span>
                                    </div>
                                    <span class="text-[8px] text-zinc-400 font-bold uppercase tracking-widest" x-text="order.paymentName"></span>
                                </div>
                            </div>
                            <div class="space-y-1 py-1">
                                <template x-for="item in order.items">
                                    <div class="flex items-center justify-between text-[11px] font-semibold text-zinc-500">
                                        <div class="flex items-center gap-1.5 truncate max-w-[240px]">
                                            <span class="text-zinc-400 font-bold" x-text="item.qty + 'x'"></span>
                                            <span class="truncate" x-text="item.name"></span>
                                        </div>
                                        <span class="text-zinc-600 shrink-0 font-bold" x-text="formatPrice(item.price * item.qty)"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-zinc-50">
                                <div>
                                    <span class="text-[9px] text-zinc-400 font-bold uppercase tracking-wider block">Total Belanja</span>
                                    <span class="text-sm font-extrabold text-zinc-900 tracking-tight" x-text="order.total"></span>
                                </div>
                                <a :href="'/invoice/' + order.invoiceCode" target="_blank" class="px-3.5 py-2 bg-zinc-50 group-hover:bg-[var(--primary-color)] text-zinc-800 group-hover:text-white rounded-xl text-[10px] font-black uppercase tracking-wider border border-zinc-100 group-hover:border-[var(--primary-color)] transition-all duration-300 flex items-center gap-1 active:scale-95 shadow-sm">
                                    <span>Detail</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="transform group-hover:translate-x-0.5 transition-transform"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="px-6 py-4 bg-zinc-100/50 border-t border-zinc-100 flex items-center justify-between shrink-0">
            <template x-if="orderHistory.length > 0">
                <button @click="clearHistory()" class="py-2.5 px-3.5 bg-red-50/80 hover:bg-red-50 text-red-600 hover:text-red-700 transition-all rounded-xl text-[10px] font-black uppercase tracking-wider flex items-center gap-1 border border-red-100/40">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                    <span>Bersihkan</span>
                </button>
            </template>
            <div x-show="orderHistory.length === 0"></div>
            <button @click="historyOpen = false" class="py-3 px-6 bg-zinc-950 hover:bg-zinc-800 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-zinc-950/15 active:scale-95 transition-all ml-auto">
                Tutup
            </button>
        </div>
    </div>
</div>
