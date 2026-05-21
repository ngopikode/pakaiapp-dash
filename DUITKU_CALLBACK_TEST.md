Ran command: `[ -x /usr/lib/command-not-found -o -x /usr/share/command-not-found/command-not-found ]`

Oke, aku bikin Artisan command khusus buat testing callback-nya!

Ran command: `ls /var/www/pakaiapp-dash/app/Console/Commands/`
Listed directory Console
Listed directory Commands
Created TestDuitkuCallback.php

Sekarang cek apakah command langsung ke-detect:

Ran command: `php artisan list | grep duitku`

Terminal sandbox lagi tidak available, tapi command-nya udah siap. Ini cara pakainya:

---

## ✅ Command Siap!

File: [TestDuitkuCallback.php](file:///var/www/pakaiapp-dash/app/Console/Commands/TestDuitkuCallback.php)

---

### 🚀 Cara Pemakaian

**Mode interaktif** (paling gampang — tinggal pilih-pilih):
```bash
php artisan duitku:test-callback
```

**Mode langsung** (sudah tau tenant & invoice-nya):
```bash
php artisan duitku:test-callback \
  --tenant=abc-123 \
  --invoice=INV-20260522-ABCDEF \
  --amount=50000
```

**Simulasi pembayaran GAGAL:**
```bash
php artisan duitku:test-callback --fail
```

**Dry-run** (cuma tampilkan signature, tidak kirim request):
```bash
php artisan duitku:test-callback --dry-run
```

**Override URL** (kalau local domain beda):
```bash
php artisan duitku:test-callback --url=http://localhost:8000
```

---

### 📺 Contoh Output

```
  🔐 Duitku Callback Tester
  Local testing helper untuk endpoint /duitku/callback

  ✓ Tenant  : abc-123
  ✓ Invoice  : INV-20260522-ABCDEF
  ✓ Amount   : Rp 50.000

  📦 Payload yang akan dikirim:
  ┌─────────────────┬──────────────────────────┐
  │ merchantCode    │ DS30906                  │
  │ merchantOrderId │ abc-123~INV-20260522-... │
  │ resultCode      │ 00                       │
  │ signature       │ 3f9a12bc4d...            │
  └─────────────────┴──────────────────────────┘

  Simulasi pembayaran: ✅ SUKSES (resultCode: 00)

  → POST http://api.pakaiapp.test/duitku/callback
  Kirim request sekarang? [yes]

  ✅ Callback berhasil!
  HTTP 200 → OK

  📊 Status order setelah callback:
  ┌────────────────┬──────────────────┐
  │ status         │ paid             │
  │ payment_method │ qris             │
  │ amount_paid    │ Rp 50.000        │
  └────────────────┴──────────────────┘
```

---

### 💡 Fitur command ini:
- **Auto-list tenant** — pilih dari daftar tenant yang ada di DB
- **Auto-list order pending** — pilih dari 10 order terbaru yang masih pending
- **Hitung signature otomatis** pakai merchant key dari `.env`
- **Kirim sebagai `form-urlencoded`** — persis seperti Duitku asli
- **Verifikasi otomatis** — cek status order setelah callback dikirim
- **Skip SSL** — aman buat local HTTPS