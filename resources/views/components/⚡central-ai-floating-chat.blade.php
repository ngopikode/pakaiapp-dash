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
    <div class="position-fixed" style="z-index: 1050;" 
         x-data="{ isOpen: window.innerWidth >= 992, showTooltip: window.innerWidth < 992, isMobile: window.innerWidth < 576 }"
         @resize.window="isMobile = window.innerWidth < 576"
         x-init="setTimeout(() => showTooltip = false, 8000)"
         :class="isOpen && isMobile ? 'top-0 bottom-0 start-0 end-0' : 'bottom-0 end-0 p-3 p-sm-4'"
         x-effect="document.body.style.overflow = isOpen && isMobile ? 'hidden' : ''">
        
        <!-- Chat Window -->
        <div x-show="isOpen" 
             x-transition.opacity.duration.300ms
             class="bg-white d-flex flex-column overflow-hidden"
             :class="isOpen && isMobile ? 'w-100 h-100 border-0 rounded-0' : 'shadow-lg rounded-4 mb-3 border'"
             style="display: none; width: 400px; max-width: 100vw; height: 550px; max-height: 85vh;">
            
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
                 style="background-color: #f8f9fa; min-height: 0; overscroll-behavior: contain;" 
                 id="central-chat-messages-container"
                 @scroll="$dispatch('chat-scrolled', ($el.scrollHeight - $el.scrollTop - $el.clientHeight) > 150)">
                <div class="text-center mb-4 mt-1">
                    <span class="badge text-secondary bg-secondary bg-opacity-10 rounded-pill px-3 py-2 text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Ngobrol dengan AI Kami</span>
                </div>

                @foreach($messages as $msg)
                    @if($msg['role'] !== 'system')
                        <div class="d-flex mb-3 {{ $msg['role'] === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="p-3 shadow-sm {{ $msg['role'] === 'user' ? 'bg-dark text-white rounded-4 rounded-bottom-0' : 'bg-white text-dark rounded-4 rounded-start-0 border markdown-content' }}" style="font-size: 14px; line-height: 1.6; max-width: 85%;">
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
                    <div class="p-3 shadow-sm bg-white text-dark rounded-4 rounded-start-0 border d-flex align-items-center gap-2" style="font-size: 14px; max-width: 85%;">
                        <span class="text-secondary fst-italic">Sedang berpikir...</span>
                        <div class="spinner-border spinner-border-sm text-secondary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Input Area -->
            <div class="bg-white border-top p-3 flex-shrink-0 position-relative">
                <!-- Scroll to Bottom Button -->
                <div x-data="{ showScroll: false }" @chat-scrolled.window="showScroll = $event.detail">
                    <button type="button" 
                            x-show="showScroll" 
                            x-transition.opacity
                            @click="let c = document.getElementById('central-chat-messages-container'); c.scrollTo({ top: c.scrollHeight, behavior: 'smooth' })"
                            class="position-absolute end-0 top-0 translate-middle-y me-3 mt-n2 btn btn-dark rounded-circle shadow-lg d-flex align-items-center justify-content-center border border-2 border-white"
                            style="width: 36px; height: 36px; z-index: 20;">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                
                <form wire:submit="sendMessage" class="d-flex align-items-center gap-2 m-0">
                    <input type="text" class="form-control rounded-pill px-4" style="font-size: 14px; padding-top: 0.65rem; padding-bottom: 0.65rem; background-color: #f1f3f5; border: none;" wire:model="userInput" placeholder="Tanya sesuatu..." autocomplete="off" required>
                    <button type="submit" class="btn btn-dark rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;" wire:loading.attr="disabled">
                        <div wire:loading.remove wire:target="sendMessage">
                            <i class="bi bi-send-fill"></i>
                        </div>
                        <div wire:loading wire:target="sendMessage" class="spinner-border spinner-border-sm text-white" role="status"></div>
                    </button>
                </form>
            </div>
        </div>

        <!-- Floating Button & CTA -->
        <div class="d-flex justify-content-end align-items-center gap-3 position-relative" :class="isOpen && isMobile ? 'd-none' : ''">
            <!-- CTA Tooltip -->
            <div x-show="!isOpen && showTooltip"
                 x-transition.opacity.duration.500ms
                 class="bg-white text-dark pl-3 pr-2 py-2 rounded-4 shadow-lg border d-flex align-items-center gap-2 fw-bold"
                 style="font-size: 14px; animation: floatBounce 3s infinite;">
                <span class="cursor-pointer" @click="isOpen = true; showTooltip = false">✨ Hai, butuh bantuan?</span>
                <button class="btn btn-link text-secondary p-0 m-0 ms-1 text-decoration-none d-flex align-items-center" @click.stop="showTooltip = false" style="line-height: 1;">
                    <i class="bi bi-x" style="font-size: 18px;"></i>
                </button>
                <!-- Segitiga penunjuk -->
                <div class="position-absolute bg-white border-end border-top" style="width: 12px; height: 12px; right: -6px; top: 50%; transform: translateY(-50%) rotate(45deg);"></div>
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
            @keyframes floatBounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-8px); }
            }
            .markdown-content p { margin-bottom: 0.5rem; }
            .markdown-content p:last-child { margin-bottom: 0; }
            .markdown-content strong { font-weight: 700; color: #212529; }
            .markdown-content ul { list-style-type: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
            .markdown-content ol { list-style-type: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
            .markdown-content li { margin-bottom: 0.25rem; }
            .markdown-content img { width: 100%; height: auto; max-height: 160px; object-fit: cover; border-radius: 0.75rem; margin-bottom: 0.75rem; border: 1px solid #dee2e6; }
        </style>
    </div>
</div>