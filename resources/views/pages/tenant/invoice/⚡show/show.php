<?php

use App\Models\Order;
use App\Models\StoreSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::print')]
class extends Component {

    public Order $order;
    public $store;

    // Menangkap parameter {code} dari URL
    public function mount($code): void
    {
        // Tarik data order beserta itemnya, kalau ga ada langsung lempar 404
        $this->order = Order::with('items')->where('invoice_code', $code)->firstOrFail();

        // Tarik info toko buat nampilin logo/nama toko di struk
        $this->store = StoreSetting::first();
    }

    // Auto-refresh order dari database (dipicu oleh wire:poll saat pending)
    public function refreshOrder(): void
    {
        $this->order->refresh();
    }

    // Mengambil nama dan logo secara dinamis dari API Duitku (Tanpa Hardcoding!)
    public function getPaymentMethodDetails(): array
    {
        $code = strtoupper($this->order->duitku_payment_method ?? '');
        
        try {
            $service = new \App\Services\DuitkuService();
            // Ambil daftar metode aktif dari API berdasarkan nominal order secara real-time
            $activeMethods = $service->getPaymentMethods((int) $this->order->total_price);
            
            foreach ($activeMethods as $method) {
                if (strtoupper($method['paymentMethod'] ?? '') === $code) {
                    return [
                        'name' => $method['paymentName'] ?? $code,
                        'logo' => $method['paymentImage'] ?? 'https://images.duitku.com/hotlink-ok/QRIS.PNG'
                    ];
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[Duitku Invoice] Gagal fetch metode pembayaran dari API', ['error' => $e->getMessage()]);
        }
        
        // Fallback statis yang aman jika koneksi Duitku API offline/bermasalah
        $fallbackMethods = [
            'BC' => ['name' => 'BCA Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/BCA.PNG'],
            'M2' => ['name' => 'Mandiri Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/MANDIRI.PNG'],
            'I1' => ['name' => 'BNI Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/BNI.PNG'],
            'BR' => ['name' => 'BRI Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/BRI.PNG'],
            'BT' => ['name' => 'Permata Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/PERMATA.PNG'],
            'B1' => ['name' => 'CIMB Niaga Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/CIMB.PNG'],
            'VA' => ['name' => 'Maybank Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/VA.PNG'],
            'DN' => ['name' => 'Danamon Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/DANAMON.PNG'],
            'HN' => ['name' => 'Hana Bank Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/HANA.PNG'],
            'NC' => ['name' => 'Neo Commerce Virtual Account', 'logo' => 'https://images.duitku.com/hotlink-ok/NEO.PNG'],
            'SP' => ['name' => 'ShopeePay', 'logo' => 'https://images.duitku.com/hotlink-ok/SHOPEEPAY.PNG'],
            'DA' => ['name' => 'DANA', 'logo' => 'https://images.duitku.com/hotlink-ok/DANA.PNG'],
            'OV' => ['name' => 'OVO', 'logo' => 'https://images.duitku.com/hotlink-ok/OVO.PNG'],
            'LA' => ['name' => 'LinkAja', 'logo' => 'https://images.duitku.com/hotlink-ok/LINKAJA.PNG'],
            'NQ' => ['name' => 'QRIS (ShopeePay/DANA/OVO/LinkAja)', 'logo' => 'https://images.duitku.com/hotlink-ok/QRIS.PNG'],
        ];

        return $fallbackMethods[$code] ?? [
            'name' => $this->order->duitku_payment_method ?: 'Digital Payment',
            'logo' => 'https://images.duitku.com/hotlink-ok/QRIS.PNG'
        ];
    }

    // Mengambil instruksi lengkap transfer berdasarkan bank yang dipilih
    public function getPaymentInstructions(): array
    {
        $code = strtoupper($this->order->duitku_payment_method ?? '');
        $va = $this->order->duitku_va_number ?? '';
        $amount = number_format($this->order->total_price, 0, ',', '.');

        switch ($code) {
            case 'BC': // BCA
                return [
                    'ATM BCA' => [
                        "Masukkan Kartu ATM dan PIN BCA Anda.",
                        "Pilih menu 'Penarikan Tunai/Transaksi Lainnya' > 'Transaksi Lainnya' > 'Transfer' > 'Ke Rek BCA Virtual Account'.",
                        "Masukkan nomor Virtual Account: <strong class='text-dark font-mono'>{$va}</strong>, lalu pilih 'Benar'.",
                        "Periksa detail transaksi. Pastikan nama merchant dan nominal transfer <strong class='text-dark'>Rp {$amount}</strong> sudah sesuai. Pilih 'Ya'.",
                        "Transaksi selesai. Simpan struk pembayaran Anda."
                    ],
                    'm-BCA (BCA Mobile)' => [
                        "Buka aplikasi BCA Mobile dan masuk ke m-BCA.",
                        "Pilih menu 'm-Transfer' > 'BCA Virtual Account'.",
                        "Masukkan nomor Virtual Account: <strong class='text-dark font-mono'>{$va}</strong>, lalu klik 'Send'.",
                        "Pastikan detail transaksi (nama merchant dan nominal transfer <strong class='text-dark'>Rp {$amount}</strong>) sudah sesuai. Pilih 'OK'.",
                        "Masukkan PIN m-BCA Anda dan klik 'OK'. Pembayaran selesai."
                    ],
                    'KlikBCA (Internet Banking)' => [
                        "Buka website KlikBCA dan login.",
                        "Pilih menu 'Transfer Dana' > 'Transfer ke BCA Virtual Account'.",
                        "Masukkan nomor Virtual Account: <strong class='text-dark font-mono'>{$va}</strong>.",
                        "Pastikan detail transaksi dan nominal transfer <strong class='text-dark'>Rp {$amount}</strong> sudah sesuai.",
                        "Ikuti instruksi KeyBCA untuk menyelesaikan transaksi."
                    ]
                ];
            case 'M2': // Mandiri
                return [
                    'ATM Mandiri' => [
                        "Masukkan Kartu ATM dan PIN Mandiri Anda.",
                        "Pilih menu 'Bayar/Beli' > 'Multipayment'.",
                        "Masukkan nomor Virtual Account / Mandiri Bill: <strong class='text-dark font-mono'>{$va}</strong>.",
                        "Masukkan nominal transfer <strong class='text-dark'>Rp {$amount}</strong>, lalu konfirmasi pembayaran.",
                        "Transaksi selesai. Simpan struk pembayaran Anda."
                    ],
                    'Livin\' by Mandiri' => [
                        "Buka aplikasi Livin' by Mandiri dan login.",
                        "Pilih menu 'Bayar' > 'Virtual Account' atau cari penyedia jasa yang sesuai.",
                        "Masukkan nomor Virtual Account: <strong class='text-dark font-mono'>{$va}</strong>.",
                        "Pastikan detail transaksi dan nominal pembayaran <strong class='text-dark'>Rp {$amount}</strong> sudah sesuai.",
                        "Masukkan PIN Livin' Anda untuk menyelesaikan transaksi."
                    ]
                ];
            case 'I1': // BNI
                return [
                    'ATM BNI' => [
                        "Masukkan Kartu ATM dan PIN BNI Anda.",
                        "Pilih menu 'Menu Lain' > 'Transfer' > 'Dari Rekening Tabungan' > 'Ke Rekening BNI Virtual Account'.",
                        "Masukkan nomor Virtual Account BNI: <strong class='text-dark font-mono'>{$va}</strong>.",
                        "Periksa detail transaksi. Pastikan nominal pembayaran <strong class='text-dark'>Rp {$amount}</strong> sudah sesuai, lalu pilih 'Ya'.",
                        "Transaksi selesai. Simpan struk pembayaran Anda."
                    ],
                    'BNI Mobile Banking' => [
                        "Buka aplikasi BNI Mobile Banking dan login.",
                        "Pilih menu 'Transfer' > 'Virtual Account Billing'.",
                        "Pilih rekening debet, lalu masukkan nomor Virtual Account BNI: <strong class='text-dark font-mono'>{$va}</strong>.",
                        "Detail transaksi akan muncul. Pastikan nominal transfer <strong class='text-dark'>Rp {$amount}</strong> sudah sesuai.",
                        "Masukkan Password Transaksi Anda untuk menyelesaikan pembayaran."
                    ]
                ];
            case 'BR': // BRI
                return [
                    'ATM BRI' => [
                        "Masukkan Kartu ATM dan PIN BRI Anda.",
                        "Pilih menu 'Transaksi Lain' > 'Pembayaran' > 'Lainnya' > 'BRIVA'.",
                        "Masukkan nomor BRIVA (Virtual Account): <strong class='text-dark font-mono'>{$va}</strong>.",
                        "Periksa detail transaksi. Pastikan nominal transfer <strong class='text-dark'>Rp {$amount}</strong> sudah sesuai, lalu pilih 'Ya'.",
                        "Transaksi selesai. Simpan struk pembayaran Anda."
                    ],
                    'BRImo (BRI Mobile)' => [
                        "Buka aplikasi BRImo dan login.",
                        "Pilih menu 'BRIVA'.",
                        "Pilih 'Pembayaran Baru' > Masukkan nomor BRIVA: <strong class='text-dark font-mono'>{$va}</strong>.",
                        "Periksa detail transaksi. Pastikan nama merchant dan nominal transfer <strong class='text-dark'>Rp {$amount}</strong> sudah sesuai.",
                        "Masukkan PIN BRImo Anda untuk memproses pembayaran."
                    ]
                ];
            default:
                return [
                    'Cara Pembayaran via ATM' => [
                        "Masukkan Kartu ATM dan PIN Anda.",
                        "Pilih menu 'Transfer' > 'Ke Rekening Virtual Account' (atau transfer ke bank lain jika berbeda bank).",
                        "Masukkan nomor Virtual Account: <strong class='text-dark font-mono'>{$va}</strong>.",
                        "Masukkan jumlah nominal transfer <strong class='text-dark'>Rp {$amount}</strong>.",
                        "Periksa kembali detail transaksi, lalu selesaikan pembayaran."
                    ],
                    'Cara Pembayaran via Mobile Banking' => [
                        "Buka aplikasi Mobile Banking Anda dan login.",
                        "Pilih menu 'Transfer' > 'Virtual Account' atau 'Transfer Antar Bank'.",
                        "Masukkan nomor Virtual Account: <strong class='text-dark font-mono'>{$va}</strong>.",
                        "Masukkan nominal pembayaran <strong class='text-dark'>Rp {$amount}</strong>.",
                        "Periksa kembali detail transaksi, lalu masukkan PIN Anda untuk menyelesaikan pembayaran."
                    ]
                ];
        }
    }

    // Biar tab browser & crawler Google ngebaca judul spesifik
    public function title(): string
    {
        $storeName = $this->store->name ?? 'Toko';
        return "Invoice {$this->order->invoice_code} - {$storeName}";
    }
};
