<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
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
        'created_at',
        'updated_at'
    ];
}
