<?php

use App\Models\StoreSetting;
use Livewire\Component;

new class extends Component {

    public function mount(): void
    {
        $storeType = StoreSetting::first()?->store_type ?? 'retail';

        if ($storeType === 'resto') {
            $this->redirect(route('cashier.resto'), navigate: true);
        } else {
            $this->redirect(route('cashier.retail'), navigate: true);
        }
    }
};
