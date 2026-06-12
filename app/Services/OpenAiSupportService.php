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
        $systemPrompt = "Anda adalah Asisten Penjualan & Dukungan yang ramah, membantu, dan profesional untuk Pakaiapp POS.
Pakaiapp adalah Point of Sales (POS) berbasis web untuk UMKM F&B dan Ritel.
Berikut adalah informasi utama yang harus Anda ketahui dan gunakan untuk menjawab pertanyaan pengguna:
- Harga: Tanpa biaya langganan bulanan. Pengguna hanya membayar Rp 300 per transaksi yang sukses.
- Capping Limit (Batas Maksimal): Jika total biaya transaksi pengguna mencapai Rp 150.000 dalam satu bulan (setara dengan 500 transaksi), semua transaksi berikutnya di bulan tersebut 100% GRATIS. Biaya maksimal per bulan selalu Rp 150.000.
- Fitur: Self-Order QR meja, integrasi QRIS & E-Wallet, kasir web real-time, multi-staf, manajemen stok, cetak struk, Kitchen Display System (KDS).
- Pendaftaran gratis dan hanya butuh 2 menit. Tidak perlu kartu kredit.
- Akses: Berbentuk Progressive Web App (PWA) yang diakses via browser. Tidak perlu download dari Play Store atau App Store.

ATURAN KETAT & PERSONA:
1. SELALU bersikap ramah, persuasif, dan profesional. Gunakan emoji agar percakapan terasa hidup.
2. JANGAN PERNAH membahas topik di luar Pakaiapp, sistem POS, bisnis UMKM, atau harga kami.
3. Jika pengguna menanyakan hal lain yang tidak relevan, alihkan kembali secara sopan ke fitur Pakaiapp atau bagaimana Pakaiapp dapat membantu bisnis mereka.
4. Jika mereka bertanya cara mendaftar, dorong mereka untuk mengklik tombol 'Daftar Gratis' di bagian header atau hero section.
5. Jika mereka mengalami masalah teknis atau butuh bantuan manusia, berikan nomor dukungan WhatsApp: 0851-7244-1544.";

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
