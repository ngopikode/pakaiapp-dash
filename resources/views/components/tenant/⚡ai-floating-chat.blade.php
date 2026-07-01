<?php

use Livewire\Component;
use App\Tenant\Models\Ai\AiChatSession;
use App\Tenant\Services\OpenAiMenuService;
use Illuminate\Support\Str;

new class extends Component
{
    public $sessionId = null;
    public $messages = [];
    public $userInput = '';
    public $storeName = 'Asisten AI';

    public function mount()
    {
        try {
            $setting = \App\Tenant\Models\Core\StoreSetting::first();
            if ($setting && $setting->name) {
                $this->storeName = 'Asisten ' . $setting->name;
            }
        } catch (\Exception $e) {}

        $token = session()->get('ai_chat_session_token');
        
        if (!$token) {
            $token = Str::uuid()->toString();
            session()->put('ai_chat_session_token', $token);
        }

        $chatSession = AiChatSession::firstOrCreate(
            ['session_token' => $token],
            ['table_number' => session('table_number', '1'), 'turn_count' => 0]
        );

        $this->sessionId = $chatSession->id;
        
        $this->loadMessages();
            
        if (empty($this->messages)) {
            $shortName = str_replace('Asisten ', '', $this->storeName);
            $this->messages[] = ['role' => 'assistant', 'content' => 'Halo! Ada yang bisa saya bantu untuk pesanan hari ini di ' . $shortName . '?'];
        }
    }

    public function loadMessages()
    {
        $chatSession = AiChatSession::find($this->sessionId);
        $this->messages = $chatSession->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return ['role' => $msg->role, 'content' => $msg->content];
            })
            ->toArray();
    }

    public function sendQuickReply($text)
    {
        $this->userInput = $text;
        $this->sendMessage();
    }

    public function sendMessage()
    {
        $this->validate(['userInput' => 'required|string|max:500']);
        
        $this->loadMessages();
        
        $userMsg = $this->userInput;
        $this->userInput = '';
        
        // Add optimistic UI message
        $this->messages[] = ['role' => 'user', 'content' => $userMsg];
        
        $session = AiChatSession::find($this->sessionId);
        $service = app(OpenAiMenuService::class);
        
        // Ambil balasan utuh (tanpa stream)
        $fullReply = $service->generateResponse($session, $userMsg);
        
        $this->messages[] = ['role' => 'assistant', 'content' => $fullReply];
    }
};
?>

