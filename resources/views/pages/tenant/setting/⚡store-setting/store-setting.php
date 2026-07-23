<?php

use App\Tenant\Models\Core\StoreSetting;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title("Pengaturan Toko")]
class extends Component {
    use WithFileUploads;

    // Properti penampung data
    public int $settingId;
    public string $name = '';
    public string $theme_color = '#f59e0b';
    public ?string $whatsapp_number = '';
    public ?string $address = '';
    public bool $is_active = true;
    public string $store_type = 'resto';
    public bool $is_dinein_active = true;
    public bool $is_takeaway_active = true;
    public bool $is_delivery_active = true;
    public bool $is_tax_active = true;
    public float $tax_rate = 10.00;
    public bool $is_service_charge_active = true;
    public float $service_charge_rate = 5.00;
    public bool $is_kitchen_active = true;

    // Foto lama
    public $logo;
    public $og_image;

    // Foto baru (upload)
    public $new_logo;
    public $new_og_image;

    // Hero & Navbar
    public $hero_promo_text = 'Promo';
    public $hero_status_text = 'Buka Sekarang';
    public $hero_headline = 'Enjoy & Dine';
    public $hero_tagline = 'Nikmati menu spesial kami.';
    public $hero_instagram_url = '';
    public $navbar_brand_text = 'Ez';
    public $navbar_title = '';
    public $navbar_subtitle = 'Menu Digital';

    // SEO
    public $seo_title = '';
    public $seo_description = '';
    public $seo_keywords = '';
    public $og_title = '';
    public $og_description = '';

    public function mount()
    {
        $setting = StoreSetting::first();

        if ($setting) {
            $this->settingId = $setting->id;
            $this->name = $setting->name;
            $this->theme_color = $setting->theme_color;
            $this->whatsapp_number = $setting->whatsapp_number;
            $this->address = $setting->address;
            $this->is_active = $setting->is_active;
            $this->store_type = $setting->store_type ?? 'resto';
            $this->is_dinein_active = (bool)$setting->is_dinein_active;
            $this->is_takeaway_active = (bool)$setting->is_takeaway_active;
            $this->is_delivery_active = (bool)$setting->is_delivery_active;
            $this->is_tax_active = !isset($setting->is_tax_active) || (bool)$setting->is_tax_active;
            $this->tax_rate = isset($setting->tax_rate) ? (float)$setting->tax_rate : 10.00;
            $this->is_service_charge_active = !isset($setting->is_service_charge_active) || (bool)$setting->is_service_charge_active;
            $this->service_charge_rate = isset($setting->service_charge_rate) ? (float)$setting->service_charge_rate : 5.00;
            $this->is_kitchen_active = !isset($setting->is_kitchen_active) || (bool)$setting->is_kitchen_active;

            $this->logo = $setting->logo;
            $this->og_image = $setting->og_image;

            $this->hero_promo_text = $setting->hero_promo_text;
            $this->hero_status_text = $setting->hero_status_text;
            $this->hero_headline = $setting->hero_headline;
            $this->hero_tagline = $setting->hero_tagline;
            $this->hero_instagram_url = $setting->hero_instagram_url;

            $this->navbar_brand_text = $setting->navbar_brand_text;
            $this->navbar_title = $setting->navbar_title;
            $this->navbar_subtitle = $setting->navbar_subtitle;

            $this->seo_title = $setting->seo_title;
            $this->seo_description = $setting->seo_description;
            $this->seo_keywords = $setting->seo_keywords;
            $this->og_title = $setting->og_title;
            $this->og_description = $setting->og_description;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'theme_color' => 'required|string|max:7',
            'new_logo' => 'nullable|image|max:2048', // max 2MB
            'new_og_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $this->name,
            'theme_color' => $this->theme_color,
            'whatsapp_number' => $this->whatsapp_number,
            'address' => $this->address,
            'is_active' => $this->is_active,
            'store_type' => $this->store_type,
            'is_dinein_active' => $this->is_dinein_active,
            'is_takeaway_active' => $this->is_takeaway_active,
            'is_delivery_active' => $this->is_delivery_active,
            'is_tax_active' => $this->is_tax_active,
            'tax_rate' => $this->tax_rate,
            'is_service_charge_active' => $this->is_service_charge_active,
            'service_charge_rate' => $this->service_charge_rate,
            'is_kitchen_active' => $this->is_kitchen_active,

            'hero_promo_text' => $this->hero_promo_text,
            'hero_status_text' => $this->hero_status_text,
            'hero_headline' => $this->hero_headline,
            'hero_tagline' => $this->hero_tagline,
            'hero_instagram_url' => $this->hero_instagram_url,

            'navbar_brand_text' => $this->navbar_brand_text,
            'navbar_title' => $this->navbar_title,
            'navbar_subtitle' => $this->navbar_subtitle,

            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
        ];

        // Eksekusi upload Logo jika ada yang baru
        if ($this->new_logo) {
            if ($this->logo) Storage::disk('public')->delete($this->logo);
            $data['logo'] = $this->new_logo->store('settings', 'public');
            $this->logo = $data['logo'];
        }

        // Eksekusi upload OG Image jika ada yang baru
        if ($this->new_og_image) {
            if ($this->og_image) Storage::disk('public')->delete($this->og_image);
            $data['og_image'] = $this->new_og_image->store('settings', 'public');
            $this->og_image = $data['og_image'];
        }

        StoreSetting::updateOrCreate(
            ['id' => $this->settingId ?? 1], // Asumsi id 1 jika belum ada
            $data
        );

        $this->new_logo = null;
        $this->new_og_image = null;

        $this->dispatch('notify', message: 'Pengaturan toko berhasil disimpan!');
    }
};
