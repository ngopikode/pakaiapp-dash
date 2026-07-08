# ADR-0003: Absolute Variant Pricing Rule

## Status
Accepted

## Tanggal
2026-07-08

## Konteks
Aplikasi Pakaiapp POS dirancang untuk mendukung industri retail (misalnya toko pakaian) dan F&B (restoran/kafe) yang membutuhkan tingkat fleksibilitas produk yang tinggi. Suatu produk (misal: "Kopi Susu" atau "Kaos Polos") sering kali memiliki beberapa varian ukuran (S, M, L, XL) atau tipe kemasan yang memiliki harga berbeda.

Jika kolom harga diletakkan di tabel `products`, akan terjadi duplikasi data atau keterbatasan model saat menangani variasi harga.

## Keputusan
Menetapkan aturan **Absolute Variant Pricing**:
1.  Tabel master `products` **DILARANG** memiliki kolom harga (`price`).
2.  Semua entitas harga wajib disimpan secara eksklusif di dalam tabel `product_variants` pada kolom `price`.
3.  Setiap produk setidaknya harus memiliki satu varian default (misalnya varian "Regular" atau "All Size" atau "Default") agar harganya tetap terasosiasi ke varian tersebut.

## Alternatif yang Dipertimbangkan

### Menyimpan harga default di tabel `products` dan harga kustom di `product_variants`
*   **Kelebihan**: Memudahkan query cepat untuk menampilkan harga terendah produk.
*   **Kekurangan**: Terjadi redundansi data (*single source of truth* terlanggar). Logika perhitungan kasir dan diskon menjadi lebih kompleks karena harus mengecek dua tempat berbeda.
*   **Alasan Penolakan**: Melanggar prinsip integritas data dan menyulitkan sistem AI dalam melakukan *Dynamic Pricing Override* secara otonom tanpa merusak harga dasar.

## Konsekuensi
1.  **Konsistensi Transaksi**: Logika kasir (POS), struk belanja, dan modul dapur hanya perlu membaca tabel `product_variants` untuk mendapatkan harga dasar.
2.  **Kemudahan AI Engine**: Fitur diskon otonom AI (Happy Hour, Weekend Boost) cukup menyimpan harga *override* sementara di tabel pivot relasi aturan AI (`ai_rule_variants`), tanpa pernah menyentuh atau merusak harga dasar (`price`) produk.
3.  **Visualisasi Catalog**: Query untuk menampilkan produk di catalog frontend harus melakukan *join* ke tabel `product_variants` untuk mendapatkan harga awal (misal: `"Mulai dari Rp " . $product->variants->min('price')`).
