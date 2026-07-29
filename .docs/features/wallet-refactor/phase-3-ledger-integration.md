# Wallet Refactor — Phase 3 Ledger Integration Plan

> Referensi: [Plan Utama](./plan.md) · [Laravel 13 Patterns](../../references/laravel13/PATTERNS.md)

Dokumen ini merinci implementasi Phase 3 untuk mencatat transaksi penjualan dari `OrderService` ke dalam dompet (`cash`, `bank`, `gateway`) sebagai buku kas toko.

---

## Scope Phase 3

Phase 3 mengintegrasikan pembuatan dan pembayaran pesanan (`Order`) agar nilai penjualannya masuk ke dalam sistem `Wallet` tenant.

Termasuk:
- Mencatat omzet penjualan ke dalam wallet yang sesuai saat pesanan sukses dibayar.
- Membatalkan/me-refund saldo dari wallet jika pesanan atau item di-void.
- Mengarahkan `DuitkuCallback` agar settlement masuk ke dompet `gateway`.

Tidak termasuk:
- Sesi Shift Kasir (Z-Report). Fitur ini akan dikerjakan terpisah di Modul Inventory & Shift.
- UI tab wallet.

---

## Aturan Pencatatan Ledger (Arus Kas)

### 1. Mapping Metode Pembayaran ke Wallet

Metode pembayaran (`payment_method`) di `Order` harus dipetakan ke tipe dompet yang tepat:

- `cash` → `Wallet::TYPE_CASH` (Tunai)
- `qris`, `transfer`, `manual` → `Wallet::TYPE_BANK` (Transfer Bank Bypass)
- `duitku`, `digital`, `midtrans` → `Wallet::TYPE_GATEWAY` (Payment Gateway)

### 2. Nilai Omzet Bersih

Uang yang masuk ke buku kas toko (wallet) adalah **omzet bersih**, yang berarti total uang yang menjadi hak toko. 
Jika `orders.application_fee > 0`, fee tersebut merupakan milik Pakaiapp dan harus dikeluarkan dari perhitungan pemasukan/pengeluaran toko.

Rumus: `Omzet Bersih = (Amount Paid atau Total Price) - Application Fee`

### 3. Timing Pencatatan

Pencatatan (`addBalance` atau `deductBalance`) harus terjadi pada saat yang tepat:
- **Order Langsung Lunas (Direct Checkout):** Saat `processOrder()` dijalankan dan order diset menjadi `completed` atau `paid`.
- **Order Menyusul/Pay Later:** Saat `processPayment()` dijalankan dan mengubah status menjadi `paid` atau `completed`.
- **Callback Gateway:** Saat webhook (`DuitkuCallbackController` dll) menerima status sukses dan mengeksekusi penyelesaian pesanan.
- **Pembatalan:** Saat `cancelOrder()` atau `voidItem()` dieksekusi pada order yang sebelumnya sudah masuk ke ledger (sudah berstatus `paid`/`completed`).

---

## Detail Implementasi

### 1. Helper Mapping di `OrderService`

Buat helper method private untuk memetakan nama payment method ke tipe dompet:

```php
private function getWalletTypeForPayment(string $paymentMethod): string
{
    return match (strtolower($paymentMethod)) {
        'cash' => Wallet::TYPE_CASH,
        'qris', 'transfer', 'manual' => Wallet::TYPE_BANK,
        'duitku', 'midtrans', 'digital' => Wallet::TYPE_GATEWAY,
        default => Wallet::TYPE_CASH,
    };
}
```

### 2. Injeksi `TenantWalletService` ke `OrderService`

Gunakan pattern Lazy Getter sesuai standar:

```php
protected ?TenantWalletService $walletService = null;

protected function walletService(): TenantWalletService
{
    return $this->walletService ??= app(TenantWalletService::class);
}
```

### 3. Integrasi pada `processOrder` (Direct Checkout)

Jika pesanan dibuat dan **langsung lunas** (status `paid` atau `completed`), catat pemasukannya:

```php
if (in_array($order->status, ['paid', 'completed'])) {
    $omzetBersih = $order->total_price - (float) ($order->application_fee ?? 0);
    if ($omzetBersih > 0) {
        $walletType = $this->getWalletTypeForPayment($order->payment_method);
        $this->walletService()->addBalance(
            amount: $omzetBersih,
            reference: $order,
            description: "Penerimaan dana dari pesanan {$order->invoice_code}",
            walletType: $walletType
        );
    }
}
```

