# Standard Visual Sidebar & Navbar

Dokumen ini menetapkan standar tampilan sidebar dan navbar agar konsisten antara
halaman back-office (Dashboard) dan POS, baik di light maupun dark mode.

## Prinsip Konsistensi

- Sidebar, navbar, dan body harus memakai **palet warna yang sama**.
- Warna aksen utama seluruh aplikasi = **Orange** (`orange-500` = `#F97316`).
- Elemen interaktif (item aktif, hover) memakai warna aksen yang sama di semua
  tema. Hindari campur aksen (mis. emerald di satu tempat, orange di tempat lain).

## Nilai Standar

| Elemen | Light mode | Dark mode |
|--------|------------|-----------|
| Background sidebar (desktop) | `bg-white/85` | `dark:bg-[#0B1120]/85` |
| Background drawer mobile | `bg-white` | `dark:bg-[#0B1120]` |
| Border pemisah | `border-slate-200/60` | `dark:border-slate-800/60` |
| Brand font header | `font-sans font-black` | (sama) |
| Item sidebar aktif | `bg-orange-500/10 text-orange-600` | `dark:bg-orange-500/15 dark:text-orange-400` |
| Indikator aktif (dot) | `bg-orange-500` + glow `rgba(249,115,22,0.6)` | (sama) |

> Background dark mode aplikasi mengikat ke `#0B1120` (bukan `slate-900`).
> Jangan gunakan `slate-900` sebagai background permukaan di dark mode karena
> akan terlihat "beda alam" dari navbar dan body.

## File Terkait

- `resources/views/layouts/app.blade.php` — wrapper sidebar desktop + drawer mobile
- `resources/views/components/layouts/⚡sidebar/sidebar.blade.php` — header brand + menu
- `resources/views/components/layouts/sidebar-item.blade.php` — item nav (active state)
- `resources/css/app.css` — class util `.sidebar-item-active` (sudah diset orange)

## Catatan

- `.sidebar-item-active` di `app.css` sengaja diset orange agar satu sumber
  kebenaran warna aktif, menyelaraskan dengan inline class di `sidebar-item.blade.php`.
- Jika suatu saat aksen diubah (mis. ke emerald), ubah **semua** referensi di atas
  sekaligus, jangan hanya di satu file.
