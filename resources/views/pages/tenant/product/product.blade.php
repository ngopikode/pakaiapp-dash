<x-layouts::app title="Menu & Produk">
    <livewire:pages::tenant.product.index/>
    <livewire:pages::tenant.product.category-modal/>

    <div x-data="{
        showForm: false,
        loading: false,

        open(productId) {
            this.showForm = true;
            this.loading = true;
            productId
                ? Livewire.dispatch('load-product', { productId })
                : Livewire.dispatch('init-new-form');
        },

        closeForm() {
            this.showForm = false;
            this.loading = false;
        }
    }"
    x-cloak
    @open-product-form.window="open($event.detail.productId)"
    @form-initialized.window="loading = false"
    @close-product-form.window="closeForm()">

        {{-- Backdrop --}}
        <div x-show="showForm" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeForm()"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40"></div>

        {{-- Drawer --}}
        <div x-show="showForm" x-cloak
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-full md:translate-y-0 md:translate-x-full"
             x-transition:enter-end="translate-y-0 md:translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 md:translate-x-0"
             x-transition:leave-end="translate-y-full md:translate-y-0 md:translate-x-full"
             class="fixed bottom-0 right-0 md:top-0 md:bottom-auto z-50 w-full md:w-[650px] h-[90dvh] md:h-screen bg-white dark:bg-slate-900 shadow-2xl rounded-t-2xl md:rounded-t-none flex flex-col border-l border-slate-200 dark:border-slate-800">

             <div class="relative flex min-h-0 flex-1 flex-col overflow-hidden">
                 {{-- Skeleton loading — layout sesuai form --}}
                 <div x-show="loading" x-cloak
                      class="absolute inset-0 z-10 flex flex-col bg-white dark:bg-slate-900 animate-pulse">

                     {{-- Header --}}
                     <div class="shrink-0 px-4 md:px-6 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                         <div class="space-y-2">
                             <div class="h-4 w-40 rounded bg-slate-200 dark:bg-slate-700"></div>
                             <div class="h-3 w-28 rounded bg-slate-100 dark:bg-slate-800"></div>
                         </div>
                         <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                     </div>

                     {{-- Tabs --}}
                     <div class="shrink-0 px-4 md:px-6 py-2 border-b border-slate-200 dark:border-slate-800 flex gap-2">
                         <div class="h-7 w-24 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                         <div class="h-7 w-28 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                         <div class="h-7 w-20 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                         <div class="h-7 w-24 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                     </div>

                     {{-- Body --}}
                     <div class="flex-1 overflow-hidden px-4 md:px-6 py-4">
                         <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                             <div class="md:col-span-2 space-y-4">
                                 <div class="space-y-2">
                                     <div class="h-3 w-24 rounded bg-slate-200 dark:bg-slate-700"></div>
                                     <div class="h-9 w-full rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                                 </div>
                                 <div class="space-y-2">
                                     <div class="h-3 w-20 rounded bg-slate-200 dark:bg-slate-700"></div>
                                     <div class="h-9 w-full rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                                 </div>
                                 <div class="space-y-2">
                                     <div class="h-3 w-16 rounded bg-slate-200 dark:bg-slate-700"></div>
                                     <div class="h-20 w-full rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                                 </div>
                                 <div class="flex items-center gap-3">
                                     <div class="h-6 w-11 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                                     <div class="h-3 w-16 rounded bg-slate-200 dark:bg-slate-700"></div>
                                 </div>
                             </div>
                             <div class="space-y-2">
                                 <div class="h-3 w-20 rounded bg-slate-200 dark:bg-slate-700 mx-auto"></div>
                                 <div class="h-48 w-48 max-w-full rounded-xl bg-slate-200 dark:bg-slate-700 mx-auto"></div>
                             </div>
                         </div>
                     </div>

                     {{-- Footer --}}
                     <div class="shrink-0 px-4 md:px-6 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                         <div class="h-9 w-28 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                     </div>
                 </div>

                 {{-- Form --}}
                 <div x-show="!loading" x-cloak class="flex min-h-0 flex-1 flex-col">
                     <livewire:pages::tenant.product.form/>
                 </div>
             </div>
        </div>
    </div>
</x-layouts::app>
