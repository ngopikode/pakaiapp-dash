<div>
    <div class="fixed z-[1050] flex flex-col justify-end items-end pointer-events-none sm:bottom-0 sm:right-0 sm:p-6 max-sm:inset-0 max-sm:p-0"
         wire:ignore.self
         x-data="{ 
             isOpen: window.innerWidth >= 992, 
             showTooltip: window.innerWidth < 992, 
             isMobile: window.innerWidth < 576, 
             showScroll: false,
             x: 0, y: 0, startX: 0, startY: 0, isDragging: false, moved: false, isCustomPositioned: false,
             initDrag(e) {
                 if (this.isOpen) return;
                 this.isDragging = true;
                 this.moved = false;
                 let clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
                 let clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
                 const rect = this.$refs.btnContainer.getBoundingClientRect();
                 if (!this.isCustomPositioned) {
                     this.x = rect.left;
                     this.y = rect.top;
                     this.isCustomPositioned = true;
                 }
                 this.startX = clientX - this.x;
                 this.startY = clientY - this.y;
             },
             doDrag(e) {
                 if (!this.isDragging) return;
                 let clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
                 let clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
                 let newX = clientX - this.startX;
                 let newY = clientY - this.startY;
                 if (Math.abs(newX - this.x) > 3 || Math.abs(newY - this.y) > 3) {
                     this.moved = true;
                     this.showTooltip = false;
                 }
                 this.x = newX;
                 this.y = newY;
             },
             stopDrag() {
                 if (!this.isDragging) return;
                 this.isDragging = false;
                 if (!this.moved) return;
                 const btnRect = this.$refs.btn.getBoundingClientRect();
                 const screenWidth = window.innerWidth;
                 const screenHeight = window.innerHeight;
                 const btnCenterX = btnRect.left + btnRect.width / 2;
                 if (btnCenterX < screenWidth / 2) {
                     this.x = this.x - (btnRect.left - 16);
                 } else {
                     this.x = this.x + (screenWidth - 16 - btnRect.right);
                 }
                 if (btnRect.top < 16) {
                     this.y = this.y - (btnRect.top - 16);
                 } else if (btnRect.bottom > screenHeight - 16) {
                     this.y = this.y - (btnRect.bottom - (screenHeight - 16));
                 }
             },
             handleClick(e) {
                 if (this.moved) {
                     e.preventDefault();
                     e.stopPropagation();
                     return;
                 }
                 this.isOpen = !this.isOpen;
                 this.showTooltip = false;
             }
         }"
         @mousemove.window="doDrag"
         @mouseup.window="stopDrag"
         @touchmove.window="doDrag"
         @touchend.window="stopDrag"
         @resize.window="isMobile = window.innerWidth < 576"
         x-init="setTimeout(() => showTooltip = false, 8000)"
         x-effect="document.body.style.overflow = isOpen && isMobile ? 'hidden' : '';
                   if (isOpen) {
                       setTimeout(() => {
                           const c = document.getElementById('central-chat-messages-container');
                           if (c) {
                               c.scrollTop = c.scrollHeight;
                               showScroll = c.clientHeight > 100 && (c.scrollHeight - c.scrollTop - c.clientHeight) > 150;
                           }
                       }, 350);
                   } else {
                       showScroll = false;
                   }">

        <!-- Chat Window -->
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-10"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-10"
             class="flex flex-col overflow-hidden w-[400px] max-w-[100vw] sm:h-[550px] sm:max-h-[85vh] bg-white dark:bg-slate-900 pointer-events-auto"
             :class="isOpen && isMobile ? 'w-full h-full border-0 rounded-none' : 'shadow-2xl rounded-2xl mb-4 border border-slate-200 dark:border-slate-800'"
             style="display: none;">

            <!-- Header -->
            <div class="bg-slate-900 dark:bg-slate-950 text-white p-4 flex justify-between items-center shrink-0 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-500 text-white rounded-full flex items-center justify-center w-10 h-10 shadow-inner">
                        <i class="ph-fill ph-magic-wand text-xl"></i>
                    </div>
                    <div class="leading-tight">
                        <span class="font-bold block text-sm">Asisten Pakaiapp</span>
                        <span class="text-emerald-300 text-[11px] tracking-wide">Selalu siap membantu</span>
                    </div>
                </div>
                <button type="button" class="text-slate-400 hover:text-white p-1 transition-colors"
                        @click="isOpen = false" aria-label="Tutup">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
            </div>

            <!-- Messages Area -->
            <div class="flex-grow overflow-y-auto p-4 bg-slate-50 dark:bg-slate-900/50 scroll-smooth"
                 id="central-chat-messages-container"
                 @scroll.debounce.150ms="showScroll = $el.clientHeight > 100 && ($el.scrollHeight - $el.scrollTop - $el.clientHeight) > 150">
                <div class="text-center mb-6 mt-2">
                    <span class="px-3 py-1.5 rounded-full text-[10px] tracking-widest font-bold uppercase text-slate-500 dark:text-slate-400 bg-slate-200/50 dark:bg-slate-800/50">Ngobrol dengan AI Kami</span>
                </div>

                @foreach($messages as $msg)
                    @if($msg['role'] !== 'system')
                        <div class="flex mb-4 {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="p-3 shadow-sm text-[14px] leading-relaxed max-w-[85%] {{ $msg['role'] === 'user' ? 'bg-emerald-600 text-white rounded-2xl rounded-tr-sm' : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-100 dark:border-slate-700 rounded-2xl rounded-tl-sm markdown-content' }}">
                                @if($msg['role'] === 'assistant')
                                    {!! str($msg['content'])->markdown(['html_input' => 'allow']) !!}
                                    @if($loop->first && count($messages) === 1)
                                        <div class="mt-4 flex flex-wrap gap-2" x-data="{ clicked: false }" x-show="!clicked">
                                            <button @click="clicked = true" wire:click="sendQuickReply('Apa saja fiturnya?')" wire:loading.attr="disabled" class="px-3 py-1.5 rounded-full text-[11px] font-semibold border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">Apa saja fiturnya?</button>
                                            <button @click="clicked = true" wire:click="sendQuickReply('Berapa biayanya?')" wire:loading.attr="disabled" class="px-3 py-1.5 rounded-full text-[11px] font-semibold border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">Berapa biayanya?</button>
                                            <button @click="clicked = true" wire:click="sendQuickReply('Bagaimana cara daftarnya?')" wire:loading.attr="disabled" class="px-3 py-1.5 rounded-full text-[11px] font-semibold border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">Cara daftarnya?</button>
                                        </div>
                                    @endif
                                @else
                                    {{ $msg['content'] }}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Target for Loading state -->
                <div class="flex mb-4 justify-start hidden" wire:loading.class.remove="hidden"
                     wire:target="sendMessage, sendQuickReply">
                    <div class="p-3 shadow-sm bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-100 dark:border-slate-700 rounded-2xl rounded-tl-sm flex items-center gap-3 text-[14px] max-w-[85%]">
                        <span class="text-slate-400 italic">Sedang berpikir...</span>
                        <div class="w-4 h-4 border-2 border-slate-300 dark:border-slate-600 border-t-emerald-500 rounded-full animate-spin"></div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="border-t border-slate-100 dark:border-slate-800 p-4 shrink-0 relative bg-white dark:bg-slate-900">

                <!-- WhatsApp Fallback Button -->
                <div class="text-center mb-3 pr-[52px]">
                    <a href="https://wa.me/6285172441544" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 rounded-full border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors font-bold text-xs">
                        <i class="ph-fill ph-whatsapp-logo text-sm"></i> Hubungi Admin via WhatsApp
                    </a>
                </div>

                <!-- Scroll to Bottom Button -->
                <button type="button"
                        x-show="showScroll"
                        x-transition.opacity
                        @click="let c = document.getElementById('central-chat-messages-container'); c.scrollTo({ top: c.scrollHeight, behavior: 'smooth' })"
                        class="absolute w-9 h-9 bg-slate-900 dark:bg-slate-800 text-white rounded-full shadow-lg flex items-center justify-center border-2 border-white dark:border-slate-700 hover:bg-slate-800 transition-colors"
                        style="display: none; z-index: 20; bottom: calc(100% + 12px); right: 16px;">
                    <i class="ph-bold ph-caret-down"></i>
                </button>

                <form wire:submit="sendMessage" class="flex items-center gap-2 m-0">
                    <input type="text" class="flex-grow bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border-none rounded-full px-5 py-3 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all"
                           wire:model="userInput" placeholder="Tanya sesuatu..." autocomplete="off" required>
                    <button type="submit"
                            class="w-11 h-11 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full flex items-center justify-center shrink-0 transition-colors shadow-md disabled:opacity-50"
                            wire:loading.attr="disabled">
                        <div wire:loading.remove wire:target="sendMessage">
                            <i class="ph-fill ph-paper-plane-right text-lg"></i>
                        </div>
                        <div wire:loading wire:target="sendMessage" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    </button>
                </form>
                <div class="text-center mt-3 text-slate-400 dark:text-slate-500 text-[10px] opacity-80">
                    Asisten AI dapat membuat kesalahan. Harap periksa kembali informasi penting Anda.
                </div>
            </div>
        </div>

        <!-- Floating Button & CTA -->
        <div class="flex justify-end items-center gap-3 relative pointer-events-auto"
             x-ref="btnContainer"
             :style="(!isOpen && isCustomPositioned) ? `position: fixed; left: ${x}px; top: ${y}px; transition: ${isDragging ? 'none' : 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'}; z-index: 1050;` : 'transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);'"
             :class="isOpen && isMobile ? 'hidden' : ''">
            <!-- CTA Tooltip -->
            <div x-show="!isOpen && showTooltip"
                 x-transition.opacity.duration.500ms
                 class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 shadow-xl rounded-xl px-4 py-2.5 text-sm font-bold flex items-center gap-2 relative animate-bounce-slow">
                <span class="cursor-pointer text-emerald-600 dark:text-emerald-400" @click="isOpen = true; showTooltip = false">✨ Hai, butuh bantuan?</span>
                <button class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors flex items-center ml-1"
                        @click.stop="showTooltip = false" aria-label="Tutup tooltip">
                    <i class="ph-bold ph-x text-base"></i>
                </button>
                <!-- Segitiga penunjuk -->
                <div class="absolute w-3 h-3 bg-white dark:bg-slate-800 border-r border-t border-slate-200 dark:border-slate-700 -right-1.5 top-1/2 -translate-y-1/2 rotate-45"></div>
            </div>

            <button type="button"
                    x-ref="btn" style="touch-action: none;"
                    @mousedown="initDrag" @touchstart="initDrag" @click="handleClick"
                    class="w-14 h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-2xl flex items-center justify-center border-[3px] border-emerald-100 dark:border-emerald-900 transition-transform hover:scale-105 active:scale-95 cursor-grab active:cursor-grabbing">
                <i class="ph-fill ph-chat-teardrop-dots text-2xl" x-show="!isOpen"></i>
                <i class="ph-bold ph-x text-xl" x-show="isOpen" style="display: none;"></i>
            </button>
        </div>

        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('commit', ({component, commit, respond, succeed, fail}) => {
                    succeed(({snapshot, effect}) => {
                        const container = document.getElementById('central-chat-messages-container');
                        if (container) {
                            setTimeout(() => {
                                container.scrollTop = container.scrollHeight;
                            }, 50);
                        }
                    });
                });
            });
        </script>


    </div>
</div>