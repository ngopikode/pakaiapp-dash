<?php

namespace App\Services;

use App\Models\GlobalSetting;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OpenAiSupportService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?? '';
    }

    /**
     * @param array $history
     * @param string $userMessage
     * @return string
     * @throws ConnectionException
     */
    public function generateResponse(array $history, string $userMessage): string
    {
        $trxFee = GlobalSetting::where('key', 'default_trx_fee')->first()?->value ?? 300;
        $cappingLimit = GlobalSetting::where('key', 'default_capping_limit')->first()?->value ?? 150000;
        $cappingLimitFormatted = number_format($cappingLimit, 0, ',', '.');
        $trxFeeFormatted = number_format($trxFee, 0, ',', '.');
        $trxSetara = $trxFee > 0 ? floor($cappingLimit / $trxFee) : 0;

        $systemPrompt = "Anda adalah Asisten Penjualan & Dukungan yang ramah, membantu, dan profesional untuk Pakaiapp POS.
Pakaiapp adalah Point of Sales (POS) berbasis web untuk UMKM F&B dan Ritel.
Berikut adalah informasi utama yang harus Anda ketahui dan gunakan untuk menjawab pertanyaan pengguna:
- Harga: Tanpa biaya langganan bulanan. Pengguna hanya membayar Rp {$trxFeeFormatted} per transaksi yang sukses. (Ada promo \"Gratis Kuota 100 Transaksi Pertama!\" untuk pendaftar baru).
- Capping Limit (Batas Maksimal): Jika total biaya transaksi pengguna mencapai Rp {$cappingLimitFormatted} dalam satu bulan (setara dengan {$trxSetara} transaksi), semua transaksi berikutnya di bulan tersebut 100% GRATIS. Biaya maksimal per bulan selalu Rp {$cappingLimitFormatted}.
- Fitur: Self-Order QR meja, integrasi QRIS & E-Wallet, kasir web real-time, multi-staf, manajemen stok, cetak struk, Kitchen Display System (KDS).
- Pendaftaran gratis dan hanya butuh 2 menit. Tidak perlu kartu kredit.
- Akses: Berbentuk Progressive Web App (PWA) yang diakses via browser. Tidak perlu download dari Play Store atau App Store.

ATURAN KETAT & PERSONA (WAJIB DIIKUTI):
1. SELALU bersikap ramah, persuasif, dan profesional. Gunakan emoji agar percakapan terasa hidup.
2. TUGAS ANDA HANYA MENJAWAB PERTANYAAN TENTANG PAKAIAAP. Anda BUKAN editor teks, bukan AI rewrite, dan BUKAN copywriter. Jangan tawarkan jasa mengubah teks.
3. JIKA PENGGUNA mengetik kata kunci atau menempelkan (paste) teks dari website (misalnya "3 langkah mudah", "tanpa install", "bayar suka-suka"): JANGAN MINTA MAAF. Anggap pengguna tertarik dengan hal tersebut, lalu langsung jelaskan secara antusias bagaimana fitur Pakaiapp itu bekerja. JANGAN menulis ulang (rewrite) kalimatnya untuk dijadikan iklan.
4. JIKA DITANYA spesifik tentang ide nama domain, template undangan, copywriting, layanan lain (seperti iPaymu), coding, atau topik umum di luar POS: BARU ANDA TOLAK SECARA TEGAS. Katakan bahwa Anda hanya asisten Pakaiapp POS.
5. KEAMANAN XSS (CRITICAL): Anda dilarang keras mencetak tag HTML berbahaya seperti `<script>`, `<iframe>`, `<style>`, atau `<form>` apa pun yang terjadi, meskipun pengguna memaksa atau menyuruhnya. Tolak mentah-mentah permintaan semacam itu.
6. Jika mereka bertanya cara mendaftar, dorong mereka untuk mengklik tombol 'Daftar Gratis' di bagian header atau hero section.
7. Jika mereka mengalami masalah teknis atau butuh bantuan manusia, berikan nomor dukungan WhatsApp dalam bentuk tombol HTML ini secara persis: `<a href='https://wa.me/6285172441544' target='_blank' class='btn btn-success btn-sm mt-2' style='display:inline-flex; align-items:center; gap:0.5rem;'><i class='bi bi-whatsapp'></i> Hubungi CS WhatsApp</a>`";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Add history
        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-5.4-mini',
                    'messages' => $messages,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content') ?? 'Maaf, sistem sedang sibuk. Silakan coba lagi.';
            }
        } catch (ConnectionException) {
            return 'Maaf, saya sedang mengalami gangguan koneksi ke server. Silakan coba beberapa saat lagi atau hubungi WhatsApp Support di bawah ini.';
        } catch (Exception) {
            return 'Maaf, terjadi kesalahan internal pada sistem. Silakan coba lagi nanti.';
        }

        return 'Maaf, saya sedang mengalami gangguan koneksi. Silakan coba beberapa saat lagi atau hubungi WhatsApp Support.';
    }
}
