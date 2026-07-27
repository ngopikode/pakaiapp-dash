# Bugfix: TypeError StoreSetting::cached() — __PHP_Incomplete_Class

> Referensi: [Operating Hours Plan](./plan.md) · [Project Map](../../project-map.md) · [Laravel 13 Upgrade Guide](../../references/laravel13/prologue/upgrade.md)

## Error

```
TypeError
app/Tenant/Models/Core/StoreSetting.php:112
App\Tenant\Models\Core\StoreSetting::cached(): Return value must be of type
?App\Tenant\Models\Core\StoreSetting, __PHP_Incomplete_Class returned
```

## Root Cause

`config/cache.php` line 128:

```php
'serializable_classes' => false,
```

**Dikonfirmasi dari docs resmi Laravel 13 Upgrade Guide** (`references/laravel13/prologue/upgrade.md`):

> The default application `cache` configuration now includes a `serializable_classes` option set to `false`. This hardens cache unserialization behavior to help prevent PHP deserialization gadget chain attacks if your application's `APP_KEY` is leaked.

Laravel 11+ menjalankan `unserialize()` dengan `allowed_classes: false`. Dengan setting ini, tidak ada PHP class yang bisa di-deserialized dari cache storage.

`StoreSetting::cached()` saat ini menyimpan **Eloquent model object** ke cache:

```php
// MASALAH: menyimpan object PHP
Cache::rememberForever('store_setting_' . tenant('id'), fn () => self::first());
```

Alur error:
1. `self::first()` → Eloquent object → disimpan ke cache via `serialize()`
2. Request berikutnya → `Cache::rememberForever()` membaca cache → `unserialize()` dijalankan dengan `allowed_classes: false`
3. PHP tidak bisa reconstruct class `StoreSetting` → mengembalikan `__PHP_Incomplete_Class`
4. Return type `?StoreSetting` menolak `__PHP_Incomplete_Class` → **TypeError**

## Keputusan Fix

**Jangan ubah `config/cache.php`.** `serializable_classes => false` adalah security setting yang benar dan direkomendasikan Laravel 13. Mengubahnya ke `true` atau allowlist akan membuka celah deserialization gadget chain attack jika `APP_KEY` bocor.

Docs Laravel 13 sendiri merekomendasikan:

> If your application previously relied on unserializing arbitrary cached objects, you will need to migrate that usage to **explicit class allow-lists** or to **non-object cache payloads (such as arrays)**.

**Fix di `StoreSetting::cached()`**: simpan **plain array attributes** (bukan object), reconstruct model saat dibaca. Array aman di-serialize tanpa dependency class.

## Fix

**File:** `app/Tenant/Models/Core/StoreSetting.php`

```php
// BEFORE — menyimpan object, pecah saat unserialize dengan allowed_classes: false
public static function cached(): ?self
{
    return Cache::rememberForever('store_setting_' . tenant('id'), fn () => self::first());
}

// AFTER — simpan array attributes, reconstruct saat dibaca
public static function cached(): ?self
{
    $attrs = Cache::rememberForever(
        'store_setting_' . tenant('id'),
        fn () => self::first()?->getAttributes()
    );

    return $attrs
        ? (new self)->forceFill($attrs)->syncOriginal()
        : null;
}
```

### Kenapa `forceFill` + `syncOriginal` aman

| Aspek | Penjelasan |
|-------|------------|
| `getAttributes()` | Mengembalikan plain PHP array — serializable tanpa class dependency, aman dengan `allowed_classes: false` |
| `forceFill($attrs)` | Mengisi semua attribute ke instance baru (bypass `$guarded`/`$fillable`) |
| `syncOriginal()` | Menyamakan `$original` dengan `$attributes` → `isDirty()` return `false`, model berperilaku seperti fresh-from-DB |
| Casts | Tetap berjalan via accessor/mutator model saat property diakses |
| Caller | Signature tetap `?StoreSetting` — tidak ada perubahan di caller manapun |
| Security | `config/cache.php` tidak perlu diubah |

### Apakah ada kelemahan?

- Relasi (eager-loaded) **tidak tersimpan** di cache — ini sudah berlaku sebelumnya dan bukan masalah karena `StoreSetting` tidak punya relasi yang di-eager-load.
- Accessor bertipe `Carbon` atau cast khusus perlu diakses lewat model (bukan raw array) — sudah terpenuhi karena kita reconstruct model.

## File yang Diubah

| File | Perubahan |
|------|-----------|
| `app/Tenant/Models/Core/StoreSetting.php` | Ganti implementasi `cached()` — simpan `getAttributes()` array, reconstruct via `forceFill` + `syncOriginal` |

**Tidak ada file lain yang diubah. Tidak ada migration. Tidak ada config change.**

---

## Fase Implementasi

### ✅ Fix `cached()`

- [x] Update `StoreSetting::cached()` sesuai fix di atas
- [x] Hapus existing cache entries (jalankan `php artisan cache:clear` atau bust manual per tenant) agar tidak ada stale data berformat lama

---

*Dibuat: 2026-07-27. Sumber: Laravel 13 Upgrade Guide `references/laravel13/prologue/upgrade.md` line 102–115.*
