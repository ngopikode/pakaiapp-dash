# MFC + Alpine Timing Architecture di Livewire 4

Dalam pengembangan komponen Livewire 4 yang menggunakan pendekatan **Multi-File Component (MFC)** (memisahkan `.php`, `.blade.php`, dan `.js`), seringkali kita memindahkan logika JavaScript ke dalam file `.js` eksternal agar kode lebih rapi.

Namun, ada satu kendala krusial terkait **timing eksekusi asinkron** saat menggabungkan file `.js` eksternal ini dengan Alpine.js. 

---

## ⚠️ Masalah: Kenapa `Alpine.data()` Sering Gagal di File `.js` Eksternal?

Jika Anda mendaftarkan `Alpine.data('myComponent', ...)` di dalam file `.js` eksternal yang di-load oleh Livewire (terutama saat menggunakan `wire:navigate` atau *asset bundling* seperti Vite), Anda mungkin sering melihat error di konsol:

> `Alpine Warning: Unable to find component [myComponent]`

### Penyebabnya:
1. **Asinkronus:** File `.js` eksternal di-load secara *asynchronous* (tidak memblokir render halaman).
2. **Race Condition:** Seringkali, **Alpine.js selesai melakukan inisialisasi / *booting*** pada struktur DOM HTML **sebelum** file `.js` eksternal Anda selesai di-download dan dieksekusi.
3. Karena Alpine sudah membaca tag `x-data="myComponent"` di HTML tetapi definisi `myComponent` belum ada di memori, Alpine akan memunculkan *warning* dan fitur interaktif Anda tidak akan berjalan.

---

## ✅ Solusi: Gunakan `@script` di dalam file `.blade.php`

Untuk mengatasi *race condition* ini, **pendaftaran `Alpine.data()` harus tetap berada di dalam file `.blade.php`** menggunakan direktif `@script` bawaan Livewire.

### Jangan lakukan ini (di file `.js` eksternal):
```javascript
// file: my-component.js (SALAH ❌)
document.addEventListener('alpine:init', () => {
    Alpine.data('myComponent', () => ({
        open: false,
        toggle() { this.open = !this.open }
    }))
})
```

### Lakukan ini (di file `.blade.php`):
```blade
<!-- file: my-component.blade.php (BENAR ✅) -->
<div x-data="myComponent">
    <button @click="toggle">Buka</button>
    <div x-show="open">Konten...</div>
</div>

@script
<script>
    Alpine.data('myComponent', () => ({
        open: false,
        toggle() { this.open = !this.open }
    }))
</script>
@endscript
```

### Mengapa ini berhasil?
Direktif `@script` memastikan bahwa blok *script* tersebut di-injeksi dan dieksekusi secara sinkron dan tepat waktu oleh siklus hidup Livewire, **sebelum** Alpine melakukan evaluasi struktur DOM untuk komponen tersebut. 

### Kapan file `.js` eksternal boleh digunakan?
File `.js` eksternal tetap sangat berguna di Livewire 4, namun gunakanlah hanya untuk:
- Mengimpor *library* pihak ketiga (Chart.js, Swiper, dsb).
- Mendeklarasikan fungsi global, *helper*, atau *class* murni.
- Mendengarkan *event listener* global (`window.addEventListener`).

**Kesimpulan Utama:**  
Pisahkan *logic* JavaScript yang berat ke file `.js` eksternal, tetapi **selalu pertahankan registrasi state Alpine (`Alpine.data`) di dalam *blade* menggunakan `@script`**.
