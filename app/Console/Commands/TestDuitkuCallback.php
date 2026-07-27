<?php

namespace App\Console\Commands;

use App\Central\Models\Tenant;
use App\Tenant\Models\Core\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * TestDuitkuCallback
 *
 * Helper command untuk testing Duitku callback di local environment.
 * Otomatis menghitung HMAC-SHA256 signature dan mengirim request POST
 * ke endpoint /duitku/callback seperti yang dilakukan server Duitku.
 *
 * Usage:
 *   php artisan duitku:test-callback
 *   php artisan duitku:test-callback --tenant=abc123 --invoice=INV-xxx --amount=50000
 *   php artisan duitku:test-callback --fail    (simulasi pembayaran gagal)
 *   php artisan duitku:test-callback --dry-run (hanya tampilkan data, tidak kirim request)
 */
class TestDuitkuCallback extends Command
{
    protected $signature = 'duitku:test-callback
                            {--tenant= : Tenant ID (UUID). Jika kosong, akan ditampilkan daftar tenant.}
                            {--invoice= : Invoice code dari order (status: pending). Jika kosong, akan otomatis dipilih.}
                            {--amount= : Jumlah pembayaran dalam Rupiah (default: ambil dari order).}
                            {--method=NQ : Kode payment method Duitku (NQ, BT, I1, dll).}
                            {--fail : Simulasi pembayaran GAGAL (resultCode 01).}
                            {--dry-run : Tampilkan payload & signature saja, tidak kirim request.}
                            {--url= : Override callback URL (default: dari DUITKU_CALLBACK_BASE_URL).}';

    protected $description = '🧪 Testing Duitku callback di local — auto-generate signature & kirim POST request';

    public function handle(): int
    {
        $this->newLine();
        $this->line('  <fg=cyan;options=bold>🔐 Duitku Callback Tester</fg=cyan;options=bold>');
        $this->line('  <fg=gray>Local testing helper untuk endpoint /duitku/callback</fg=gray>');
        $this->newLine();

        // ── 1. Resolve Tenant ────────────────────────────────────────────────
        $tenantId = $this->option('tenant');

        if (!$tenantId) {
            $tenants = Tenant::all(['id'])->pluck('id')->toArray();

            if (empty($tenants)) {
                $this->error('  Tidak ada tenant ditemukan di database. Buat tenant dulu!');

                return self::FAILURE;
            }

            $tenantId = $this->choice('  Pilih Tenant ID', $tenants, 0);
        }

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("  Tenant '{$tenantId}' tidak ditemukan.");

            return self::FAILURE;
        }

        $this->line("  <fg=green>✓</fg=green> Tenant: <fg=yellow>{$tenantId}</fg=yellow>");

        // ── 2. Resolve Invoice Code ──────────────────────────────────────────
        $invoiceCode = $this->option('invoice');
        $amount = $this->option('amount');

        $order = null;

        $tenant->run(function () use (&$order, &$invoiceCode, &$amount) {
            if ($invoiceCode) {
                $order = Order::where('invoice_code', $invoiceCode)->first();
                if (!$order) {
                    $this->warn("  Order '{$invoiceCode}' tidak ditemukan di tenant ini.");
                }
            } else {
                // Pilih order pending secara otomatis
                $pendingOrders = Order::where('status', 'pending')
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get(['invoice_code', 'total_price', 'created_at']);

                if ($pendingOrders->isEmpty()) {
                    $this->warn('  Tidak ada order pending di tenant ini. Mencari order terbaru...');
                    $order = Order::orderByDesc('created_at')->first();
                } else {
                    $choices = $pendingOrders->map(fn ($o) => "{$o->invoice_code}  (Rp " . number_format($o->total_price, 0, ',', '.') . ')'
                    )->toArray();

                    $selected = $this->choice('  Pilih order untuk di-test', $choices, 0);
                    $selectedIdx = array_search($selected, $choices);
                    $order = $pendingOrders[$selectedIdx];
                }

                if ($order) {
                    $invoiceCode = $order->invoice_code;
                }
            }

            if ($order && !$amount) {
                $amount = (string) $order->total_price;
            }
        });

        if (!$invoiceCode) {
            $this->error('  Tidak dapat menentukan invoice code. Gunakan --invoice=xxx');

            return self::FAILURE;
        }

        if (!$amount) {
            $amount = $this->ask('  Masukkan jumlah pembayaran (Rupiah)', '50000');
        }

        $this->line("  <fg=green>✓</fg=green> Invoice  : <fg=yellow>{$invoiceCode}</fg=yellow>");
        $this->line('  <fg=green>✓</fg=green> Amount   : <fg=yellow>Rp ' . number_format((int) $amount, 0, ',', '.') . '</fg=yellow>');

        // ── 3. Build Merchant Order ID & Hitung Signature ───────────────────
        $merchantCode = config('duitku.merchant_code');
        $merchantKey = config('duitku.merchant_key');
        $paymentMethod = strtoupper($this->option('method') ?? 'NQ');
        $resultCode = $this->option('fail') ? '01' : '00';
        $reference = 'LOCAL-TEST-' . strtoupper(substr(md5(uniqid()), 0, 8));

