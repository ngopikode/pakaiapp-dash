<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Tenant\Models\Resto\ProductExtra;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[ObservedBy(ProductObserver::class)]
#[Fillable([
    'id',
    'category_id',
    'name',
    'description',
    'image',
    'tax_included',
    'has_variants',
    'is_active',
    'selection_type',
    'max_selections',
    'created_at',
    'updated_at',
])]
class Product extends Model
{
    use \App\Shared\Traits\ClearsAiMenuCache;

    protected function casts(): array
    {
        return [
            'has_variants' => 'boolean',
            'is_active' => 'boolean',
            'tax_included' => 'boolean',
            'max_selections' => 'integer',
        ];
    }

    public function getRouteKey(): string
    {
        // Hasilnya: nasi-goreng-123
        // Ini nggak nambah kolom, cuma ngolah string di memori pas generate link
        return Str::slug($this->name) . '-' . $this->id;
    }

    public function resolveRouteBinding($value, $field = null): Model|Product|null
    {
        // Ambil angka terakhir (ID) dari string
        $id = (int)last(explode('-', $value));

        // Tetep cari pake ID, jadi database lu gak bakal keberatan
        return $this->where('id', $id)->firstOrFail();
    }

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

    /**
     * Format array khusus untuk frontend (AlpineJS).
     * Mencegah duplikasi format data antara halaman katalog dan detail produk.
     */
    public function toFrontendArray(): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => $this->description,
            'image'           => $this->image ? \Illuminate\Support\Facades\Storage::url($this->image) : null,
            'price'           => $this->price,
            'active_discount_price' => $this->variants->min('active_discount_price'),
            'active_discount_name'  => $this->variants->firstWhere('active_discount_name', '!=', null)?->active_discount_name,
            'formatted_price' => $this->formatted_price,
            'category'        => $this->category?->name ?? '',
            'is_active'       => $this->is_active,
            'has_variants'    => $this->has_variants,
            'selection_type'  => $this->selection_type ?? 'single',
            'max_selections'  => $this->max_selections ?? 1,
            'default_variant_id' => $this->variants->firstWhere('name', 'Default')?->id ?? $this->variants->first()?->id,
            'variants'        => $this->variants->map(fn ($v) => [
                'id'    => $v->id,
                'name'  => $v->name,
                'price' => $v->price,
                'active_discount_price' => $v->active_discount_price,
                'active_discount_name'  => $v->active_discount_name,
                'stock' => $v->stock
            ])->toArray(),
            'extras'          => $this->extras->where('is_active', true)->map(fn ($e) => [
                'id'    => $e->id,
                'name'  => $e->name,
                'price' => $e->price,
            ])->values()->toArray(),
        ];
    }
}