### 4. Integrasi pada `processPayment`

Saat status berubah dari `pending` atau `progress` menjadi lunas:

```php
$omzetBersih = $totalPrice - (float) ($order->application_fee ?? 0);
if ($omzetBersih > 0) {
    $walletType = $this->getWalletTypeForPayment($paymentMethod);
    $this->walletService()->addBalance(
        amount: $omzetBersih,
        reference: $order,
        description: "Penerimaan dana pembayaran pesanan {$order->invoice_code}",
        walletType: $walletType
    );
}
```

*Catatan: Pastikan `processPayment` juga menangani case cicilan (jika amount_paid bertahap) atau potong penuh di akhir. Untuk MVP, anggap pembayaran lunas sekaligus.*

### 5. Integrasi pada `cancelOrder`

Jika pesanan dibatalkan tapi sebelumnya sudah pernah lunas/dibayar (sehingga uangnya sudah masuk ke ledger):

```php
// Jika status sebelum batal adalah paid/completed, kembalikan uang (deduct ledger kas toko)
$originalStatus = $order->getOriginal('status');
if (in_array($originalStatus, ['paid', 'completed'])) {
    $omzetBersih = $order->total_price - (float) ($order->application_fee ?? 0);
    if ($omzetBersih > 0) {
        $walletType = $this->getWalletTypeForPayment($order->payment_method);
        $this->walletService()->deductBalance(
            amount: $omzetBersih,
            reference: $order,
            description: "Pengembalian dana (refund) pembatalan pesanan {$order->invoice_code}",
            walletType: $walletType
        );
    }
}
```

### 6. Integrasi pada `voidItem`

Jika item divoid dari pesanan yang sudah lunas (misal: bayar duluan, lalu ada makanan kosong):

```php
$originalStatus = $order->getOriginal('status');
if (in_array($originalStatus, ['paid', 'completed'])) {
    // Karena item hilang, subtotal dan pajak menyesuaikan. Kembalikan sebesar subtotal item (belum dipotong proporsi pajak, atau gunakan pendekatan sederhana subtotal)
    // Untuk presisi: refund = Harga item setelah diskon + Porsi pajak item.
    // Pendekatan MVP: karena void item mengurangi total_price secara kalkulatif, cari selisih harga total yang baru.
    
    $oldTotalPrice = $order->getOriginal('total_price') - (float) ($order->getOriginal('application_fee') ?? 0);
    $newTotalPrice = $order->total_price - (float) ($order->application_fee ?? 0);
    $refundAmount = max(0, $oldTotalPrice - $newTotalPrice);
    
    if ($refundAmount > 0) {
        $walletType = $this->getWalletTypeForPayment($order->payment_method);
        $this->walletService()->deductBalance(
            amount: $refundAmount,
            reference: $order,
            description: "Pengembalian dana void item pada pesanan {$order->invoice_code}",
            walletType: $walletType
        );
    }
}
```

---

## Backward Compatibility & Edge Cases

- **Shift Integration (Deferred Cash Ledger):**
  Untuk Phase 3 ini, **order `cash` tetap langsung dimasukkan ke `Wallet::TYPE_CASH`**. Nanti pada saat modul Shift Kasir diimplementasikan, fungsi helper `$this->getWalletTypeForPayment()` atau tempat injeksinya akan diperbarui: Jika *Shift is Active*, tahan pencatatan ledger `cash`, masukkan ke `shifts.cash_sales` saja.
- **Double Entry Prevention:**
  Pemanggilan `addBalance` berada di dalam transaksi `DB::beginTransaction()`, jadi aman dari *race conditions* atau API retry berulang yang menghasilkan status *paid* berkali-kali.

---

## Acceptance Criteria

- Semua transaksi `cash`, `bank`, `gateway` yang lunas akan tercatat sebagai `CREDIT` (Pemasukan) pada dompet yang sesuai.
- Nominal yang tercatat adalah harga pesanan **dikurangi biaya layanan Pakaiapp (Application Fee)** agar tidak menggelembungkan laporan keuntungan toko.
- Membatalkan pesanan (`cancelOrder`) yang *sudah lunas* akan mencatat `DEBIT` (Pengeluaran) pada dompet yang bersangkutan, sebagai *refund*.
