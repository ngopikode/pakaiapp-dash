<?php

use App\Tenant\Models\Ai\AiChatSession;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\OpenAiMenuService;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public $sessionId = null;

    public $messages = [];

    public $userInput = '';

    public $storeName = 'Asisten AI';

    protected ?OpenAiMenuService $aiMenuService = null;

    protected function aiMenuService(): OpenAiMenuService
    {
        return $this->aiMenuService ??= app(OpenAiMenuService::class);
    }

    public function mount()
    {
        try {
            $setting = StoreSetting::first();
            if ($setting && $setting->name) {
                $this->storeName = 'Asisten ' . $setting->name;
            }
        } catch (Exception $e) {
        }

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
        $service = $this->aiMenuService();

        // Ambil balasan utuh (tanpa stream)
        $fullReply = $service->generateResponse($session, $userMsg);

        $this->messages[] = ['role' => 'assistant', 'content' => $fullReply];
    }
};
