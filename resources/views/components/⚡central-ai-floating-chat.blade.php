<?php

use Livewire\Component;
use App\Services\OpenAiSupportService;

new class extends Component
{
    public $messages = [];
    public $userInput = '';

    public function mount()
    {
        $this->messages = session()->get('central_ai_chat_messages', []);
            
        if (empty($this->messages)) {
            $this->messages[] = ['role' => 'assistant', 'content' => 'Halo! 👋 Saya asisten virtual Pakaiapp. Ada yang ingin ditanyakan seputar pendaftaran atau fitur kami?'];
            session()->put('central_ai_chat_messages', $this->messages);
        }
    }

    public function sendMessage()
    {
        $this->validate(['userInput' => 'required|string|max:500']);
        
        $userMsg = $this->userInput;
        $this->userInput = '';
        
        $this->messages[] = ['role' => 'user', 'content' => $userMsg];
        session()->put('central_ai_chat_messages', $this->messages);
        
        $service = app(OpenAiSupportService::class);
        
        // Send last 10 messages for context (excluding the very new user message which is appended in the service)
        $historyForAi = collect($this->messages)
            ->take(-11) // take 11 because we just added the user message, we want previous + current
            ->slice(0, -1) // remove the very last one because the service appends it
            ->toArray();

        $fullReply = $service->generateResponse($historyForAi, $userMsg);
        
        $this->messages[] = ['role' => 'assistant', 'content' => $fullReply];
        session()->put('central_ai_chat_messages', $this->messages);
    }
};
?><div>
    <div class="central-chat-wrapper"
         wire:ignore.self
         x-data="{ isOpen: window.innerWidth >= 992, showTooltip: window.innerWidth < 992, isMobile: window.innerWidth < 576, showScroll: false }"
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
             x-transition.opacity.duration.300ms
             class="central-chat-window"
             :class="isOpen && isMobile ? 'border-0 rounded-0' : 'shadow-lg rounded-4 mb-3 border'"
             style="display: none; background-color: var(--chat-bg); color: var(--chat-text); border-color: var(--chat-border) !important;">
            
            <!-- Header -->
            <div class="bg-dark text-white p-3 d-flex justify-content-between align-items-center flex-shrink-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-robot fs-5"></i>
                    </div>
                    <div class="lh-sm">
                        <span class="fw-bold d-block" style="font-size: 15px;">Asisten Pakaiapp</span>
                        <span class="text-white-50" style="font-size: 11px; letter-spacing: 0.5px;">Selalu siap membantu</span>
                    </div>
                </div>
                <button type="button" class="btn btn-link text-white-50 p-1 m-0 text-decoration-none" @click="isOpen = false" aria-label="Tutup">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            
            <!-- Messages Area -->
            <div class="flex-grow-1 overflow-y-auto p-3" 
                 style="background-color: var(--chat-msg-area-bg); min-height: 0; overscroll-behavior: contain;" 
                 id="central-chat-messages-container"
                 @scroll.debounce.150ms="showScroll = $el.clientHeight > 100 && ($el.scrollHeight - $el.scrollTop - $el.clientHeight) > 150">
                <div class="text-center mb-4 mt-1">
                    <span class="badge rounded-pill px-3 py-2 text-uppercase" style="font-size: 10px; letter-spacing: 1px; color: var(--chat-text); opacity: 0.7; background-color: var(--chat-badge-bg);">Ngobrol dengan AI Kami</span>
                </div>

                @foreach($messages as $msg)
                    @if($msg['role'] !== 'system')
                        <div class="d-flex mb-3 {{ $msg['role'] === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="p-3 shadow-sm {{ $msg['role'] === 'user' ? 'user-msg-bubble' : 'bot-msg-bubble markdown-content' }}" style="font-size: 14px; line-height: 1.6; max-width: 85%;">
                                @if($msg['role'] === 'assistant')
                                    {!! str($msg['content'])->markdown(['html_input' => 'escape']) !!}
                                @else
                                    {{ $msg['content'] }}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
                
                <!-- Target for Loading state -->
                <div class="d-flex mb-3 justify-content-start d-none" wire:loading.class.remove="d-none" wire:target="sendMessage">
                    <div class="p-3 shadow-sm bot-msg-bubble d-flex align-items-center gap-2" style="font-size: 14px; max-width: 85%;">
                        <span class="text-muted fst-italic">Sedang berpikir...</span>
                        <div class="spinner-border spinner-border-sm text-secondary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Input Area -->
            <div class="border-top p-3 flex-shrink-0 position-relative" style="background-color: var(--chat-input-area-bg); border-color: var(--chat-border) !important;">
                
                <!-- WhatsApp Fallback Button -->
                <div class="text-center mb-2" style="padding-right: 52px;">
                    <a href="https://wa.me/6285172441544" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center justify-content-center gap-2 w-100" style="font-size: 12px; border-width: 1.5px;">
                        <i class="bi bi-whatsapp"></i> Hubungi Admin via WhatsApp
                    </a>
                </div>

                <!-- Scroll to Bottom Button -->
                <button type="button" 
                        x-show="showScroll" 
                        x-transition.opacity
                        @click="let c = document.getElementById('central-chat-messages-container'); c.scrollTo({ top: c.scrollHeight, behavior: 'smooth' })"
                        class="position-absolute btn btn-dark rounded-circle shadow-lg d-flex align-items-center justify-content-center border border-2 border-white"
                        style="display: none; width: 36px; height: 36px; z-index: 20; bottom: calc(100% + 12px); right: 16px;">
                    <i class="bi bi-chevron-down"></i>
                </button>
                
                <form wire:submit="sendMessage" class="d-flex align-items-center gap-2 m-0">
                    <input type="text" class="form-control rounded-pill px-4" style="font-size: 14px; padding-top: 0.65rem; padding-bottom: 0.65rem; background-color: var(--chat-input-bg); color: var(--chat-input-text); border: none;" wire:model="userInput" placeholder="Tanya sesuatu..." autocomplete="off" required>
                    <button type="submit" class="btn central-chat-send-btn rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;" wire:loading.attr="disabled">
                        <div wire:loading.remove wire:target="sendMessage">
                            <i class="bi bi-send-fill"></i>
                        </div>
                        <div wire:loading wire:target="sendMessage" class="spinner-border spinner-border-sm text-white" role="status"></div>
                    </button>
                </form>
                <div class="text-center mt-2 text-muted" style="font-size: 10px; opacity: 0.6;">
                    Asisten AI dapat membuat kesalahan.<br>Harap periksa kembali informasi penting Anda.
                </div>
            </div>
        </div>

        <!-- Floating Button & CTA -->
        <div class="d-flex justify-content-end align-items-center gap-3 position-relative" :class="isOpen && isMobile ? 'd-none' : ''">
            <!-- CTA Tooltip -->
            <div x-show="!isOpen && showTooltip"
                 x-transition.opacity.duration.500ms
                 class="central-chat-tooltip border d-flex align-items-center gap-2 fw-bold"
                 style="animation: floatBounce 3s infinite;">
                <span class="cursor-pointer" @click="isOpen = true; showTooltip = false">✨ Hai, butuh bantuan?</span>
                <button class="btn btn-link p-0 m-0 ms-1 text-decoration-none d-flex align-items-center" @click.stop="showTooltip = false" style="line-height: 1; color: inherit; opacity: 0.7;">
                    <i class="bi bi-x" style="font-size: 18px;"></i>
                </button>
                <!-- Segitiga penunjuk -->
                <div class="tooltip-arrow"></div>
            </div>

            <button type="button" class="btn btn-dark rounded-circle shadow-lg d-flex align-items-center justify-content-center border border-2 border-white" style="width: 56px; height: 56px; z-index: 10;" @click="isOpen = !isOpen; showTooltip = false">
                <i class="bi bi-chat-dots-fill fs-4" x-show="!isOpen"></i>
                <i class="bi bi-x-lg fs-4" x-show="isOpen" style="display: none;"></i>
            </button>
        </div>
        
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                    succeed(({ snapshot, effect }) => {
                        const container = document.getElementById('central-chat-messages-container');
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
            .central-chat-wrapper {
                position: fixed;
                z-index: 1050;
                pointer-events: none;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                align-items: flex-end;
                
                /* Theme CSS variables - LIGHT MODE (default) */
                --chat-bg: #ffffff;
                --chat-border: #dee2e6;
                --chat-text: #212529;
                --chat-badge-bg: rgba(108, 117, 125, 0.1);
                --chat-msg-area-bg: #f8f9fa;
                --chat-msg-user-bg: #212529;
                --chat-msg-user-text: #ffffff;
                --chat-msg-bot-bg: #ffffff;
                --chat-msg-bot-text: #212529;
                --chat-input-area-bg: #ffffff;
                --chat-input-bg: #f1f3f5;
                --chat-input-text: #212529;
                --chat-tooltip-bg: #ffffff;
                --chat-tooltip-text: #212529;
            }
            
            /* Theme CSS variables - DARK MODE */
            html.dark .central-chat-wrapper,
            [data-bs-theme="dark"] .central-chat-wrapper {
                --chat-bg: #1a1c1e;
                --chat-border: #2f3337;
                --chat-text: #e2e2e6;
                --chat-badge-bg: rgba(226, 226, 230, 0.1);
                --chat-msg-area-bg: #0f1113;
                --chat-msg-user-bg: #e2e2e6;
                --chat-msg-user-text: #1a1c1e;
                --chat-msg-bot-bg: #1f2225;
                --chat-msg-bot-text: #e2e2e6;
                --chat-input-area-bg: #1a1c1e;
                --chat-input-bg: #26292c;
                --chat-input-text: #e2e2e6;
                --chat-tooltip-bg: #1f2225;
                --chat-tooltip-text: #e2e2e6;
            }

            .central-chat-wrapper * {
                pointer-events: auto;
            }
            .central-chat-window {
                display: flex;
                flex-direction: column;
                overflow: hidden;
                width: 400px;
                max-width: 100vw;
                height: 550px;
                max-height: 85vh;
                background-color: var(--chat-bg);
            }
            .user-msg-bubble {
                background-color: var(--chat-msg-user-bg);
                color: var(--chat-msg-user-text);
                border-radius: 1rem 1rem 0 1rem;
            }
            .bot-msg-bubble {
                background-color: var(--chat-msg-bot-bg);
                color: var(--chat-msg-bot-text);
                border: 1px solid var(--chat-border);
                border-radius: 1rem 1rem 1rem 0;
            }
            .central-chat-send-btn {
                background-color: var(--chat-msg-user-bg);
                color: var(--chat-msg-user-text);
                border: none;
            }
            .central-chat-send-btn:hover {
                opacity: 0.9;
                background-color: var(--chat-msg-user-bg);
                color: var(--chat-msg-user-text);
            }
            .central-chat-tooltip {
                background-color: var(--chat-tooltip-bg);
                color: var(--chat-tooltip-text);
                border-color: var(--chat-border) !important;
                padding: 0.5rem 0.5rem 0.5rem 1rem;
                border-radius: 1rem;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                font-size: 14px;
                position: relative;
            }
            .tooltip-arrow {
                position: absolute;
                background-color: var(--chat-tooltip-bg);
                border-right: 1px solid var(--chat-border);
                border-top: 1px solid var(--chat-border);
                width: 12px;
                height: 12px;
                right: -6px;
                top: 50%;
                transform: translateY(-50%) rotate(45deg);
            }

            @media (min-width: 576px) {
                .central-chat-wrapper {
                    bottom: 0;
                    right: 0;
                    padding: 1.5rem;
                }
            }
            @media (max-width: 575.98px) {
                .central-chat-wrapper {
                    top: 0;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    padding: 0;
                }
                .central-chat-window {
                    width: 100% !important;
                    height: 100% !important;
                    max-width: 100% !important;
                    max-height: 100% !important;
                }
            }
            @keyframes floatBounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-8px); }
            }
            .markdown-content p { margin-bottom: 0.5rem; }
            .markdown-content p:last-child { margin-bottom: 0; }
            .markdown-content strong { font-weight: 700; color: inherit; }
            .markdown-content ul { list-style-type: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
            .markdown-content ol { list-style-type: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
            .markdown-content li { margin-bottom: 0.25rem; }
            .markdown-content img { width: 100%; height: auto; max-height: 160px; object-fit: cover; border-radius: 0.75rem; margin-bottom: 0.75rem; border: 1px solid var(--chat-border); }
        </style>
    </div>
</div>