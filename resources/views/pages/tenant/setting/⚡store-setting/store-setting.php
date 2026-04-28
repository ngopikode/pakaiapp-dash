<?php

use App\Models\StoreSetting;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    // Properti penampung data
    public int $settingId;
    public string $name = '';
    public string $theme_color = '#f59e0b';
    public ?string $whatsapp_number = '';
    public ?string $address = '';
    public bool $is_active = true;

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
