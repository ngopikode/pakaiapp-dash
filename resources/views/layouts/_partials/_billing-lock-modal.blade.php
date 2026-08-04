<div x-data="{ 
         isLocked: false,
         init() {
             window.addEventListener('show-billing-lock', () => {
                 this.isLocked = true;
             });
         }
     }"
     x-show="isLocked" x-cloak
     class="fixed inset-0 z-[99999] flex items-center justify-center bg-slate-900/90 backdrop-blur-md">
     
    <div class="relative w-full max-w-lg transform overflow-hidden rounded-[24px] bg-white text-center shadow-2xl dark:bg-slate-900 border border-red-500/20 m-4 p-8 sm:p-10">
        
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 ring-8 ring-red-50 dark:ring-red-900/10 mb-6">
            <i class="ph-bold ph-warning-circle text-5xl text-red-600 dark:text-red-400"></i>
        </div>
        
        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-3">
            Aplikasi Terkunci
        </h2>
        
        <p class="text-base text-slate-600 dark:text-slate-400 mb-8 max-w-sm mx-auto leading-relaxed">
            Saldo <strong class="text-slate-900 dark:text-slate-200">Deposit Billing (Pakaiapp)</strong> Anda telah habis. Transaksi baru dihentikan sementara. Silakan isi ulang saldo (Top Up) untuk melanjutkan berjualan.
        </p>
        
        <div class="flex flex-col gap-3">
            <a href="/wallet" wire:navigate 
               class="inline-flex w-full items-center justify-center rounded-2xl bg-red-600 px-6 py-4 text-base font-bold text-white shadow-md transition-all hover:bg-red-500 focus:outline-none focus:ring-4 focus:ring-red-500/20 active:scale-[0.98]">
                <i class="ph-bold ph-wallet mr-2 text-xl"></i> Isi Ulang Saldo (Top Up)
            </a>
            
            <button @click="window.location.reload()" 
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-100 px-6 py-4 text-sm font-bold text-slate-700 transition-all hover:bg-slate-200 focus:outline-none dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 active:scale-[0.98]">
                <i class="ph-bold ph-arrows-clockwise mr-2"></i> Saya sudah Top Up (Refresh)
            </button>
        </div>
        
        <div class="mt-6 text-xs font-medium text-slate-400 dark:text-slate-500">
            Powered by PakaiApp Billing System
        </div>
    </div>
</div>
