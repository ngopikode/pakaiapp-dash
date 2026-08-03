<?php

namespace App\Tenant\Models\Core;

use App\Shared\Traits\ClearsStoreSettingCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'id',
    'name',
    'logo',
    'theme_color',
    'whatsapp_number',
    'address',
    'seo_title',
    'seo_description',
    'seo_keywords',
    'og_title',
    'og_description',
    'og_image',
    'hero_promo_text',
    'hero_status_text',
    'hero_headline',
    'hero_tagline',
    'hero_instagram_url',
    'navbar_brand_text',
    'navbar_title',
    'navbar_subtitle',
    'is_active',
    'store_type',
    'is_dinein_active',
    'is_takeaway_active',
    'is_delivery_active',
    'is_tax_active',
    'tax_rate',
    'is_service_charge_active',
    'service_charge_rate',
    'is_application_fee_passed',
    'is_kitchen_active',
    'is_shift_active',
    'use_same_hours',
    'operating_hours',
    'created_at',
    'updated_at',
])]
class StoreSetting extends Model
{
    use ClearsStoreSettingCache;

    protected function casts(): array
    {
        return [
            'is_shift_active' => 'boolean',
            'operating_hours' => 'array',
            'use_same_hours' => 'boolean',
        ];
    }

    /** Nama hari lowercase sesuai key JSON (Carbon: 'Monday' → 'monday') */
    private function todayKey(): string
    {
        return strtolower(Carbon::now('Asia/Jakarta')->format('l'));
    }

    /**
     * Kembalikan schedule hari ini.
     * Jika use_same_hours = true → pakai key 'default'.
     * Jika tidak ada data → anggap buka 24 jam.
     */
    public function getTodayHours(): array
    {
        $hours = $this->operating_hours ?? [];

        if ($this->use_same_hours) {
            return $hours['default'] ?? ['open' => '00:00', 'close' => '23:59', 'is_closed' => false];
        }

        $day = $this->todayKey();

        return $hours[$day] ?? $hours['default'] ?? ['open' => '00:00', 'close' => '23:59', 'is_closed' => false];
    }

    /**
     * Cek apakah toko sedang buka berdasarkan jam operasional.
     * Null operating_hours → dianggap selalu buka.
     */
    public function isOpenNow(): bool
    {
        if (empty($this->operating_hours)) {
            return true;
        }

        $today = $this->getTodayHours();

        if ($today['is_closed'] ?? false) {
            return false;
        }

        $now = Carbon::now('Asia/Jakarta');
        $open = Carbon::createFromFormat('H:i', $today['open'], 'Asia/Jakarta')->setDate($now->year, $now->month, $now->day);
        $close = Carbon::createFromFormat('H:i', $today['close'], 'Asia/Jakarta')->setDate($now->year, $now->month, $now->day);

        // Handle overnight (misal: buka 22:00 tutup 02:00)
        if ($close->lt($open)) {
            return $now->gte($open) || $now->lt($close);
        }

        return $now->between($open, $close);
    }

    public static function cached(): ?self
    {
        $attrs = Cache::rememberForever(
            'store_setting_' . tenant('id'),
            fn () => self::first()?->getAttributes()
        );

        return $attrs ? (new self)->forceFill($attrs)->syncOriginal() : null;
    }
}
