<div class="w-full">
    <!-- Tenant Logo -->
    <div class="mb-8 flex flex-col items-center">
        @if($this->settings && $this->settings->logo)
            <img src="{{ Storage::url($this->settings->logo) }}" alt="{{ $this->settings->name }}"
                 class="h-16 w-auto object-contain rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 p-1 mb-4">
        @else
            <div class="w-16 h-16 rounded-2xl bg-brand-accent/10 text-brand-accent flex items-center justify-center border border-brand-accent/20 mb-4 shadow-sm">
                <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        @endif
        <h2 class="text-2xl font-extrabold font-heading text-gray-900 dark:text-white tracking-tight mb-2 text-center">
            Atur Ulang Password
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium text-center">
            Silakan buat kata sandi baru untuk akun kasir Anda.
        </p>
    </div>

    @if ($isSuccess)
        <div class="flex items-center p-4 mb-6 text-sm text-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-2xl border border-emerald-200 dark:border-emerald-800" role="alert">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <div class="font-medium">{{ $statusMessage }}</div>
        </div>
        
        <div class="pt-2">
            <a href="{{ route('login') }}" class="w-full py-4 px-6 text-white font-bold bg-brand-accent hover:bg-brand-accent-dark rounded-2xl shadow-xl shadow-brand-accent/20 transition-all duration-300 flex justify-center items-center gap-2 group" wire:navigate.hover>
                Masuk Sekarang
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    @else
        <form wire:submit="resetPassword" class="space-y-5">
            <input type="hidden" wire:model="token">
            <input type="hidden" wire:model="email">

            <!-- Read-only Email display -->
            <div class="space-y-2">
                <label for="email-display" class="text-sm font-bold text-gray-700 dark:text-gray-300">Alamat Email</label>
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input type="text" id="email-display" value="{{ $email }}" disabled
                           class="block w-full pl-12 pr-4 py-4 bg-gray-100 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-800 rounded-2xl text-gray-500 dark:text-gray-400 sm:text-sm font-medium cursor-not-allowed">
                </div>
            </div>

            <!-- Password Input -->
            <div x-data="{ show: false }" class="space-y-2 group">
                <label for="password" class="text-sm font-bold text-gray-700 dark:text-gray-300">Password Baru</label>
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-brand-accent transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input wire:model="password" :type="show ? 'text' : 'password'" id="password" 
                           class="block w-full pl-12 pr-12 py-4 bg-gray-50/50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-brand-accent/15 focus:border-brand-accent focus:outline-none transition-all duration-300 sm:text-sm font-medium" 
                           placeholder="••••••••" required autofocus>
                    
                    <button type="button" @click="show = !show" class="absolute right-2 p-2 text-gray-400 hover:text-brand-accent transition-colors focus:outline-none rounded-lg">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password Input -->
            <div x-data="{ show: false }" class="space-y-2 group">
                <label for="password_confirmation" class="text-sm font-bold text-gray-700 dark:text-gray-300">Konfirmasi Password Baru</label>
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-brand-accent transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input wire:model="password_confirmation" :type="show ? 'text' : 'password'" id="password_confirmation" 
                           class="block w-full pl-12 pr-12 py-4 bg-gray-50/50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-brand-accent/15 focus:border-brand-accent focus:outline-none transition-all duration-300 sm:text-sm font-medium" 
                           placeholder="••••••••" required>
                    
                    <button type="button" @click="show = !show" class="absolute right-2 p-2 text-gray-400 hover:text-brand-accent transition-colors focus:outline-none rounded-lg">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            @error('email')
                <div class="flex items-center p-4 mb-4 text-sm text-red-800 bg-red-50 dark:bg-red-900/30 dark:text-red-400 rounded-2xl border border-red-200 dark:border-red-800" role="alert">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="font-medium">{{ $message }}</div>
                </div>
            @enderror

            <div class="pt-2">
                <button type="submit" 
                        wire:loading.attr="disabled" 
                        class="w-full py-4 px-6 text-white font-bold bg-brand-accent hover:bg-brand-accent-dark rounded-2xl shadow-xl shadow-brand-accent/20 transition-all duration-300 flex justify-center items-center gap-2 overflow-hidden group disabled:opacity-70 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="resetPassword" class="flex items-center gap-2">
                        Simpan Password Baru 
                        <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="resetPassword" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
