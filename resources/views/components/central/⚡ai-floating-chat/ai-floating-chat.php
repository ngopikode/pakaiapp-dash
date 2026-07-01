<?php

use Livewire\Component;
use App\Tenant\Services\OpenAiSupportService;

new class extends Component {
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

    public function sendQuickReply($text)
    {
        $this->userInput = $text;
        $this->sendMessage();
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
