# ADR-0001: Multi-Database Tenant Isolation

## Status
Accepted

## Tanggal
2026-07-08

## Konteks
Aplikasi Pakaiapp POS dikembangkan sebagai platform Point of Sale (POS) Multi-Tenant Retail SaaS. Setiap toko (tenant) mengelola data transaksi sensitif (pesanan, stok, harga, data keuangan, data pelanggan, dll.). Ada dua opsi umum untuk mengimplementasikan multi-tenancy di Laravel:
1.  **Single-Database (Column Scoping)**: Semua data tenant berada di satu database, dipisahkan oleh kolom `tenant_id` di setiap tabel.
2.  **Multi-Database (Physical Isolation)**: Setiap tenant memiliki database fisiknya sendiri. Ada satu database pusat (central database) untuk menyimpan metadata tenant dan domain.

## Keputusan
Memilih **Multi-Database Tenancy** menggunakan package `stancl/tenancy`. Setiap tenant akan dibuatkan database MySQL terpisah saat pendaftaran selesai.

## Alternatif yang Dipertimbangkan

### Single-Database (Column Scoping)
*   **Kelebihan**: Infrastruktur sederhana, biaya hosting database awal lebih murah, migrasi database mudah (hanya satu database).
*   **Kekurangan**: Risiko kebocoran data antar-tenant sangat tinggi jika developer lupa menyertakan scope `tenant_id` pada query Eloquent. Skalabilitas database untuk satu tabel berukuran sangat besar akan menjadi bottleneck di masa depan.
*   **Alasan Penolakan**: Keamanan isolasi data dan kepercayaan merchant retail adalah prioritas utama. Risiko kebocoran data transaksi tidak dapat ditoleransi.

## Konsekuensi
1.  **Isolasi Data Penuh**: Data transaksi, produk, dan pelanggan tersimpan secara fisik di database yang terpisah untuk setiap toko, memastikan keamanan tingkat tinggi.
2.  **Manajemen Migrasi**: Proses migrasi database dibagi dua: `php artisan migrate` (untuk database central) dan `php artisan tenants:migrate-type {type}` (untuk database tenant secara tersegmentasi).
3.  **Routing Domain**: Tenant diidentifikasi secara otomatis menggunakan subdomain (misal: `toko1.pakaiapp.test`).
4.  **Kompleksitas Infrastruktur**: Server database lokal atau production harus memiliki user dengan hak akses `CREATE DATABASE` secara dinamis.
