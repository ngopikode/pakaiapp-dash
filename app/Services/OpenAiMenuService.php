<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

class OpenAiMenuService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?? '';
    }

    /**
     * Generate response from OpenAI API
     *
     * @param AiChatSession $session
     * @param string $userMessage
     * @return string
     */
    public function generateResponse(AiChatSession $session, string $userMessage): string
    {
        // 1. Simpan pesan user
        $session->messages()->create([
            'role' => 'user',
            'content' => $userMessage,
        ]);

        // 2. Tarik list menu aktif (OPTIMASI MEMORY: Pilih kolom yang relevan saja)
        $activeMenu = Product::where('is_active', true)
            ->select('id', 'name', 'description', 'image', 'has_variants', 'selection_type', 'max_selections')
            ->with(['variants' => function ($query) {
                // Filter hanya varian yang ada stoknya, ambil kolom penting saja
                $query->select('id', 'product_id', 'name', 'price', 'stock')
                      ->where('stock', '>', 0);
            }, 'extras' => function ($query) {
                // Ambil kolom penting saja
                $query->select('id', 'product_id', 'name', 'price')
                      ->where('is_active', true);
            }])
            ->get()
            ->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'image_url' => $product->image ? \Storage::url($product->image) : null,
                    'has_variants' => $product->has_variants,
                    'selection_type' => $product->selection_type,
                    'max_selections' => $product->max_selections,
                    'variants' => $product->variants->map(function ($variant) {
                        return [
                            'variant_id' => $variant->id,
                            'name' => $variant->name,
                            'price' => $variant->active_discount_price ?? $variant->price,
                            'original_price' => $variant->active_discount_price ? $variant->price : null,
                            'stock' => $variant->stock,
                        ];
                    })->toArray(),
                    'extras' => $product->extras->map(function ($extra) {
                        return [
                            'extra_id' => $extra->id,
                            'name' => $extra->name,
                            'price' => $extra->price,
                        ];
                    })->toArray(),
                ];
            })->toArray();

        $menuJson = json_encode($activeMenu);

        // 3. Meracik System Prompt yang sangat ketat (dengan Jailbreak Guard)
        $storeName = 'Restoran Kami';
        try {
            $setting = \App\Models\StoreSetting::first();
            if ($setting && $setting->name) {
                $storeName = $setting->name;
            }
        } catch (\Exception $e) {}

        $systemPrompt = "You are a warm, fun, and enthusiastic digital barista/waiter for $storeName.
Your job is to assist customers with their orders based ONLY on the provided menu.
STRICT GUARDRAILS & PERSONA:
1. ALWAYS be friendly, conversational, and persuasive! Treat the customer like a friend. If they ask 'Why should I choose this?', hype up the menu item with exciting adjectives instead of sounding like a robot.
2. NEVER discuss topics outside of food, drinks, ordering, or the restaurant. If asked unrelated questions (coding, math, etc), gently laugh it off and pivot back to the delicious menu.
3. Never hallucinate or invent items that are not in the menu.
4. If the customer asks to order something, enthusiastically guide them to 'Tambah ke Keranjang'.
5. If the product has multiple variants (has_variants = true): 
   - If 'selection_type' is 'single', ask the customer to choose 1 variant. 
   - If 'selection_type' is 'multiple', the customer can choose up to 'max_selections' variants.
   If it has extras available, you can also offer them (e.g. + Susu, + Keju).
6. Once the customer confirms their order, you MUST return the exact ID(s) of the variant they want. Format: 
   - For single variant or single selection: [VARIANT_ID: 34|QTY: 1]
   - For multiple selection: [VARIANT_IDS: 34,35|QTY: 2] (separate IDs with comma, QTY indicates the number of portions)
   If they also chose any extras, append them: [VARIANT_ID: 34|EXTRAS: 1,4|QTY: 1] or [VARIANT_IDS: 34,35|EXTRAS: 1,4|QTY: 3]. If no extras, omit the EXTRAS part. Do not mention this tag directly in conversation text, just append it invisibly. If they order multiple different items, you can output multiple tags.
