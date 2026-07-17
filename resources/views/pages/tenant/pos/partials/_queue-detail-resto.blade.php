<template x-if="selectedQueueOrder">
    <div class="flex h-full flex-col {{ $isSheet ? 'pb-5' : 'rounded-2xl border border-border bg-card shadow-sm overflow-hidden' }}">
    
        <!-- Header: X, Title, Status Pill -->
        <div class="flex items-center justify-between px-4 sm:px-5 py-4 border-b border-border/50">
            <div class="flex items-center gap-3 flex-1">
                <button type="button" @click="{{ $isSheet ? 'isMobileQueueDetailOpen = false' : 'isDesktopQueueDetailOpen = false' }}"
                        class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-accent hover:text-foreground text-muted-foreground transition-colors shrink-0 cursor-pointer -ml-1">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
                <h2 class="text-lg font-bold text-foreground">Detail Pesanan</h2>
            </div>
            
            <div class="flex items-center gap-1.5 rounded-full px-3 py-1 shrink-0 bg-opacity-20"
                 :class="getStatusColor(selectedQueueOrder.kStatus)">
                <i class="ph-bold text-[11px]" :class="getStatusIcon(selectedQueueOrder.kStatus)"></i>
                <span class="font-bold text-[11px] uppercase tracking-wider" x-text="getStatusLabel(selectedQueueOrder.kStatus)"></span>
            </div>
        </div>

        <!-- Order Meta Details -->
        <div class="px-3 sm:px-4 flex flex-col gap-2 mb-3 sm:mb-4">
            <div class="flex justify-between items-center text-xs">
                <span class="text-muted-foreground font-medium">Nama Pelanggan</span>
                <span class="font-bold text-foreground" x-text="selectedQueueOrder.customer_name"></span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-muted-foreground font-medium">No. Pesanan</span>
                <span class="font-bold text-foreground" x-text="'#' + selectedQueueOrder.invoice_code"></span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-muted-foreground font-medium">Waktu Pesan</span>
                <span class="font-bold text-foreground" x-text="selectedQueueOrder.created_at_human"></span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-muted-foreground font-medium">Tipe Pesanan</span>
                <span class="font-bold text-foreground" x-text="selectedQueueOrder.order_type === 'dinein' ? 'Makan di Tempat (Meja ' + (selectedQueueOrder.table_number || '-') + ')' : 'Bawa Pulang (Takeaway)'"></span>
            </div>
        </div>

        <div class="px-3 sm:px-4">
            <div class="border-t border-dashed border-border/70"></div>
        </div>

        <!-- Items List -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-4 custom-scrollbar relative">
            <h3 class="text-sm font-bold text-foreground mb-3">
                Daftar Menu <span class="text-muted-foreground font-normal text-xs" x-text="'(' + selectedQueueOrder.items.length + ')'"></span>
            </h3>

            <div class="flex flex-col gap-3">
                <template x-for="item in selectedQueueOrder.items" :key="item.id">
                    <div class="flex justify-between items-start text-[13px]">
                        <div class="flex gap-2">
                            <span class="font-bold text-primary" x-text="item.quantity + 'x'"></span>
                            <div class="flex flex-col">
                                <span class="text-foreground font-medium" x-text="item.product_name"></span>
                                <template x-if="item.variant_name">
                                    <span class="text-[11px] text-muted-foreground mt-0.5" x-text="item.variant_name"></span>
                                </template>
                                <template x-if="item.notes">
                                    <span class="text-[11px] text-orange-600 dark:text-orange-400 mt-0.5 flex items-center gap-1">
                                        <i class="ph-bold ph-warning-circle"></i> <span x-text="item.notes"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3 shrink-0 ml-2">
                            <span class="font-bold text-foreground" x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                            <template x-if="selectedQueueOrder.amount_paid == 0 && !['processing', 'ready', 'completed'].includes(item.kitchen_status)">
                                <button type="button" @click="$dispatch('open-void-item-modal', { itemId: item.id })"
                                        class="flex h-5 w-5 items-center justify-center rounded-full bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors cursor-pointer -mt-0.5"
                                        title="Batal (Void) Item">
                                    <i class="ph-bold ph-x text-[9px]"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            
            <template x-if="selectedQueueOrder.notes">
                <div class="mt-4 p-3 rounded-xl bg-accent text-accent-foreground text-xs flex gap-2 shadow-sm">
                    <i class="ph-bold ph-article mt-0.5 opacity-70"></i>
                    <div class="flex flex-col">
                        <span class="font-bold text-[10px] uppercase tracking-wider opacity-70 mb-0.5">Catatan Pesanan</span>
                        <span x-text="selectedQueueOrder.notes" class="leading-relaxed"></span>
                    </div>
                </div>
            </template>
        </div>

        <div class="px-3 sm:px-4">
            <div class="border-t border-dashed border-border/70"></div>
        </div>

        <!-- Payment Summary & Actions -->
        <div class="p-3 sm:p-4 pt-3 bg-card {{ $isSheet ? '' : 'rounded-b-2xl' }} relative overflow-hidden">
            <!-- PAID Watermark (if paid) -->
            <template x-if="selectedQueueOrder.amount_paid >= selectedQueueOrder.total_price">
                <div class="absolute right-2 top-2 -rotate-12 pointer-events-none opacity-20 z-0">
                    <div class="border-2 border-emerald-500 text-emerald-500 rounded-full px-4 py-1 font-black text-xl tracking-widest border-dashed inline-block">
                        LUNAS
                    </div>
                </div>
            </template>

            <h3 class="text-sm font-bold text-foreground mb-3 relative z-10">Rincian Pembayaran</h3>
            
            <div class="flex justify-between items-center text-xs mb-2 relative z-10">
                <span class="text-muted-foreground font-medium">Subtotal</span>
                <span class="font-bold text-foreground" x-text="'Rp ' + formatRupiah(selectedQueueOrder.subtotal)"></span>
            </div>
            <template x-if="selectedQueueOrder.total_price > selectedQueueOrder.subtotal">
                <div class="flex justify-between items-center text-xs mb-2 relative z-10">
                    <span class="text-muted-foreground font-medium">Pajak & Biaya</span>
                    <span class="font-bold text-foreground" x-text="'Rp ' + formatRupiah(selectedQueueOrder.total_price - selectedQueueOrder.subtotal)"></span>
                </div>
            </template>
            
            <div class="flex justify-between items-center mt-3 mb-4 relative z-10">
                <span class="text-sm font-bold text-foreground">Total Tagihan</span>
                <span class="text-base font-bold text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(selectedQueueOrder.total_price)"></span>
            </div>
            
            <!-- Actions Grid -->
            <template x-if="selectedQueueOrder.kitchen_status !== 'completed' && selectedQueueOrder.amount_paid < selectedQueueOrder.total_price">
                <div class="grid grid-cols-2 gap-2 relative z-10">
                    <!-- Edit Button -->
                    <button type="button" 
                            @click="$wire.setEditOrder(selectedQueueOrder.id)"
                            class="flex items-center justify-center gap-1.5 rounded-xl border border-border bg-card py-2 text-xs font-bold text-foreground hover:bg-accent transition-colors shadow-sm cursor-pointer">
                        <i class="ph-bold ph-pencil-simple text-sm"></i> Edit
                    </button>
                    
                    <!-- Pay Button -->
                    <button type="button" 
                            @click="openPayForOrder(selectedQueueOrder)"
                            class="flex items-center justify-center gap-1.5 rounded-xl bg-primary py-2 text-xs font-black text-primary-foreground hover:bg-primary/90 transition-colors shadow-sm cursor-pointer">
                        <i class="ph-bold ph-coins text-sm"></i> Bayar
                    </button>
                </div>
            </template>
            
            <template x-if="selectedQueueOrder.amount_paid == 0">
                <div class="grid grid-cols-2 gap-2 mt-2 relative z-10">
                    <button type="button" 
                            @click="openSplitModal({ id: selectedQueueOrder.id, invoice_code: selectedQueueOrder.invoice_code, items: selectedQueueOrder.items })"
                            class="flex items-center justify-center gap-1.5 rounded-xl border border-border bg-card py-2 text-[11px] font-bold text-foreground hover:bg-accent transition-colors shadow-sm cursor-pointer">
                        <i class="ph-bold ph-split-horizontal text-sm"></i> Pisah Bill
                    </button>
                    
                    <button type="button" 
                            @click="openMergeModal({ id: selectedQueueOrder.id, invoice_code: selectedQueueOrder.invoice_code })"
                            class="flex items-center justify-center gap-1.5 rounded-xl border border-border bg-card py-2 text-[11px] font-bold text-foreground hover:bg-accent transition-colors shadow-sm cursor-pointer">
                        <i class="ph-bold ph-intersect text-sm"></i> Gabung Bill
                    </button>
                </div>
            </template>
            
            <template x-if="selectedQueueOrder.can_cancel">
                <button type="button" 
                        @click="$dispatch('open-cancel-modal', { orderId: selectedQueueOrder.id })"
                        class="mt-2 w-full flex items-center justify-center gap-1.5 rounded-xl bg-destructive/10 py-2 text-xs font-bold text-destructive hover:bg-destructive/20 border border-destructive/20 transition-colors shadow-sm relative z-10 cursor-pointer">
                    <i class="ph-bold ph-trash text-sm"></i> Batalkan
                </button>
            </template>
        </div>
    </div>
</template>

<!-- Empty State -->
<div class="flex h-full flex-col items-center justify-center p-4 sm:p-6 text-center {{ $isSheet ? '' : 'rounded-2xl border border-border bg-card/50' }}" x-show="!selectedQueueOrder">
    <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-secondary shadow-inner border border-border/50">
        <i class="ph-bold ph-cursor-click text-2xl text-muted-foreground/50"></i>
    </div>
    <h3 class="mb-1 text-sm font-bold text-foreground">Pilih Pesanan</h3>
    <p class="text-xs text-muted-foreground">Silakan klik salah satu pesanan di antrean.</p>
</div>