<div class="fixed z-[1050]" 
     x-ref="container"
     :style="(!isOpen && isCustomPositioned) ? `left: ${x}px; top: ${y}px; bottom: auto; right: auto; transition: ${isDragging ? 'none' : 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'};` : 'transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);'"
     :class="isOpen ? 'inset-0 sm:inset-auto sm:bottom-6 sm:right-6' : ((!isOpen && isCustomPositioned) ? '' : 'bottom-[105px] right-4 sm:bottom-6 sm:right-6')"
     x-data="{ 
         isOpen: false, showTooltip: false, contactModalOpen: false, showScroll: false,
         x: 0, y: 0, startX: 0, startY: 0, isDragging: false, moved: false, isCustomPositioned: false,
         initDrag(e) {
             if (this.isOpen) return;
             this.isDragging = true;
             this.moved = false;
             let clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
             let clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
             const rect = this.$refs.container.getBoundingClientRect();
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
     @open-contact-modal.window="contactModalOpen = true"
     @close-contact-modal.window="contactModalOpen = false"
     @keydown.escape.window="if(!isOpen) { contactModalOpen = false }"
     x-show="!(typeof qrOpen !== 'undefined' && qrOpen) && !(typeof optionOpen !== 'undefined' && optionOpen) && !(typeof checkoutOpen !== 'undefined' && checkoutOpen) && !(typeof historyOpen !== 'undefined' && historyOpen) && !contactModalOpen"
     x-init="
         if (!localStorage.getItem('aiTooltipDismissed')) {
             setTimeout(() => { showTooltip = true; }, 3000);
             setTimeout(() => { showTooltip = false; localStorage.setItem('aiTooltipDismissed', '1'); }, 11000);
         }
     "
     x-effect="document.body.style.overflow = isOpen && window.innerWidth < 640 ? 'hidden' : '';
               if (isOpen) {
                   setTimeout(() => {
                       const c = document.getElementById('tenant-chat-messages-container');
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
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="bg-[var(--surface)] flex flex-col overflow-hidden w-full h-full sm:shadow-2xl sm:rounded-3xl sm:mb-4 sm:border sm:border-[var(--border)] sm:w-[400px] sm:h-[550px] sm:max-h-[80vh]"
         style="display: none;">
        <!-- Header -->
        <div class="bg-[var(--foreground)] text-[var(--background)] px-4 py-3.5 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-3">
                <div class="bg-[var(--background)] text-[var(--foreground)] w-9 h-9 rounded-full flex items-center justify-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275Z"/><path d="m5 3 1 2.5L8.5 6 6 7 5 9.5 4 7 1.5 6 4 5.5Z"/><path d="m19 17 1 2.5 2.5.5-2.5 1-1 2.5-1-2.5-2.5-1 2.5-1Z"/></svg>
                </div>
                <div class="leading-tight">
                    <span class="font-bold block text-[15px]">{{ $storeName }}</span>
                    <span class="text-[11px] opacity-70 tracking-wide">Selalu siap membantu</span>
                </div>
            </div>
            <button type="button" class="opacity-70 hover:opacity-100 transition-opacity" @click="isOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        
        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 bg-[var(--background)] overscroll-contain" 
             id="tenant-chat-messages-container"
             @scroll.debounce.150ms="showScroll = $el.clientHeight > 100 && ($el.scrollHeight - $el.scrollTop - $el.clientHeight) > 150">
            <div class="text-center mb-5 mt-1">
                <span class="inline-block bg-[var(--surface)] border border-[var(--border)] text-[var(--text-secondary)] rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider">Ngobrol dengan AI Kami</span>
            </div>

            @foreach($messages as $msg)
                <div class="flex mb-4 {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="p-3.5 text-[14px] leading-relaxed shadow-sm max-w-[85%] {{ $msg['role'] === 'user' ? 'bg-[var(--foreground)] text-[var(--background)] rounded-2xl rounded-br-sm' : 'bg-[var(--surface)] text-[var(--foreground)] rounded-2xl rounded-bl-sm border border-[var(--border)] markdown-content' }}">
                        @if($msg['role'] === 'assistant')
                            @php
                                $htmlContent = str($msg['content'])->markdown(['html_input' => 'allow']);
                                $htmlContent = preg_replace_callback('/\[VARIANT_IDS?:\s*([\d, ]+)(?:\|EXTRAS:\s*([\d, ]+))?(?:\|QTY:\s*(\d+))?\]/', function($matches) {
                                    $variantIds = array_filter(array_map('trim', explode(',', $matches[1])));
                                    $extraIds = !empty($matches[2]) ? array_filter(array_map('trim', explode(',', $matches[2]))) : [];
                                    $qty = !empty($matches[3]) && is_numeric($matches[3]) ? (int)$matches[3] : 1;
                                    
                                    if (empty($variantIds)) return '';
                                    
                                    $variants = \App\Tenant\Models\Core\ProductVariant::with('product')->whereIn('id', $variantIds)->get();
                                    if ($variants->isEmpty() || !$variants->first()->product) return '';
                                    
                                    $product = $variants->first()->product;
                                    
                                    $validVariants = $variants->where('product_id', $product->id);
                                    if ($validVariants->isEmpty()) return '';
                                    
                                    $isMulti = $product->selection_type === 'multiple';
                                    if ($isMulti && $product->max_selections > 0 && $validVariants->count() > $product->max_selections) {
                                        $validVariants = $validVariants->take($product->max_selections);
                                    }
                                    
                                    $validVariantIds = $validVariants->pluck('id')->values()->toArray();
                                    
                                    $extraPrice = 0;
                                    $extraNames = [];
                                    $validExtraIds = [];
                                    if (!empty($extraIds)) {
                                        $extras = \App\Tenant\Models\Core\ProductExtra::whereIn('id', $extraIds)
                                            ->where('product_id', $product->id)
                                            ->get();
                                        $extraPrice = $extras->sum('price');
                                        $extraNames = $extras->pluck('name')->toArray();
                                        $validExtraIds = $extras->pluck('id')->values()->toArray();
                                    }
                                    
                                    if ($isMulti) {
                                        $finalPrice = $product->price + $extraPrice;
                                        $combinedVariantName = $validVariants->pluck('name')->join(', ');
                                        $variantIdToPass = 'null';
                                    } else {
                                        $variant = $validVariants->first();
                                        $finalPrice = ($variant->active_discount_price ?? $variant->price) + $extraPrice;
                                        $combinedVariantName = $variant->name;
                                        $variantIdToPass = $variant->id;
                                    }
                                    
                                    $productJson = htmlspecialchars(json_encode([
                                        'id' => $product->id,
                                        'name' => $product->name,
                                        'price' => $finalPrice,
                                        'image' => $product->image ? \Storage::url($product->image) : null,
                                        'has_variants' => $product->has_variants,
                                        'extras' => [],
                                        'variant_ids' => $validVariantIds,
                                        'extra_ids' => $validExtraIds
                                    ]), ENT_QUOTES, 'UTF-8');

                                    if ($product->has_variants || !empty($extraNames)) {
                                        // Jika Multi Varian ATAU punya ekstra: 
                                        // Gabungkan nama varian dan ekstra untuk jadi cart label
                                        $labels = [];
                                        if ($product->has_variants && $combinedVariantName) {
                                            $labels[] = $combinedVariantName;
                                        }
                                        if (!empty($extraNames)) {
                                            $labels[] = implode(', ', $extraNames);
                                        }
                                        $combinedLabel = implode(' + ', $labels);
                                        
                                        $buttonText = 'Tambah ' . $qty . ' ' . ($combinedLabel ?: 'Pesanan');

                                        return '<div class="mt-3"><button @click="addToCart(JSON.parse(\''.$productJson.'\'), \''.$combinedLabel.'\', '.$qty.', '.$variantIdToPass.')" class="bg-[var(--primary-color,bg-zinc-900)] text-zinc-900 text-xs px-4 py-2.5 rounded-xl font-bold shadow-sm border border-[var(--primary-color)] hover:brightness-110 w-full flex items-center justify-center gap-2 transition-all active:scale-95"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> '.$buttonText.'</button></div>';
                                    } else {
                                        // Single Varian & tanpa ekstra: abaikan nama varian dan id varian
                                        $buttonText = 'Tambah ' . $qty . ' ' . $product->name;
                                        return '<div class="mt-3"><button @click="addToCart(JSON.parse(\''.$productJson.'\'), \'\', '.$qty.')" class="bg-[var(--primary-color,bg-zinc-900)] text-zinc-900 text-xs px-4 py-2.5 rounded-xl font-bold shadow-sm border border-[var(--primary-color)] hover:brightness-110 w-full flex items-center justify-center gap-2 transition-all active:scale-95"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> '.$buttonText.'</button></div>';
                                    }
                                }, $htmlContent);
                            @endphp
                            {!! $htmlContent !!}
                            @if($loop->first && count($messages) === 1)
                                <div class="mt-3 flex flex-wrap gap-2" x-data="{ clicked: false }" x-show="!clicked">
                                    <button @click="clicked = true" wire:click="sendQuickReply('Menu yang paling laris?')" wire:loading.attr="disabled" class="border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--surface)] hover:text-[var(--foreground)] px-3 py-1.5 rounded-full text-[11px] font-semibold transition-colors">Menu paling laris?</button>
                                    <button @click="clicked = true" wire:click="sendQuickReply('Rekomendasi minuman manis?')" wire:loading.attr="disabled" class="border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--surface)] hover:text-[var(--foreground)] px-3 py-1.5 rounded-full text-[11px] font-semibold transition-colors">Rekomendasi minuman?</button>
                                    <button @click="clicked = true" wire:click="sendQuickReply('Gimana cara pesannya?')" wire:loading.attr="disabled" class="border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--surface)] hover:text-[var(--foreground)] px-3 py-1.5 rounded-full text-[11px] font-semibold transition-colors">Cara pesannya gimana?</button>
                                </div>
                            @endif
                        @else
                            {{ $msg['content'] }}
                        @endif
                    </div>
                </div>
            @endforeach
            
            <!-- Target for Loading state -->
            <div class="flex mb-4 justify-start hidden" wire:loading.class.remove="hidden" wire:target="sendMessage, sendQuickReply">
                <div class="p-3.5 text-[14px] leading-relaxed bg-[var(--surface)] text-[var(--foreground)] rounded-2xl rounded-bl-sm border border-[var(--border)] shadow-sm max-w-[85%] flex items-center gap-2">
                    <span class="text-[var(--text-secondary)] italic">Sedang berpikir...</span>
                    <svg class="animate-spin h-4 w-4 text-[var(--text-secondary)] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
            </div>
        </div>
        
        <!-- Input Area -->
        <div class="bg-[var(--surface)] border-t border-[var(--border)] p-3.5 relative shrink-0">
            <!-- Scroll to Bottom Button -->
            <button type="button" 
                    x-show="showScroll" 
                    x-transition.opacity
                    @click="let c = document.getElementById('tenant-chat-messages-container'); c.scrollTo({ top: c.scrollHeight, behavior: 'smooth' })"
                    class="bg-[var(--foreground)] text-[var(--background)] rounded-full w-9 h-9 flex items-center justify-center shadow-lg hover:brightness-110 transition-colors z-20 border border-[var(--border)]"
                    style="display: none; position: absolute; bottom: calc(100% + 12px); right: 16px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            
            <form wire:submit="sendMessage" class="flex items-center gap-2 m-0">
                <input type="text" class="flex-1 bg-[var(--background)] border border-[var(--border)] rounded-full px-5 py-2.5 text-sm focus:bg-[var(--background)] focus:ring-2 focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] outline-none transition-all placeholder-[var(--text-secondary)]" wire:model="userInput" placeholder="Tanya sesuatu..." autocomplete="off" required>
                <button type="submit" class="bg-[var(--primary-color)] text-black rounded-full w-11 h-11 flex items-center justify-center shrink-0 hover:brightness-110 transition-all disabled:opacity-50" wire:loading.attr="disabled">
                    <svg wire:loading.remove wire:target="sendMessage" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="-ml-1 mt-0.5"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    <svg wire:loading wire:target="sendMessage" class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </form>
            <div class="text-center mt-2 text-[var(--text-secondary)]" style="font-size: 10px;">
                Asisten AI dapat membuat kesalahan.<br>Harap periksa kesesuaian keranjang & pesanan Anda.
            </div>
        </div>
    </div>

    <!-- Floating Button & CTA -->
    <div class="flex justify-end items-center gap-4 relative">
        <!-- CTA Tooltip -->
        <div x-show="!isOpen && showTooltip" 
             x-transition.opacity.duration.500ms
             class="bg-[var(--foreground)] text-[var(--background)] text-sm font-bold pl-4 pr-2 py-3 rounded-2xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1),0_8px_10px_-6px_rgba(0,0,0,0.1)] flex items-center gap-2 animate-[bounce_3s_infinite] relative">
            <span class="cursor-pointer" @click="isOpen = true; showTooltip = false; localStorage.setItem('aiTooltipDismissed', '1')">✨ Hai, butuh rekomendasi menu?</span>
            <button type="button" class="opacity-70 hover:opacity-100 p-1 flex items-center justify-center transition-opacity" @click.stop="showTooltip = false; localStorage.setItem('aiTooltipDismissed', '1')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <!-- Segitiga penunjuk -->
            <div class="absolute -right-1.5 top-1/2 -translate-y-1/2 w-3 h-3 bg-[var(--foreground)] transform rotate-45"></div>
        </div>

        <button type="button" x-ref="btn" style="touch-action: none;" @mousedown="initDrag" @touchstart="initDrag" @click="handleClick" class="bg-[var(--foreground)] text-[var(--background)] rounded-full shadow-2xl shadow-black/30 flex items-center justify-center w-14 h-14 hover:scale-105 active:scale-95 transition-all duration-200 border-2 border-[var(--border)] relative z-10 cursor-grab active:cursor-grabbing">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
        </button>
    </div>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    const container = document.getElementById('tenant-chat-messages-container');
                    if (container) {
                        setTimeout(() => {
                            container.scrollTop = container.scrollHeight;
                        }, 50);
                    }
                })
            })
        });
    </script>

    <style>
        .markdown-content p { margin-bottom: 0.5rem; }
        .markdown-content p:last-child { margin-bottom: 0; }
        .markdown-content strong { font-weight: 700; color: var(--foreground); }
        .markdown-content ul { list-style-type: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .markdown-content ol { list-style-type: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .markdown-content li { margin-bottom: 0.25rem; }
        .markdown-content img { width: 100%; height: 160px; object-fit: cover; border-radius: 0.75rem; margin-bottom: 0.75rem; border: 1px solid var(--border); }
    </style>
</div>