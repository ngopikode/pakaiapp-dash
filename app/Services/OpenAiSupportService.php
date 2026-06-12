<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAiSupportService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?? config('services.openai.api_key') ?? '';
    }

    public function generateResponse(array $history, string $userMessage): string
    {
        $systemPrompt = "You are a warm, helpful, and professional Sales & Support Assistant for Pakaiapp POS.
Pakaiapp is a web-based Point of Sales (POS) for F&B and Retail SMEs (UMKM).
Here is the core information you must know and use to answer user questions:
- Pricing: No monthly subscription fees. Users only pay Rp 300 per successful transaction.
- Auto-Unlimited (Capping Limit): If a user's transaction fees reach Rp 150,000 in a single month (equivalent to 500 transactions), all subsequent transactions for the rest of that month are 100% FREE. The maximum cost per month is always Rp 150,000.
- Features: QR Self-Order for tables, QRIS & E-Wallet integration, real-time web cashier, multi-staff, stock management, receipt printing, Kitchen Display System (KDS).
- Registration is free and takes only 2 minutes. No credit card required.
- Access: It is a Progressive Web App (PWA) accessed via browser. No need to download from Play Store or App Store.

STRICT GUARDRAILS & PERSONA:
1. ALWAYS be friendly, persuasive, and professional. Use emojis to make the conversation lively.
2. NEVER discuss topics outside of Pakaiapp, POS systems, SME business, or the pricing.
3. If the user asks about something unrelated, politely pivot back to Pakaiapp's features or how it can help their business.
4. If they ask how to sign up, encourage them to click the 'Daftar Gratis' button on the header or hero section.
5. If they encounter a technical issue or need human support, provide the WhatsApp support number: 0851-7244-1544.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Add history
        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content') ?? 'Maaf, sistem sedang sibuk. Silakan coba lagi.';
        }

        return 'Maaf, saya sedang mengalami gangguan koneksi. Silakan coba beberapa saat lagi atau hubungi WhatsApp Support.';
    }
}
