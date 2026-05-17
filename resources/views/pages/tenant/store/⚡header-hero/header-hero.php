<?php

use Livewire\Component;
use App\Models\StoreSetting;

new class extends Component
{
    public $promoText = 'Promo';
    public $statusText = 'Buka Sekarang';
    public $headlineParts = ['Enjoy', 'Dine'];
    public $tagline = 'Nikmati menu spesial kami.';
    public $instagramUrl = '#';
    public $address = '';

    public function mount()
    {
        $store = StoreSetting::first();
        if ($store) {
            $this->promoText = $store->hero_promo_text ?? 'Promo';
            $this->statusText = $store->hero_status_text ?? 'Buka Sekarang';
            $this->tagline = $store->hero_tagline ?? 'Nikmati menu spesial kami.';
            $this->instagramUrl = $store->hero_instagram_url ?? '#';
            $this->address = $store->address ?? '';

            $headline = $store->hero_headline ?? 'Enjoy & Dine';
            $this->headlineParts = array_map('trim', explode('&', $headline));
        }
    }
};
