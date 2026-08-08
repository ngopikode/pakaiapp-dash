# Feature: QRIS Confirmation Screen (Resto — WA Checkout)

Dokumentasi ini mencatat perubahan alur checkout WhatsApp untuk toko tipe Resto saat pelanggan memilih metode pembayaran QRIS. Perubahan ini juga menangani isu layout yang saling tindih dan mengoptimalkan UX dengan Local Storage.

---

## 1. Alur Baru (Intercept `processOrder`)

Sebelumnya, memilih QRIS dan menekan "Pesan & Transfer QRIS" akan langsung mengeksekusi request API `/api/preorders` dan lompat ke layar Sukses. Hal ini menyebabkan user kebingungan karena QRIS tidak sempat di-scan.

**Alur yang diterapkan:**
1. Klik "Pesan & Transfer QRIS" memanggil method baru: `proceedToQrisOrProcess()`.
2. Method tersebut melakukan validasi form.
3. Jika mode adalah WA Checkout / Pre-Order DAN pembayaran QRIS, eksekusi API **ditunda**.
4. Layar QRIS Confirmation dimunculkan (`showQrisConfirm = true`). Data pesanan sementara dimasukkan ke `pendingQrisOrder`.
5. Di layar QRIS, user bisa scan/download gambar.
6. User menekan **"Konfirmasi & Kirim Bukti via WA"** -> Barulah `processOrder()` dieksekusi (API di-*hit*, order disimpan ke DB, keranjang dikosongkan, form di-reset, lalu WA dibuka).

---

## 2. Struktur Layout Checkout Modal

Terjadi isu di mana elemen subtotal dan daftar metode pembayaran bocor atau terdorong oleh spasi kosong ketika layar QRIS aktif.
Penyelesaiannya adalah dengan menstrukturkan **Step 2** menjadi 3 *layer* independen bersaudara (siblings) yang dibungkus `flex-col min-h-0 overflow-hidden`:

```html
<div x-show="checkoutStep === 2" class="flex-1 min-h-0 flex flex-col overflow-hidden">
    
    <!-- LAYER 1: QRIS CONFIRM -->
    <template x-if="showQrisConfirm && pendingQrisOrder">
        <div class="flex-1 min-h-0 flex flex-col overflow-hidden ...">
            ... konten qris ...
            <div class="shrink-0 p-6 ..."> <button @click="confirmQrisPayment()">WA</button> </div>
        </div>
    </template>

    <!-- LAYER 2: FORM VIEW (Input + Tombol Pesan) -->
    <div x-show="!showQrisConfirm && !showPaymentSelector" class="flex-1 min-h-0 flex flex-col ...">
        <div class="flex-1 overflow-y-auto ..."> ... Input Form ... </div>
        <div class="shrink-0 p-6 ..."> ... Subtotal & Tombol Pesan Utama ... </div>
    </div>

    <!-- LAYER 3: PAYMENT SELECTOR (Daftar Bank) -->
    <div x-show="showPaymentSelector" class="flex-1 min-h-0 flex flex-col ...">
        <div class="flex-1 overflow-y-auto ..."> ... Daftar Metode ... </div>
        <div class="shrink-0 p-6 ..."> <button @click="showPaymentSelector=false">Konfirmasi Metode</button> </div>
    </div>
</div>
```

---

## 3. Download QRIS via Javascript `fetch()` Blob

Agar fungsi download gambar QRIS berfungsi baik pada file dengan *absolute path* / *cross-origin*, fungsi download mem-fetch data menggunakan `blob()` lalu merubahnya menjadi `ObjectURL`.

```js
async downloadQris() {
    if (!this.qrisImage || !this.pendingQrisOrder) return;
    try {
        const res = await fetch(this.qrisImage);
        if (!res.ok) throw new Error('Network response was not ok');
        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `QRIS-${this.pendingQrisOrder.invoiceCode}.jpg`;
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Download QRIS failed:', error);
        this.showToast('Gagal mengunduh QRIS. Silahkan screenshot layar ini.', 'error');
    }
}
```

---

## 4. Local Storage untuk Detail Pelanggan

Untuk UX yang lebih baik, detail form dipertahankan jika pelanggan melakukan pesanan lagi:
- State Alpine `$watch` mendengarkan perubahan variabel: `customerName`, `customerEmail`, `customerPhone`, `customerAddress`.
- Otomatis tersimpan ke `localStorage.getItem('tenant_store_customer')`.
- Variabel di atas **TIDAK** lagi dikosongkan (di-reset ke `''`) pada blok akhir `processOrder()`.
- Yang di-reset hanya keranjang belanja (`cart`) dan catatan tambahan (`customerInfo`).

---

## 5. Penghapusan QRIS (Toko Setting)

Pada bagian backend *Store Setting* (Dashboard Tenant):
- Menambahkan tombol "Hapus Gambar QRIS" di tampilan Alpine.
- Membuat route & action khusus untuk manghapus image qris melalui `SettingService::deleteQrisImage()`.
- Pembersihan memori in-memory cache menggunakan `StoreSetting::forgetCache()`.
