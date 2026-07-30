---
name: pakaiapp-project-context
description: Panduan arsitektur utama, tech stack, dan referensi standar pola penulisan (PATTERNS) untuk Pakaiapp POS. Wajib dibaca agen saat coding fitur aplikasi.
---

# Pakaiapp POS - Context & Patterns

Gunakan skill ini sebagai referensi arsitektur dan pola penulisan utama ketika merencanakan atau mengerjakan fitur apa pun di proyek Pakaiapp. Jika Anda membuat controller, service, atau komponen UI baru, selalu pertimbangkan pedoman ini.

## 1. Direktori Referensi Penting
Alih-alih mencari-cari secara sporadis, gunakan `read` atau `grep` pada dokumen berikut:
- **Arsitektur & Peta Proyek:** `.docs/project-map.md` (Menjelaskan struktur Multi-Tenant, perbedaan `Central/` vs `Tenant/`).
- **Standar Backend (Laravel 13):** `.docs/references/laravel13/PATTERNS.md` (Memuat aturan Thin Controller, Service Lazy Getter Injection, DTO `Spatie\LaravelData`, Single-line if, dan Named Arguments).
- **Standar Frontend (Livewire 4):** `.docs/references/livewire4/PATTERNS.md` (Memuat aturan Zero-Roundtrip UI, `$wire.$dirty()`, dan menghindari *__PHP_Incomplete_Class* saat caching model).

## 2. Aturan Emas Pengembangan (Ponytail Mode)
- **Lazy tapi Presisi:** Kerjakan dengan diff sekecil mungkin. Hapus abstraksi yang tidak diminta. 
- **Tidak Menambah Package Sembarangan:** Maksimalkan fitur native Laravel 13, Livewire 4, dan TailwindCSS.
- **Pemisahan Layer Mutlak:** Livewire Component HANYA bertugas menerima input dan mengembalikannya ke UI. Segala operasi query DB (create, update, delete, mutasi) WAJIB dieksekusi di class `Service` (contoh: `app/Tenant/Services/...`). Interaksi antar-layer wajib via `DTO`.

## 3. Eksekusi Database Transaction
Untuk menjamin integritas data (terutama pesanan, shift, dan stok), jangan gunakan closure `DB::transaction(fn() => ...)`. 
Gunakan *Pessimistic Locking* dan *Manual Transaction* (`try-catch`, `DB::beginTransaction()`, `DB::commit()`, `DB::rollBack()`).