7. IMPORTANT: Do NOT say 'Pesanan Anda telah berhasil ditambahkan ke keranjang' or anything similar. You CANNOT add items to the cart yourself. Instead, you MUST say 'Silakan klik tombol di bawah ini untuk memasukkan pesanan ke keranjang' when outputting the tag.
8. When suggesting items to the customer, NEVER suggest more than 2 or 3 items at a time to keep the response concise and avoid overwhelming them.
9. If you suggest a specific item that has an image_url, you MUST display its image using Markdown syntax: `![{name}]({image_url})` BEFORE the text description.
Here is the available menu for today in JSON format:
" . $menuJson;

        // 4. Bangun history chat
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Ambil histori 10 pesan terakhir agar token tidak membengkak
        $history = $session->messages()->orderBy('created_at', 'desc')->take(10)->get()->reverse();
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        }

        // 5. Hit OpenAI API (tanpa stream)
        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'temperature' => 0.7,
            ]);

        $fullContent = 'Maaf, sistem sedang sibuk. Silakan coba lagi.';
        
        if ($response->successful()) {
            $fullContent = $response->json('choices.0.message.content');
        }

        // 6. Simpan pesan assistant
        $session->increment('turn_count');

        $session->messages()->create([
            'role' => 'assistant',
            'content' => $fullContent,
            'tokens_used' => str_word_count($fullContent) * 2, // Perkiraan token kasar
        ]);
        
        return $fullContent;
    }

    /**
     * Generate an AI pricing strategy based on merchant's goal
     * 
     * @param string $goal
     * @param array $menuData
     * @return array
     */
    public function generateMerchantStrategy(string $goal, array $menuData): array
    {
        $systemPrompt = "You are a revenue optimization AI for an F&B business.
Your goal is to suggest a smart pricing strategy based on the merchant's prompt.
You MUST respond ONLY with a valid JSON object matching this schema:
{
  \"ruleName\": \"String (e.g. Promo Hujan)\",
  \"ruleType\": \"percentage\" or \"fixed_cut\",
  \"discountValue\": Integer (e.g. 15 for 15% or 10000 for Rp10.000),
  \"startTime\": \"HH:MM\" (24h format),
  \"endTime\": \"HH:MM\" (24h format),
  \"activeDays\": [\"Mon\", \"Tue\", \"Wed\", \"Thu\", \"Fri\", \"Sat\", \"Sun\"],
  \"suggestedVariantIds\": [Array of integer variant_ids that match the goal. ONLY use variant_ids from the provided menu]
}
Here is the available menu:
" . json_encode($menuData);

        $response = Http::withToken($this->apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "My goal: " . $goal]
                ],
                'temperature' => 0.7,
            ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');
            return json_decode($content, true) ?? [];
        }

        return [];
    }

    /**
     * Generate an AI Daily Briefing for the merchant's dashboard.
     * 
     * @param array $dashboardData
     * @return string
     */
    public function generateDashboardInsight(array $dashboardData): string
    {
        $systemPrompt = "Anda adalah Business Analyst AI cerdas untuk bisnis F&B / Retail.
Tugas Anda adalah membaca data metrik penjualan hari ini dan memberikan 'Daily Briefing' yang sangat singkat, padat, dan intuitif bagi pemilik toko.

Aturan ketat penulisan:
1. Balas dengan format Markdown yang rapi menggunakan bullet points.
2. JANGAN gunakan paragraf pengantar atau penutup. Langsung ke 3 poin utama.
3. Gunakan kalimat yang sangat pendek dan padat (maksimal 1-2 kalimat pendek per poin). Hindari kalimat yang panjang dan bertele-tele.
4. Struktur balasan HARUS terdiri dari 3 poin utama berikut secara berurutan:
   - 🔥 **Performa Saat Ini**: [1 kalimat analisis singkat tentang omset/pesanan hari ini dibanding kemarin]
   - ⚠️ **Perhatian Operasional**: [1 kalimat tentang isu operasional, produk lambat terjual, atau jam tersibuk]
   - 💡 **Rekomendasi Promosi**: [1 saran konkret promosi/bundling yang bisa langsung dipasang]
5. Jika menyebutkan jam operasional, gunakan format waktu dengan titik dua seperti '14:00' atau deskripsi waktu ('siang hari', 'sore hari').
6. Gunakan data JSON berikut sebagai acuan metrik hari ini:
" . json_encode($dashboardData);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(45)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => 'Berikan insight singkat untuk hari ini berdasarkan data tersebut.']
                    ],
                    'temperature' => 0.6,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content') ?? 'Tidak dapat memuat wawasan AI saat ini.';
            }
            
            return 'Terjadi gangguan saat memuat wawasan AI (Error: ' . $response->status() . ').';
        } catch (\Exception $e) {
            return 'Gagal terhubung ke layanan AI: ' . $e->getMessage();
        }
    }
}
