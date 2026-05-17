<?php

use App\Models\StoreSetting;
use Livewire\Component;

new class extends Component {
    public string $storeType = 'resto';

    public function mount(): void
    {
        $setting = StoreSetting::first();
        if ($setting) $this->storeType = $setting->store_type;
    }
};