        $merchantOrderId = $tenantId . '~' . $invoiceCode;
        // Callback signature: merchantCode + amount + merchantOrderId
        $stringToSign = $merchantCode . $amount . $merchantOrderId;
        $signature = hash_hmac('sha256', $stringToSign, $merchantKey);

        // ── 4. Build Payload ─────────────────────────────────────────────────
        $payload = [
            'merchantCode' => $merchantCode,
            'amount' => $amount,
            'merchantOrderId' => $merchantOrderId,
            'productDetail' => 'Pembayaran ' . $invoiceCode,
            'additionalParam' => $tenantId,
            'paymentCode' => $paymentMethod,
            'resultCode' => $resultCode,
            'merchantUserId' => $tenantId,
            'reference' => $reference,
            'publisherOrderId' => '',
            'spUserHash' => '',
            'settlementDate' => now()->format('Y-m-d H:i:s'),
            'issuerCode' => '',
            'signature' => $signature,
        ];

        // ── 5. Tampilkan Info ────────────────────────────────────────────────
        $this->newLine();
        $this->line('  <fg=cyan;options=bold>📦 Payload yang akan dikirim:</fg=cyan;options=bold>');
        $this->table(
            ['Field', 'Value'],
            collect($payload)->map(fn ($v, $k) => [$k, $k === 'signature' ? substr($v, 0, 20) . '...' : $v])->values()->toArray()
        );

        $statusLabel = $resultCode === '00'
            ? '<fg=green;options=bold>✅ SUKSES (resultCode: 00)</fg=green;options=bold>'
            : '<fg=red;options=bold>❌ GAGAL (resultCode: 01)</fg=red;options=bold>';

        $this->line("  Simulasi pembayaran: {$statusLabel}");
        $this->newLine();

        // ── 6. Dry Run? ───────────────────────────────────────────────────────
        if ($this->option('dry-run')) {
            $this->line('  <fg=yellow>⚡ Dry-run mode — request tidak dikirim.</fg=yellow>');
            $this->newLine();
            $this->line('  <fg=cyan>Signature lengkap:</fg=cyan>');
            $this->line("  <fg=white>{$signature}</fg=white>");
            $this->newLine();
            $this->line('  <fg=cyan>String yang di-sign:</fg=cyan>');
            $this->line("  <fg=white>{$stringToSign}</fg=white>");

            return self::SUCCESS;
        }

        // ── 7. Kirim Request ─────────────────────────────────────────────────
        $callbackBaseUrl = $this->option('url')
            ?? config('duitku.callback_base_url', 'http://api.pakaiapp.test');

        $callbackUrl = rtrim($callbackBaseUrl, '/') . '/duitku/callback';

        $this->line("  <fg=gray>→ POST {$callbackUrl}</fg=gray>");

        if (!$this->confirm('  Kirim request sekarang?', true)) {
            $this->line('  Dibatalkan.');

            return self::SUCCESS;
        }

        $this->newLine();

        try {
            $response = Http::timeout(15)
                ->asForm()          // application/x-www-form-urlencoded (sama seperti Duitku asli)
                ->withoutVerifying() // Abaikan SSL di local
                ->post($callbackUrl, $payload);

            $statusCode = $response->status();
            $body = trim($response->body());

            if ($response->successful() && $body === 'OK') {
                $this->line('  <fg=green;options=bold>✅ Callback berhasil!</fg=green;options=bold>');
                $this->line("  HTTP {$statusCode} → <fg=green>{$body}</fg=green>");
            } else {
                $this->line('  <fg=yellow>⚠️  Response tidak seperti expected:</fg=yellow>');
                $this->line("  HTTP {$statusCode} → <fg=yellow>{$body}</fg=yellow>");
            }

        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('  ❌ Request gagal: ' . $e->getMessage());
            $this->newLine();
            $this->line('  <fg=yellow>💡 Tips:</fg=yellow>');
            $this->line('  • Pastikan domain <fg=cyan>api.pakaiapp.test</fg=cyan> ada di /etc/hosts → 127.0.0.1');
            $this->line('  • Atau gunakan: <fg=cyan>--url=http://localhost</fg=cyan>');
            $this->line('  • Cek apakah Laravel dev server sedang running.');

            return self::FAILURE;
        }

        $this->newLine();

        // ── 8. Cek Status Order Setelah Callback ─────────────────────────────
        $this->line('  <fg=cyan>📊 Status order setelah callback:</fg=cyan>');

        $finalOrder = null;
        $tenant->run(function () use (&$finalOrder, $invoiceCode) {
            $finalOrder = Order::where('invoice_code', $invoiceCode)
                ->first(['invoice_code', 'status', 'payment_method', 'amount_paid']);
        });

        if ($finalOrder) {
            $statusColor = match ($finalOrder->status) {
                'paid' => 'green',
                'cancelled' => 'red',
                default => 'yellow',
            };

            $this->table(
                ['Field', 'Value'],
                [
                    ['invoice_code',   $finalOrder->invoice_code],
                    ['status',         "<fg={$statusColor}>{$finalOrder->status}</fg={$statusColor}>"],
                    ['payment_method', $finalOrder->payment_method ?? '-'],
                    ['amount_paid',    $finalOrder->amount_paid
                        ? 'Rp ' . number_format($finalOrder->amount_paid, 0, ',', '.')
                        : '-'],
                ]
            );
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
