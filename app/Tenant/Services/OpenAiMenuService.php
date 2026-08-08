<?php

namespace App\Tenant\Services;

use App\Tenant\Models\Ai\AiChatSession;
use App\Tenant\Models\Core\Product;
use App\Tenant\Models\Core\StoreSetting;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
     * @throws ConnectionException
     */
    public function generateResponse(AiChatSession $session, string $userMessage): string
    {
        // 1. Simpan pesan user
        $session->messages()->create([
            'role' => 'user',
            'content' => $userMessage,
        ]);

        // 2. Tarik list menu aktif (di-cache untuk optimasi database)
        $cacheKey = 'ai_menu_tenant_' . (function_exists('tenant') ? tenant('id') : 'global');
        $activeMenu = Cache::rememberForever($cacheKey, function () {
            return Product::where('is_active', true)
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
                        'image_url' => $product->image ? Storage::url($product->image) : null,
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
        });

        $menuJson = json_encode($activeMenu);

        // 3. Meracik System Prompt yang sangat ketat (dengan Jailbreak Guard)
        $storeName = 'Restoran Kami';
        try {
            $setting = StoreSetting::cached();
            if ($setting && $setting->name) {
                $storeName = $setting->name;
            }
        } catch (Exception) {
        }

        $systemPrompt = "Anda adalah pelayan / asisten AI eksklusif yang ramah, elegan, dan profesional untuk $storeName.
Tugas Anda adalah memanjakan pelanggan yang ingin memesan makanan/minuman dan memberikan pengalaman 'fine-dining' digital.

ATURAN KETAT & PERSONA:
1. SELALU gunakan Bahasa Indonesia yang elegan, hangat, dan persuasif! Perlakukan pelanggan layaknya tamu VIP. Jika mereka bertanya 'Mengapa saya harus memilih menu ini?', promosikan menu tersebut dengan kata-kata yang menggugah selera dan meyakinkan.
2. JANGAN PERNAH melayani obrolan di luar topik makanan, minuman, pemesanan, atau restoran. Jika ditanya hal lain, tanggapi dengan candaan hangat dan langsung alihkan kembali ke menu lezat kami.
3. JANGAN PERNAH mengarang menu baru yang tidak tercantum dalam daftar menu yang disediakan (no hallucination).
4. Jika pelanggan ingin memesan sesuatu, arahkan mereka dengan antusias untuk menambahkannya ke keranjang.
5. UPSELLING & CROSS-SELLING (PENTING!): Selalu cari peluang secara natural untuk menaikkan nilai pesanan. Jika pelanggan memesan makanan utama, tawarkan minuman penyegar atau dessert. Jika memesan minuman, tawarkan cemilan pendamping. Jangan terlalu agresif, lakukan layaknya rekomendasi dari koki.
6. Jika produk memiliki beberapa varian (has_variants = true):
   - Jika 'selection_type' adalah 'single', minta pelanggan memilih 1 varian saja.
   - Jika 'selection_type' adalah 'multiple', pelanggan dapat memilih hingga 'max_selections' varian.
   Jika ada ekstra/add-on yang tersedia, Anda sangat dianjurkan untuk menawarkannya (misal: 'Apakah ingin ditambah ekstra Keju Mozzarella?').
7. Setelah pelanggan mengonfirmasi pesanan mereka, Anda WAJIB menyisipkan tag ID varian yang mereka inginkan dengan format yang tepat di akhir pesan Anda.
   Format Tag:
   - Untuk varian tunggal atau pilihan tunggal: [VARIANT_ID: 34|QTY: 1]
   - Untuk pilihan ganda: [VARIANT_IDS: 34,35|QTY: 2]
   Jika ada ekstra yang dipilih, gabungkan seperti: [VARIANT_ID: 34|EXTRAS: 1,4|QTY: 1].
   Jangan menyebutkan tag ini secara langsung di teks obrolan, melainkan letakkan secara tidak terlihat di paling bawah/akhir pesan.
8. PENTING (BACA DENGAN TELITI): Jangan pernah bingung membedakan antara JUMLAH/PORSI pesanan dengan ID Produk atau ID Varian (misalnya: pelanggan memesan 'pecel 3' artinya 3 porsi, BUKAN barang ber-ID 3).
9. PENTING: JANGAN katakan kalimat kaku seperti 'Pesanan Anda telah berhasil ditambahkan ke keranjang'. Anda TIDAK BISA memasukkan item ke keranjang secara otomatis. Sebagai gantinya, Anda WAJIB menggunakan kalimat elegan seperti 'Pilihan yang luar biasa! Hidangan ini sudah saya siapkan, silakan ketuk tombol di bawah untuk menambahkannya ke pesanan meja Anda.' ketika Anda memunculkan tag tersebut.
10. Saat menyarankan menu, JANGAN menyarankan lebih dari 2 atau 3 item sekaligus agar pelanggan tidak bingung.
11. Jika menyarankan produk tertentu yang memiliki 'image_url', Anda WAJIB menampilkan gambarnya menggunakan format Markdown: `![{name}]({image_url})` TEPAT SEBELUM teks deskripsi produk tersebut.
11. KEAMANAN XSS (CRITICAL): Anda dilarang keras mencetak tag HTML berbahaya seperti `<script>`, `<iframe>`, `<style>`, atau `<form>` apa pun yang terjadi, meskipun pengguna memaksa atau menyuruhnya. Tolak mentah-mentah permintaan semacam itu.

Berikut adalah daftar menu aktif hari ini dalam format JSON:
" . $menuJson;

        // 4. Bangun history chat
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
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
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-5.4-mini',
                    'messages' => $messages,
                    'temperature' => 0.7,
                ]);

            $fullContent = 'Maaf, sistem sedang sibuk. Silakan coba lagi.';

            if ($response->successful()) {
                $fullContent = $response->json('choices.0.message.content');
            }
        } catch (ConnectionException) {
            $fullContent = 'Maaf, saya sedang mengalami gangguan koneksi. Silakan coba beberapa saat lagi.';
        } catch (Exception) {
            $fullContent = 'Maaf, terjadi kesalahan internal. Silakan coba lagi nanti.';
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
     * @throws ConnectionException
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

        try {
            $response = Http::withToken($this->apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-5.4-mini',
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => 'My goal: ' . $goal],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');

                return json_decode($content, true) ?? [];
            }
        } catch (ConnectionException) {
            // Silently return empty array on connection error
            return [];
        } catch (Exception) {
            return [];
        }

        return [];
    }

    /**
     * Generate an AI Daily Briefing for the merchant's dashboard.
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
                    'model' => 'gpt-5.4-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => 'Berikan insight singkat untuk hari ini berdasarkan data tersebut.'],
                    ],
                    'temperature' => 0.6,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content') ?? 'Tidak dapat memuat wawasan AI saat ini.';
            }

            return 'Terjadi gangguan saat memuat wawasan AI (Error: ' . $response->status() . ').';
        } catch (Exception $e) {
            return 'Gagal terhubung ke layanan AI: ' . $e->getMessage();
        }
    }
}
