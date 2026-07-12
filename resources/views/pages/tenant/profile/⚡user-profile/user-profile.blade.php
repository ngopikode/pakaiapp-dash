<div class="max-w-6xl mx-auto pb-24 px-4 sm:px-6 lg:px-8 mt-6 sm:mt-10 font-sans">

    <!-- Page Header -->
    <div class="mb-10 pb-6 border-b border-slate-200 dark:border-slate-800">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Keamanan Akun</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lindungi akun bisnis kamu dari akses yang tidak sah dan kelola informasi personal.</p>
    </div>

    <!-- Alert Success -->
    @if($showSuccessMessage)
        <div class="mb-8 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-between" role="alert">
            <div class="flex items-center text-emerald-700 dark:text-emerald-400">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-semibold text-sm">Pengaturan berhasil diperbarui.</span>
            </div>
            <button type="button" wire:click="$set('showSuccessMessage', false)" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <div class="space-y-12 sm:space-y-16">

        {{-- ═══════════════════════════════════════════════════════════
             1. INFORMASI PROFIL
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Informasi Profil</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Perbarui nama dan alamat email kamu. Pastikan menggunakan nama asli.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8">
                <form wire:submit="updateProfileInformation" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] overflow-hidden">
                    <div class="p-5 sm:p-6 space-y-5">
                        <div class="flex flex-col sm:flex-row gap-5">
                            <div class="w-full">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                                <input type="text" wire:model="form.name" placeholder="Nama Lengkap" 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 transition-shadow">
                                @error('form.name') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-full">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Alamat Email</label>
                                <input type="email" wire:model="form.email" disabled 
                                       class="w-full bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-xl px-4 py-2.5 cursor-not-allowed">
                                <p class="text-[11px] text-slate-500 mt-1.5">Email utama tidak dapat diubah.</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 sm:px-6 py-4 bg-slate-50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors flex items-center justify-center">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Divider -->
        <hr class="border-slate-200 dark:border-slate-800 hidden md:block">

        {{-- ═══════════════════════════════════════════════════════════
             2. PASSWORD SECTION
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Password</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Gunakan password yang panjang, rumit dan unik untuk melindungi akun kamu.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8">
                <div x-data="{ openPass: false }" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] overflow-hidden">
                    
                    <!-- Overview State -->
                    <div x-show="!openPass" class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Password akun</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Terakhir diubah: belum pernah</p>
                        </div>
                        <button type="button" @click="openPass = true" class="w-full sm:w-auto px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-2">
                            Ubah Password
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"></path></svg>
                        </button>
                    </div>

                    <!-- Form State -->
                    <form wire:submit="updatePassword" x-show="openPass" style="display: none;">
                        <div class="p-5 sm:p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Password Saat Ini</label>
                                <input type="password" wire:model="form.current_password" placeholder="••••••••" 
                                       class="w-full md:max-w-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400">
                                @error('form.current_password') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
                                <input type="password" wire:model="form.password" placeholder="Minimal 8 karakter" 
                                       class="w-full md:max-w-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400">
                                @error('form.password') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password Baru</label>
                                <input type="password" wire:model="form.password_confirmation" placeholder="••••••••" 
                                       class="w-full md:max-w-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400">
                            </div>
                        </div>
                        <div class="px-5 sm:px-6 py-4 bg-slate-50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-end items-center gap-3">
                            <button type="button" @click="openPass = false" class="w-full sm:w-auto px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors flex items-center justify-center">
                                Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <hr class="border-slate-200 dark:border-slate-800 hidden md:block">

        {{-- ═══════════════════════════════════════════════════════════
             3. 2FA SECTION
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Autentikasi Dua Faktor (2FA)</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Amankan akun kamu lebih baik dengan verifikasi kode tambahan saat login.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">2FA belum aktif</h3>
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Direkomendasikan</span>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Selain password, login butuh kode 6 digit dari aplikasi authenticator.</p>
                    </div>
                    <button type="button" class="w-full sm:w-auto whitespace-nowrap px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-2">
                        Aktifkan 2FA
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"></path></svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <hr class="border-slate-200 dark:border-slate-800 hidden md:block">

        {{-- ═══════════════════════════════════════════════════════════
             4. TRUST DEVICE & SESSIONS
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="3" rx="2"></rect><line x1="8" x2="16" y1="21" y2="21"></line><line x1="12" x2="12" y1="17" y2="21"></line></svg>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Perangkat & Sesi</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Kelola perangkat yang sedang login dan percayai perangkat utama kamu.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8 space-y-5">
                
                <!-- Trust Device Banner -->
                <div class="bg-orange-50/50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 rounded-3xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-orange-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 12 2 2 4-4"></path></svg>
                        <div>
                            <h4 class="text-sm font-bold text-orange-900 dark:text-orange-400">Perangkat ini belum dipercaya</h4>
                            <p class="text-sm text-orange-700 dark:text-orange-300/80 mt-1">Percayai perangkat ini agar tidak perlu verifikasi tambahan di login berikutnya.</p>
                        </div>
                    </div>
                    <button type="button" class="w-full sm:w-auto whitespace-nowrap px-4 py-2 bg-white dark:bg-slate-800 border border-orange-200 dark:border-orange-500/30 text-orange-600 dark:text-orange-400 text-sm font-bold rounded-xl shadow-sm hover:bg-orange-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-2">
                        Percayai Perangkat
                    </button>
                </div>

                <!-- Sessions List -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800/60">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Sesi Aktif</h4>
                        <div class="flex items-start gap-4">
                            <div class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-600 dark:text-slate-400 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="3" rx="2"></rect><line x1="8" x2="16" y1="21" y2="21"></line><line x1="12" x2="12" y1="17" y2="21"></line></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">Windows 10/11 · Chrome</span>
                                    <span class="w-max px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wider">Perangkat Ini</span>
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    <span>Jakarta, Indonesia</span>
                                    <span class="mx-1">•</span>
                                    <span>IP: 103.111.12.34</span>
                                    <span class="mx-1">•</span>
                                    <span>Aktif baru saja</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 sm:px-6 py-4 bg-slate-50 dark:bg-slate-800/20 flex justify-end">
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                            Logout dari perangkat lain
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <hr class="border-slate-200 dark:border-slate-800 hidden md:block">

        {{-- ═══════════════════════════════════════════════════════════
             5. SECURITY LOGS
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Log Aktivitas Keamanan</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Pantau aktivitas login dan perubahan keamanan terbaru pada akunmu.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] overflow-hidden">
                    <div class="p-0">
                        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-800/60 flex items-start sm:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Berhasil Login</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Oleh: {{ auth()->user()->name }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 whitespace-nowrap">Baru saja</span>
                        </div>
                        <div class="px-5 sm:px-6 py-4 flex items-start sm:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Password Diperbarui</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Diubah dari perangkat ini</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 whitespace-nowrap">2 hari yang lalu</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <hr class="border-slate-200 dark:border-slate-800 hidden md:block">

        {{-- ═══════════════════════════════════════════════════════════
             6. DANGER ZONE
        ═══════════════════════════════════════════════════════════ --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
            <div class="md:col-span-4 lg:col-span-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2 bg-red-50 dark:bg-red-500/10 rounded-lg text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17h.01"></path></svg>
                    </span>
                    <h2 class="text-base font-bold text-red-600 dark:text-red-500">Zona Bahaya</h2>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-11 md:pl-0">Tindakan destruktif yang tidak dapat dibatalkan.</p>
            </div>
            
            <div class="md:col-span-8 lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 border border-red-200 dark:border-red-900/30 rounded-3xl shadow-[0_8px_30px_rgb(239,68,68,0.05)] p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Hapus Akun</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-lg">Ini akan menghapus seluruh data bisnis kamu secara permanen (produk, transaksi, dan laporan). Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <button type="button" class="w-full sm:w-auto whitespace-nowrap px-5 py-2.5 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/20 text-sm font-bold rounded-xl hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center">
                        Hapus Akun
                    </button>
                </div>
            </div>
        </section>

    </div>
    
    <!-- Extra spacing for mobile bottom navbar -->
    <div class="h-24 md:hidden"></div>
</div>
