# Plan: Sesuaikan skeleton wallet page

## Target file
- `resources/views/pages/tenant/payment/⚡wallet/wallet.blade.php`

## Masalah utama
- Skeleton `wallet-overview` belum mirror struktur aktual `walletOverview`: card kiri harus `min-h-[170px]`, gradient/dark-card feel, dan kanan 2 kartu cashflow dengan tinggi/spacing sama.
- Skeleton `tx-section` table/chart masih kasar: header/search/row/chart tidak match konten aktual, sehingga loading layout bergeser.
- Skeleton `tx-list` punya typo class `dark:bg-slate-850` yang tidak valid.

## Keputusan
- Tidak tambah component/helper baru. Edit Blade ini saja.
- Skeleton harus meniru layout aktual, bukan desain baru.
- Pakai Tailwind class yang sudah dipakai di file; tidak tambah dependency atau JS.

## Langkah implementasi
1. Update `wallet-overview` placeholder:
   - Pertahankan wrapper `flex flex-col md:flex-row gap-6`.
   - Samakan card kiri dengan konten aktual: `w-full md:w-3/5`, `rounded-3xl`, `p-6 md:p-8`, `min-h-[170px]`, `relative overflow-hidden`, border/shadow/dark background.
   - Samakan skeleton row dalam card: label kecil, angka besar, chip kanan, bottom owner/account row.
   - Samakan card kanan dengan konten aktual: `w-full md:w-2/5 flex flex-col sm:flex-row gap-4`, tiap card `p-6 rounded-3xl ... flex flex-col justify-between gap-6`.

2. Update `tx-section` placeholder:
   - Samakan wrapper `flex flex-col-reverse md:flex-row gap-6 items-start`.
   - Card kiri harus mirror aktual: `w-full md:w-[65%] dash-card p-0 flex flex-col overflow-hidden`.
   - Header skeleton mirror filter buttons dan sort button ukuran aktual.
   - Search skeleton mirror `px-4 py-3` dan input `h-10 rounded-xl`.
   - Row skeleton mirror actual unified row: icon `w-12 h-12`, left text 3 lines, right nominal + badge; pakai `p-4`, border bottom.
   - Card kanan mirror actual analytics: `w-full md:w-[35%] dash-card p-6 flex flex-col bg-card relative overflow-hidden`, header, donut `w-56 h-56`, legend 3 item `p-4 rounded-2xl`.

3. Update `tx-list` placeholder:
   - Samakan row skeleton dengan actual transaction row.
   - Ganti `dark:bg-slate-850` menjadi class valid, misalnya `dark:bg-slate-800`.

4. Hapus komentar berlebihan kalau menyentuh blok skeleton, tapi jangan refactor area lain.

## Validasi
- Jalankan Blade/PHP lint paling ringan jika tersedia:
  - `php -l resources/views/pages/tenant/payment/⚡wallet/wallet.blade.php` kemungkinan tidak valid untuk Blade penuh; jika gagal karena Blade syntax, abaikan sebagai bukan checker tepat.
- Cari command project:
  - cek `composer.json` / `package.json` untuk `lint`, `test`, atau format command.
- Manual visual check:
  - reload wallet page dengan network throttling atau island defer loading.
  - Pastikan skeleton tidak shift saat konten muncul pada mobile dan desktop.
  - Pastikan dark mode skeleton class valid.

## Out of scope
- Ubah actual UI wallet.
- Tambah reusable skeleton component.
- Ubah island behavior atau data query.
