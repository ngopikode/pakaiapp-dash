# Rencana Migrasi Tenant Symlinks & Storage

## 1. Konteks dan Masalah
Saat ini, symlink untuk tenant public dan folder storage menggunakan konfigurasi bawaan `stancl/tenancy` dengan `suffix_base` bernilai `tenant_`. 
Hal ini menyebabkan:
- Symlink bertebaran di root direktori `/public` (contoh: `public/tenant_toko-aliang`).
- Saat proses registrasi tenant secara online, web server (`www-data`) gagal membuat symlink secara otomatis karena folder `/public` sepenuhnya dimiliki oleh user `rwr`.
- Jika folder `/public` diubah kepemilikannya ke `www-data` seluruhnya, maka user `rwr` akan mendapatkan pesan error (Permission Denied) saat menjalankan proses build Vite/NPM (`npm run build`).

## 2. Rencana Solusi (Migrasi ke /tenants/)
Untuk memisahkan akses symlink tanpa mengganggu folder build milik `rwr`, kita akan memindahkan lokasi penyimpanan storage dan symlink ke dalam sub-folder `/tenants/`.

- **Konfigurasi Baru:** Mengubah `suffix_base` di `config/tenancy.php` menjadi `tenants/`.
- **Dampak Storage:** Direktori penyimpanan setiap tenant akan pindah dari `storage/tenant_{id}` menjadi `storage/tenants/{id}`.
- **Dampak Symlink:** Symlink public akan pindah dari `public/tenant_{id}` menjadi `public/tenants/{id}`.

## 3. Analisis Dampak (Impact Analysis)

### ✅ Yang AMAN (Tidak Terpengaruh)
1. **Database:** Struktur dan nama database tenant (contoh: `pakaiapp_tenant_tokoaliang`) **TIDAK AKAN BERUBAH** atau rusak. Konfigurasi nama database diatur terpisah melalui `database.prefix` (`pakaiapp_tenant_`).
2. **Proses Build NPM:** Karena symlink kini terisolasi di dalam `public/tenants/`, user `rwr` dapat leluasa melakukan `npm run build` di folder root `/public` tanpa gangguan masalah kepemilikan file.
3. **Kode Aplikasi (Helper):** Karena kita menggunakan perubahan konfigurasi bawaan `suffix_base`, semua fungsi helper bawaan seperti `Storage::url()` atau `$tenant->run()` akan otomatis membaca jalur/path yang baru tanpa perlu modifikasi kode PHP sama sekali.

### ⚠️ Yang HARUS DIMIGRASI (Downtime/Breaking jika tidak dipindahkan)
Jika `suffix_base` diubah tanpa memindahkan file fisik, maka:
- Semua gambar (logo, hero image, produk) milik tenant lama akan menjadi *Broken Link* (gagal muat) karena Laravel akan mencarinya di `public/tenants/` sedangkan file aslinya masih di `public/tenant_`.
- Oleh karena itu, skrip migrasi direktori (Move Directory) wajib dijalankan.

## 4. Langkah Migrasi Terperinci (Action Plan)

1. **Ubah Konfigurasi:**
   Update `config/tenancy.php` -> ubah `'suffix_base' => 'tenant_'` menjadi `'suffix_base' => 'tenants/'`.

2. **Buat Direktori Penampung Baru (Terminal):**
   ```bash
   mkdir -p /var/www/pakaiapp-dash/storage/tenants
   mkdir -p /var/www/pakaiapp-dash/public/tenants
   ```

3. **Migrasi Folder Storage (Data Asli Tenant Lama):**
   ```bash
   cd /var/www/pakaiapp-dash/storage
   for d in tenant_*; do
     if [ -d "$d" ]; then
       tenant_id="${d#tenant_}"
       mv "$d" "tenants/$tenant_id"
     fi
   done
   ```

4. **Bersihkan Symlink Lama dan Buat Ulang:**
   Hapus semua symlink usang yang berawalan `tenant_` di folder `public/`.
   Jalankan Artisan Command bawaan sistem untuk merakit ulang seluruh direktori dan symlink tenant menggunakan path yang baru (tenants/):
   ```bash
   php artisan tenant:symlink
   ```

5. **Penyesuaian Hak Akses Folder Symlink (Tugas Server Admin):**
   Karena sub-folder `/tenants` sekarang menjadi lokasi sentral untuk penulisan symlink saat registrasi, kita cukup menugaskan *ownership*-nya ke `www-data`:
   ```bash
   sudo chown -R www-data:www-data /var/www/pakaiapp-dash/public/tenants
   ```
