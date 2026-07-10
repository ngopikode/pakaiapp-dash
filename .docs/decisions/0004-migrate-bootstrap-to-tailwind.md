# ADR 0004 — Migrasi Dashboard dari Bootstrap ke Tailwind CSS 3

**Status:** Disetujui  
**Tanggal:** 2026-07-10  
**Decider:** @ngopikode

---

## Context

Dashboard tenant (halaman kasir, order, produk, pengaturan, dsb) saat ini menggunakan Bootstrap 5.3 sebagai CSS framework utama. Sementara itu, halaman toko pelanggan (`store.css`) sudah menggunakan Tailwind CSS 3 secara penuh.

Adanya dua framework CSS yang berbeda menyebabkan:
- Ketidakkonsistensian design system antara halaman admin dan halaman toko
- Bundle size lebih besar karena dua framework dimuat
- Developer experience yang tidak konsisten (dua paradigma utility class berbeda)
- Sulitnya membangun komponen baru yang konsisten

## Keputusan

Migrasi penuh dari Bootstrap 5.3 ke Tailwind CSS 3 untuk seluruh dashboard tenant, layout, dan halaman terkait.

**Keputusan spesifik yang disepakati:**

| Aspek | Keputusan |
|---|---|
| Bootstrap CSS | Dihapus sepenuhnya |
| Bootstrap JS (Modal, Offcanvas, Dropdown) | Dihapus → diganti Alpine.js |
| Bootstrap Icons (`bi bi-*`) | Dihapus → diganti Phosphor Icons (`ph-*`) |
| Dark mode | Dipertahankan, migrasi dari `data-bs-theme` ke class `dark` Tailwind |
| Pendekatan migrasi | Sekaligus (big bang), bukan bertahap |
| Halaman toko pelanggan | Tidak diubah (`store.css` tetap) |

## Konsekuensi

**Positif:**
- Satu design system yang konsisten (Tailwind) di seluruh aplikasi
- Bundle CSS lebih kecil setelah Bootstrap dihapus (~30KB gzip saved)
- Komponen baru lebih mudah dibuat dengan Tailwind utility-first
- Alpine.js sudah ada di project, tidak perlu dependency baru

**Negatif / Risiko:**
- Effort besar: estimasi ~16–22 engineer-days
- Semua tampilan dashboard akan berubah selama proses migrasi
- Potensi regresi visual di area yang tidak di-test

## Alternatif yang Ditolak

- **Migrasi bertahap per halaman** — ditolak karena ingin selesai sekaligus tanpa periode hybrid
- **Tetap Bootstrap + theming ulang** — ditolak karena tidak menyelesaikan masalah dua framework
- **Bootstrap CSS tetap, hanya ganti JS** — ditolak karena hasilnya hybrid dan tidak bersih
