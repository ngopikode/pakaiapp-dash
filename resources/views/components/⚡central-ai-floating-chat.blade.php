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
?>

<div class="fixed bottom-6 right-4 sm:right-6 z-[1050]" 
     x-data="{ isOpen: false }"
     x-effect="document.body.style.overflow = isOpen && window.innerWidth < 640 ? 'hidden' : ''">
    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="bg-white shadow-2xl rounded-3xl mb-4 flex flex-col overflow-hidden border border-zinc-200 w-[calc(100vw-32px)] sm:w-[400px] h-[500px] max-h-[calc(100dvh-120px)] sm:h-[550px]"
         style="display: none;">
        <!-- Header -->
        <div class="bg-zinc-900 text-white px-4 py-3.5 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-3">
                <div class="bg-white text-zinc-900 w-9 h-9 rounded-full flex items-center justify-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>
                </div>
                <div class="leading-tight">
                    <span class="font-bold block text-[15px]">Asisten Pakaiapp</span>
                    <span class="text-[11px] text-zinc-400 tracking-wide">Selalu siap membantu</span>
                </div>
            </div>
            <button class="text-zinc-400 hover:text-white transition-colors" @click="isOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        
        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 bg-zinc-50 overscroll-contain" id="central-chat-messages-container">
            <div class="text-center mb-5 mt-1">
                <span class="inline-block bg-zinc-200/50 text-zinc-500 rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider">Ngobrol dengan AI Kami</span>
            </div>

            @foreach($messages as $msg)
                <div class="flex mb-4 {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="p-3.5 text-[14px] leading-relaxed shadow-sm max-w-[85%] {{ $msg['role'] === 'user' ? 'bg-zinc-900 text-white rounded-2xl rounded-br-sm' : 'bg-white text-zinc-800 rounded-2xl rounded-bl-sm border border-zinc-100 markdown-content' }}">
                        @if($msg['role'] === 'assistant')
                            {!! str($msg['content'])->markdown(['html_input' => 'escape']) !!}
                        @else
                            {{ $msg['content'] }}
                        @endif
                    </div>
                </div>
            @endforeach
            
            <!-- Target for Loading state -->
            <div class="flex mb-4 justify-start hidden" wire:loading.class.remove="hidden" wire:target="sendMessage">
                <div class="p-3.5 text-[14px] leading-relaxed bg-white text-zinc-800 rounded-2xl rounded-bl-sm border border-zinc-100 shadow-sm max-w-[85%] flex items-center gap-2">
                    <span class="text-zinc-500 italic">Sedang berpikir...</span>
                    <svg class="animate-spin h-4 w-4 text-zinc-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
            </div>
        </div>
        
        <!-- Input Area -->
        <div class="bg-white border-t border-zinc-100 p-3.5">
            <form wire:submit="sendMessage" class="flex items-center gap-2 m-0">
                <input type="text" class="flex-1 bg-zinc-100 border-transparent rounded-full px-5 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-zinc-900 focus:border-transparent outline-none transition-all" wire:model="userInput" placeholder="Tanya sesuatu..." autocomplete="off" required>
                <button type="submit" class="bg-zinc-900 text-white rounded-full w-11 h-11 flex items-center justify-center shrink-0 hover:bg-zinc-800 transition-colors disabled:opacity-50" wire:loading.attr="disabled">
                    <svg wire:loading.remove wire:target="sendMessage" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="-ml-1 mt-0.5"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    <svg wire:loading wire:target="sendMessage" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Floating Button & CTA -->
    <div class="flex justify-end items-center gap-4 relative">
        <!-- CTA Tooltip -->
        <div x-show="!isOpen" 
             x-transition.opacity.duration.500ms
             class="bg-white text-zinc-800 text-sm font-bold px-4 py-3 rounded-2xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1),0_8px_10px_-6px_rgba(0,0,0,0.1)] border border-zinc-100 flex items-center gap-2 cursor-pointer animate-[bounce_3s_infinite] relative"
             @click="isOpen = true">
            <span>👋 Tanya-tanya Pakaiapp?</span>
            <!-- Segitiga penunjuk -->
            <div class="absolute -right-1.5 top-1/2 -translate-y-1/2 w-3 h-3 bg-white border-r border-t border-zinc-100 transform rotate-45"></div>
        </div>

        <button class="bg-[#1A2B3E] text-white rounded-full shadow-2xl shadow-[#1A2B3E]/30 flex items-center justify-center w-14 h-14 hover:scale-105 active:scale-95 transition-all duration-200 border-2 border-white relative z-10" @click="isOpen = !isOpen">
            <template x-if="isOpen">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </template>
            <template x-if="!isOpen">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
            </template>
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
        .markdown-content p { margin-bottom: 0.5rem; }
        .markdown-content p:last-child { margin-bottom: 0; }
        .markdown-content strong { font-weight: 700; color: #18181b; }
        .markdown-content ul { list-style-type: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .markdown-content ol { list-style-type: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .markdown-content li { margin-bottom: 0.25rem; }
        .markdown-content img { width: 100%; height: 160px; object-fit: cover; border-radius: 0.75rem; margin-bottom: 0.75rem; border: 1px solid #f4f4f5; }
    </style>
</div>