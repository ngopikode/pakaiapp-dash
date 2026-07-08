# ADR-0002: Service Injection Guidelines

## Status
Accepted

## Tanggal
2026-07-08

## Konteks
Ketika menulis Controller, Livewire Component, atau Service dalam aplikasi Laravel, kita sering kali perlu menyuntikkan (inject) dependensi ke kelas lain. Laravel menyediakan service container untuk menyelesaikannya. Namun, tanpa aturan yang disepakati, developer sering mencampuradukkan pola injeksi:
1.  Injeksi via parameter method
2.  Constructor property promotion
3.  Memanggil helper `app(Service::class)` berulang kali secara inline

Pola yang tidak konsisten membuat kode sulit diuji (untestable) dan menurunkan keterbacaan serta performa aplikasi.

## Keputusan
Menerapkan dua pola standardisasi injeksi dependensi:

### 1. Constructor Property Promotion (Banyak Penggunaan)
Jika dependensi/service tersebut digunakan oleh **hampir semua** atau **seluruh** method di dalam kelas tersebut, gunakan *Constructor Property Promotion*:
```php
public function __construct(
    protected readonly TargetService $targetService
) {}
```

### 2. Lazy Initialization (Sedikit Penggunaan)
Jika dependensi/service tersebut hanya digunakan oleh **satu atau beberapa** method tertentu saja (di mana pembuatan instansiasi service di setiap request akan membuang memori/sumber daya), gunakan pola *Lazy Initialization* dengan properti nullable dan getter method. **DILARANG** memanggil helper `app(TargetService::class)` secara inline berulang kali.
```php
protected ?TargetService $targetService = null;

protected function targetService(): TargetService
{
    return $this->targetService ??= app(TargetService::class);
}
```

## Konsekuensi
1.  **Konsistensi**: Kode menjadi lebih terstruktur dan seragam di seluruh proyek.
2.  **Efisiensi Memori**: Pola Lazy Initialization menghemat memori karena service pendukung tidak akan diinstansiasi jika method yang bersangkutan tidak dipanggil.
3.  **Kemudahan Testing**: Pola ini memudahkan pembuatan unit/feature test karena dependensi dapat dengan mudah di-*mock* menggunakan `bind()` atau `instance()` pada container Laravel.
