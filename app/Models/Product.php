<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = [];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke tabel variants
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Relasi ke Ekstra/Add-ons (Opsional, khusus F&B)
    public function extras(): HasMany
    {
        return $this->hasMany(ProductExtra::class);
    }

    /**
     * Mengambil harga termurah dari seluruh varian yang ada.
     * Penggunaan di view: $product->price
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->variants->min('price') ?? 0,
        );
    }

    /**
     * Memformat tampilan harga untuk Katalog.
     * Jika punya lebih dari 1 varian, otomatis nambah kata "Mulai dari".
     * Penggunaan di view: $product->formatted_price
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                $minPrice = $this->price; // Mengambil dari accessor price() di atas

                // Format Rupiah standar
                $rupiah = 'Rp ' . number_format($minPrice, 0, ',', '.');

                // Jika varian nyala dan jumlah varian lebih dari 1
                if ($this->has_variants && $this->variants->count() > 1) {
                    return 'Mulai dari ' . $rupiah;
                }

                return $rupiah;
            }
        );
    }

    /**
     * Mengambil total stok dari semua varian.
     * Penggunaan di view: $product->total_stock
     */
    protected function totalStock(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->variants->sum('stock'),
        );
    }
}
