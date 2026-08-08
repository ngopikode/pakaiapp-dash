<div class="relative w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 sm:mt-10 font-sans pb-28"
     x-data="{
        showMobileForm: false,
        form: { id: null, name: '', start_time: '', end_time: '', max_orders: 0, is_active: true },
        errors: {},
        isSaving: false,
        resetForm() {
            this.form = { id: null, name: '', start_time: '', end_time: '', max_orders: 0, is_active: true };
            this.errors = {};
            this.showMobileForm = true;
        },
        editSlot(slot) {
            this.form = {
                id: slot.id,
                name: slot.name,
                start_time: slot.start_time.substring(0,5),
                end_time: slot.end_time.substring(0,5),
                max_orders: parseInt(slot.max_orders),
                is_active: slot.is_active === 1 || slot.is_active === true
            };
            this.errors = {};
            this.showMobileForm = true;
        },
        async saveSlot() {
            this.errors = {};
            if (!this.form.name.trim()) this.errors.name = 'Nama slot harus diisi.';
            if (!/^(?:[01]\d|2[0-3]):[0-5]\d$/.test(this.form.start_time)) this.errors.start_time = 'Format HH:MM tidak valid.';
            if (!/^(?:[01]\d|2[0-3]):[0-5]\d$/.test(this.form.end_time)) this.errors.end_time = 'Format HH:MM tidak valid.';
            if (this.form.max_orders === '' || this.form.max_orders === null) this.errors.max_orders = 'Isi 0 jika tidak dibatasi.';
            if (Object.keys(this.errors).length > 0) return;

            window.showLoader();
            this.isSaving = true;
            await $wire.save(this.form);
            await $wire.$island('list').$refresh();
            
            this.isSaving = false;
            window.hideLoader();
            this.showMobileForm = false;
            this.form = { id: null, name: '', start_time: '', end_time: '', max_orders: 0, is_active: true };
        },
        confirmDelete(id) {
            const isDark = document.documentElement.classList.contains('dark');
            window.Swal.fire({
                title: 'Hapus Slot Waktu?',
                text: 'Pesanan yang telah memilih slot ini mungkin akan terpengaruh.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: isDark ? '#334155' : '#94a3b8',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                background: isDark ? '#1e293b' : '#ffffff',
                color: isDark ? '#ffffff' : '#0f172a'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    window.showLoader();
                    await $wire.delete(id);
                    await $wire.$island('list').$refresh();
                    window.hideLoader();
                }
            });
        }
            });
        }
     }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-500/20 text-orange-500 flex items-center justify-center shadow-sm">
                    <i class="ph-bold ph-clock text-xl"></i>
                </div>
                Kelola Slot Waktu Pengiriman
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Daftar jam keberangkatan pengiriman dan batas pesanan per slot.</p>
        </div>
        <a href="{{ route('store-setting') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shrink-0 w-max">
            <i class="ph-bold ph-arrow-left"></i>
            Kembali
        </a>
    </div>

    <!-- Main Content -->
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] md:shadow-xl md:border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col md:flex-row min-h-[65vh]">

        {{-- Left Panel: List (LIVEWIRE ISLAND) --}}
        <div class="w-full md:w-5/12 lg:w-1/3 flex flex-col border-b md:border-b-0 md:border-r border-slate-200 dark:border-slate-800 min-h-0 bg-transparent md:bg-slate-50/50 md:dark:bg-slate-900/50">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <button @click="resetForm()" class="w-full py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm hover:border-orange-500 hover:text-orange-500 transition-colors flex justify-center items-center gap-2">
                    <i class="ph-bold ph-plus"></i> Tambah Slot Baru
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @island(name: 'list', defer: true)
                    @placeholder
                        <div class="space-y-3">
                            @foreach([1,2,3] as $skeleton)
                                <div class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800/50 animate-pulse h-24 border border-slate-200 dark:border-slate-700"></div>
                            @endforeach
                        </div>
                    @endplaceholder

                    @forelse($this->slots as $slot)
                        <div class="p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl flex items-start justify-between gap-3 group hover:border-orange-500 transition-colors cursor-pointer mb-3"
                             :class="form.id == {{ $slot->id }} ? 'ring-2 ring-orange-500 border-orange-500 shadow-md' : 'shadow-sm'"
                             @click="editSlot({{ $slot->toJson() }})">
                            <div class="min-w-0 pointer-events-none">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate flex items-center gap-2">
                                    {{ $slot->name }}
                                    @if(!$slot->is_active)
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-[10px] text-slate-500 uppercase tracking-widest">Nonaktif</span>
                                    @endif
                                </h4>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                                    <i class="ph ph-clock text-slate-400"></i> {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }}
                                </p>
                                @if($slot->max_orders > 0)
                                    <p class="text-[10px] font-bold text-orange-600 dark:text-orange-400 mt-1 bg-orange-50 dark:bg-orange-500/10 px-2 py-0.5 rounded inline-block">
                                        Maks: {{ $slot->max_orders }} pesanan
                                    </p>
                                @else
                                    <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-1 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded inline-block">
                                        Tanpa batas kuota
                                    </p>
                                @endif
                            </div>
                            <div class="flex flex-col items-center gap-1 shrink-0 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                <button @click.stop="editSlot({{ $slot->toJson() }})" title="Edit" class="p-1.5 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors">
                                    <i class="ph-bold ph-pencil-simple text-lg"></i>
                                </button>
                                <button @click.stop="confirmDelete({{ $slot->id }})" title="Hapus" class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                    <i class="ph-bold ph-trash text-lg"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 px-4">
                            <div class="w-16 h-16 mx-auto bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-3">
                                <i class="ph ph-clock text-3xl text-slate-400 dark:text-slate-500"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum Ada Slot</p>
                            <p class="text-xs text-slate-500 mt-1">Tambahkan slot waktu pengiriman untuk opsi pre-order.</p>
                        </div>
                    @endforelse
                @endisland
            </div>
        </div>

        {{-- Right Panel: Form (Desktop) & Bottom Sheet (Mobile) --}}
        
        <!-- Mobile Overlay -->
        <div x-show="showMobileForm" x-transition.opacity.duration.300ms 
             class="md:hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100]" 
             style="display: none;" 
             @click="showMobileForm = false"></div>

        <div class="md:w-7/12 lg:w-2/3 flex flex-col bg-white dark:bg-slate-900 min-h-0
                    fixed md:relative bottom-0 left-0 right-0 z-[101] md:z-auto
                    h-[85vh] md:h-auto rounded-t-[2.5rem] md:rounded-none shadow-2xl md:shadow-none border-t md:border-none border-slate-200 dark:border-slate-800
                    transition-transform duration-300 ease-out transform"
             :class="showMobileForm ? 'translate-y-0' : 'translate-y-full md:translate-y-0'">
            
            <!-- Mobile Drag Handle -->
            <div class="md:hidden p-3 flex justify-center shrink-0" @click="showMobileForm = false">
                <div class="w-12 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 cursor-pointer"></div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white mb-1" x-text="form.id ? 'Edit Slot Waktu' : 'Tambah Slot Baru'"></h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Atur jam pengiriman dan batasan jumlah pesanan per slot.</p>
                    </div>
                    <button @click="showMobileForm = false" class="md:hidden p-2 text-slate-400 hover:text-slate-600 bg-slate-100 rounded-full">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Slot <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name" placeholder="Contoh: Pagi (06:00 - 09:00)" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all outline-none text-slate-800 dark:text-white">
                        <span x-show="errors.name" class="text-[11px] text-red-500 mt-1.5 block font-bold" x-text="errors.name" style="display:none;"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div x-data="{
                                formatTime(e) {
                                    let val = e.target.value.replace(/[^0-9]/g, '');
                                    if (val.length >= 3) val = val.substring(0,2) + ':' + val.substring(2,4);
                                    if (val.length > 5) val = val.substring(0,5);
                                    if (val.length === 5) {
                                        let [h, m] = val.split(':');
                                        if (parseInt(h) > 23) h = '23';
                                        if (parseInt(m) > 59) m = '59';
                                        val = h + ':' + m;
                                    }
                                    e.target.value = val;
                                    form.start_time = val;
                                }
                             }">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ph ph-clock text-slate-400"></i>
                                </div>
                                <input type="text" :value="form.start_time" @input="formatTime" placeholder="HH:MM" class="w-full font-mono bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold rounded-xl pl-10 pr-4 py-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all outline-none text-slate-800 dark:text-white">
                            </div>
                            <span x-show="errors.start_time" class="text-[11px] text-red-500 mt-1.5 block font-bold" x-text="errors.start_time" style="display:none;"></span>
                        </div>

                        <div x-data="{
                                formatTime(e) {
                                    let val = e.target.value.replace(/[^0-9]/g, '');
                                    if (val.length >= 3) val = val.substring(0,2) + ':' + val.substring(2,4);
                                    if (val.length > 5) val = val.substring(0,5);
                                    if (val.length === 5) {
                                        let [h, m] = val.split(':');
                                        if (parseInt(h) > 23) h = '23';
                                        if (parseInt(m) > 59) m = '59';
                                        val = h + ':' + m;
                                    }
                                    e.target.value = val;
                                    form.end_time = val;
                                }
                             }">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ph ph-clock text-slate-400"></i>
                                </div>
                                <input type="text" :value="form.end_time" @input="formatTime" placeholder="HH:MM" class="w-full font-mono bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold rounded-xl pl-10 pr-4 py-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all outline-none text-slate-800 dark:text-white">
                            </div>
                            <span x-show="errors.end_time" class="text-[11px] text-red-500 mt-1.5 block font-bold" x-text="errors.end_time" style="display:none;"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Batas Kuota Pesanan (Per Hari) <span class="text-red-500">*</span></label>
                        <input type="number" x-model.number="form.max_orders" placeholder="0" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all outline-none text-slate-800 dark:text-white">
                        <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">Isi dengan 0 jika kuota tidak dibatasi. Jika sudah penuh, pembeli tidak bisa memilih slot ini.</p>
                        <span x-show="errors.max_orders" class="text-[11px] text-red-500 mt-1.5 block font-bold" x-text="errors.max_orders" style="display:none;"></span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Status Aktif</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tampilkan slot ini di pilihan checkout pembeli.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-500 peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 shrink-0 pb-10 md:pb-6">
                <button @click="saveSlot()" :disabled="isSaving" class="w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-black rounded-xl shadow-lg shadow-orange-500/25 active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <span x-show="!isSaving">Simpan Pengaturan Slot</span>
                    <span x-show="isSaving" style="display:none;" class="flex items-center gap-2">
                        <i class="ph ph-spinner animate-spin"></i> Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
