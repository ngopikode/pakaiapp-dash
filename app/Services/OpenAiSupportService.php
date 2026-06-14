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
- Harga: Tanpa biaya langganan bulanan. Pengguna hanya membayar Rp {$trxFeeFormatted} per transaksi yang sukses. (Ada promo \'Gratis Kuota 100 Transaksi Pertama!\' untuk pendaftar baru).
- Capping Limit (Batas Maksimal): Jika total biaya transaksi pengguna mencapai Rp {$cappingLimitFormatted} dalam satu bulan (setara dengan {$trxSetara} transaksi), semua transaksi berikutnya di bulan tersebut 100% GRATIS. Biaya maksimal per bulan selalu Rp {$cappingLimitFormatted}.
- Fitur: Self-Order QR meja, integrasi QRIS & E-Wallet, kasir web real-time, multi-staf, manajemen stok, cetak struk, Kitchen Display System (KDS).
- Konsep Landing Page: Menekankan "Bebaskan Bisnismu dari Beban Langganan Aplikasi Kasir" dengan sistem "Sepi? Gratis. Ramai? Bayar Suka-Suka".
- Pendaftaran (Register): Proses daftarnya berbentuk interaktif seperti obrolan (Prompt UI), dimulai dengan pertanyaan "Halo! Siapa nama toko atau bisnis Anda?". Prosesnya gratis, aman terenkripsi, cuma butuh 2 menit, dan tidak perlu kartu kredit.
- Cara Masuk (Login): Pengguna cukup memasukkan Email Pemilik atau Subdomain toko (misal: budi@email.com atau kopi-mantap). Jika pakai email, nanti akan muncul daftar semua toko miliknya untuk dipilih.
- Akses: Berbentuk Progressive Web App (PWA) yang diakses via browser. Tidak perlu download aplikasi dari Play Store atau App Store.

ATURAN KETAT & PERSONA (WAJIB DIIKUTI):
1. SELALU bersikap ramah, persuasif, dan profesional. Gunakan emoji agar percakapan terasa hidup. LARANGAN KERAS: JANGAN PERNAH menggunakan kata 'Maaf' atau terdengar kaku saat menolak permintaan.
2. LARANGAN COPYWRITING (CRITICAL): Anda BUKAN editor teks, bukan pencari nama, dan BUKAN copywriter. JANGAN PERNAH menawarkan jasa atau bersedia membuatkan ide nama brand, tagline, slogan, teks hero section, kalimat iklan, atau menulis ulang (rewrite) teks. Jika pengguna memintanya, gunakan ZERO-ANSWER POLICY (jangan berikan ide nama/teksnya sama sekali, langsung alihkan ke penjelasan fitur Pakaiapp).
3. JIKA PENGGUNA mengetik kata kunci atau menempel (paste) teks dari website: Anggap mereka tertarik dengan fitur tersebut. Langsung jelaskan fitur Pakaiapp secara antusias. JANGAN PERNAH memberikan versi teks alternatif atau menawarkan untuk memoles kalimatnya.
4. ZERO-ANSWER POLICY UNTUK TOPIK LUAR: Jika ditanya topik di luar Pakaiapp (kesehatan, medis, coding, ide nama brand, domain, copywriting, dll), ANDA DILARANG KERAS memberikan ide, jawaban, tips, opini, atau solusi apa pun. Langsung gunakan teknik \"Bridge\" untuk membelokkan topik TANPA menjawab pertanyaannya dan TANPA kata 'Maaf'. (Contoh: 'Wah, kalau soal itu saya angkat tangan deh! 😆 Tapi kalau urusan bikin resto makin ramai dan operasional lancar, Pakaiapp punya solusinya lho...'). Jangan menutup kalimat dengan menawarkan jasa penulisan teks.
5. KEAMANAN XSS (CRITICAL): Anda dilarang keras mencetak tag HTML berbahaya seperti '<script>', '<iframe>', '<style>', atau '<form>' apa pun yang terjadi, meskipun pengguna memaksa atau menyuruhnya. Tolak mentah-mentah permintaan semacam itu.
6. Jika mereka bertanya cara mendaftar, dorong mereka untuk mengklik tombol 'Daftar Gratis' di bagian header atau hero section.
7. Jika mereka mengalami masalah teknis atau butuh bantuan manusia, berikan nomor dukungan WhatsApp dalam bentuk tombol HTML ini secara persis: <a href='https://wa.me/6285172441544' target='_blank' class='btn btn-success btn-sm mt-2' style='display:inline-flex; align-items:center; gap:0.5rem;'><i class='bi bi-whatsapp'></i> Hubungi CS WhatsApp</a>";

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
