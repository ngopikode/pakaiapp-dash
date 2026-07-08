# Spesifikasi API Tenant — Pakaiapp POS

Dokumen ini mendefinisikan seluruh *endpoint* REST API yang berjalan di bawah domain Tenant (kasir/operasional toko). API ini dikonsumsi oleh aplikasi QR Menu Pelanggan (B2C) maupun integrasi eksternal lainnya.

---

## 📌 Ketentuan Umum
*   **Base URL**: `http://{tenant-subdomain}.pakaiapp.online/api` (atau `http://{tenant-subdomain}.pakaiapp.test/api` di lingkungan lokal).
*   **Response Envelope**: Semua respons menggunakan standardisasi dari `ApiResponserTrait` dengan format JSON:
    *   **Success Response (2xx)**:
        ```json
        {
            "success": true,
            "data": { ... },
            "message": "Pesan deskriptif"
        }
        ```
    *   **Error Response (4xx/5xx)**:
        ```json
        {
            "success": false,
            "message": "Pesan kesalahan",
            "errors": [ ... ]
        }
        ```

---

## 🌐 Endpoints

### 1. Dapatkan Profil Toko
Mengambil informasi profil, tema, pajak, biaya layanan, dan elemen SEO toko/restoran.

*   **URL**: `/restaurant`
*   **Method**: `GET`
*   **Auth Required**: No
*   **Response (200 OK)**:
    ```json
    {
        "success": true,
        "data": {
            "id": "toko1",
            "name": "Kafienia Coffee",
            "logo": "http://toko1.pakaiapp.test/storage/logos/toko1.png",
            "theme_color": "#1565c0",
            "whatsapp_number": "6281234567890",
            "address": "Jl. Kopi No. 12, Jakarta",
            "is_tax_active": true,
            "tax_rate": 10.0,
            "is_service_charge_active": true,
            "service_charge_rate": 5.0,
            "hero": {
                "promo_text": "Beli 1 Gratis 1 Kopi Susu",
                "status_text": "Buka",
                "headline": "Ngopi Tenang di Kafienia",
                "tagline": "Premium Coffee Blend",
                "instagram_url": "https://instagram.com/kafienia"
            },
            "navbar": {
                "brand_text": "Kafienia",
                "title": "Kafienia Coffee",
                "subtitle": "Order online dari meja"
            },
            "seo": {
                "title": "Kafienia Coffee - POS Online",
                "description": "Menu digital dan kasir online Kafienia",
                "keywords": "kopi, cafe, jakarta",
                "og_title": "Kafienia Coffee Menu",
                "og_description": "Menu digital online",
                "og_image": "http://toko1.pakaiapp.test/storage/seo/og-toko1.png"
            }
        },
        "message": "Request processed successfully."
    }
    ```

---

### 2. Kirim Pesanan Baru (Checkout)
Mengirimkan transaksi checkout pelanggan dari QR Menu. Mendukung pembayaran tunai (Manual), Midtrans (Digital), dan Duitku (Digital).

*   **URL**: `/orders`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Middleware**: `throttle:orders` (Rate Limiting)
*   **Headers**: `Content-Type: application/json`
*   **Request Body (JSON)**:
    ```json
    {
        "customer_name": "Budi Utomo",
        "customer_email": "budi@email.com",
        "customer_phone": "62899999999",
        "order_type": "dinein",
        "order_info": "Meja 04",
        "total_price": 38000,
        "payment_method": "digital",
        "items": [
            {
                "product_id": 1,
                "variant_id": 2,
                "variant_ids": [2],
                "extra_ids": [5],
                "name": "Kopi Susu Gula Aren",
                "quantity": 2,
                "price": 19000
            }
        ]
    }
    ```
    *   `customer_email`: Wajib diisi jika `payment_method` adalah pembayaran online (`digital` / Duitku channel).
    *   `order_type`: Enum `['retail', 'dinein', 'takeaway', 'online', 'delivery']`. Jika tidak valid, default ke `retail`.
    *   `order_info`: Nomor meja (jika `order_type` = `dinein`) atau catatan tambahan (jika `order_type` selain `dinein`).
    *   `payment_method`: Tipe pembayaran. Nilai `digital` akan merujuk ke Midtrans. Nilai spesifik Duitku (seperti `QRIS`, `NQ`, `SP`, `LQ`, `GQ`) akan merujuk ke Duitku. Nilai `CASH` atau `MANUAL` merujuk ke kasir manual.

