<?php

use App\Models\StoreSetting;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component {
    public string $storeType = 'resto';

    #[Url(as: 'add_to_order')]
    public ?int $addToOrder = null;

    public function mount(): void
    {
        $setting = StoreSetting::first();
        if ($setting) $this->storeType = $setting->store_type;
    }
};