*   **Response - Cash/Manual (201 Created)**:
    ```json
    {
        "success": true,
        "data": {
            "id": 45,
            "invoice_code": "INV-20260708-AB12CD",
            "customer_name": "Budi Utomo",
            "status": "pending",
            "total_price": 38000
        },
        "message": "Order created successfully."
    }
    ```

*   **Response - Midtrans Digital (201 Created)**:
    Mengembalikan token Snap Midtrans untuk merender interface pop-up pembayaran di sisi client.
    ```json
    {
        "success": true,
        "data": {
            "order_id": 45,
            "invoice_code": "INV-20260708-AB12CD",
            "snap_token": "887a0dbd-1b33-4f9e-ad38-a2df6a0ad89c"
        },
        "message": "Order berhasil dibuat."
    }
    ```

*   **Response - Duitku Digital (201 Created)**:
    Mengembalikan data rujukan Duitku untuk dialihkan ke halaman pembayaran eksternal.
    ```json
    {
        "success": true,
        "data": {
            "order_id": 45,
            "invoice_code": "INV-20260708-AB12CD",
            "payment_url": "https://sandbox.duitku.com/webback/checkout?reference=...",
            "va_number": "1234567890",
            "reference": "DK12345678"
        },
        "message": "Order berhasil dibuat."
    }
    ```

---

### 3. Riwayat Transaksi (History)
Mengambil daftar riwayat transaksi berdasarkan kumpulan kode invoice yang dikirimkan. Berguna untuk sinkronisasi riwayat lokal di browser pelanggan (*localStorage*).

*   **URL**: `/orders/history`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Middleware**: `throttle:30,1` (Maksimal 30 request per menit)
*   **Request Body (JSON)**:
    ```json
    {
        "invoices": [
            "INV-20260708-AB12CD",
            "INV-20260707-XY98ZW"
        ]
    }
    ```
    *   Maksimal jumlah kode invoice dibatasi hingga **50 item** per request untuk mencegah spam payload.

*   **Response (200 OK)**:
    ```json
    {
        "success": true,
        "data": [
            {
                "invoiceCode": "INV-20260708-AB12CD",
                "date": "2026-07-08T16:03:45+07:00",
                "totalRaw": 38000,
                "orderType": "dinein",
                "status": "pending",
                "paymentMethod": "transfer",
                "paymentName": "Transfer Bank",
                "items": [
                    {
                        "name": "Kopi Susu Gula Aren",
                        "qty": 2,
                        "price": 19000
                    }
                ]
            }
        ],
        "message": "Request processed successfully."
    }
    ```

---

### 4. Dapatkan Saluran Pembayaran Duitku
Mengambil daftar metode pembayaran yang aktif dan didukung oleh Duitku beserta perkiraan biayanya (fee) berdasarkan nilai nominal transaksi.

*   **URL**: `/duitku/payment-methods`
*   **Method**: `GET`
*   **Auth Required**: No
*   **Request Query Parameters**:
    *   `amount` (Required): Angka nominal transaksi (misal: `amount=38000`).

*   **Response (200 OK)**:
    ```json
    {
        "success": true,
        "data": [
            {
                "paymentMethod": "VC",
                "paymentName": "Visa/Mastercard",
                "paymentImage": "https://duitku.com/images/visa-mastercard.png",
                "totalFee": 2000
            },
            {
                "paymentMethod": "SP",
                "paymentName": "ShopeePay",
                "paymentImage": "https://duitku.com/images/shopeepay.png",
                "totalFee": 500
            }
        ],
        "message": "Request processed successfully."
    }
    ```
