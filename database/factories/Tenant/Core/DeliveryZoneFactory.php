<?php

namespace Database\Factories\Tenant\Core;

use App\Tenant\Models\Core\DeliveryZone;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryZoneFactory extends Factory
{
    protected $model = DeliveryZone::class;

    public function definition(): array
    {
        return [
            'name' => 'Zona ' . $this->faker->city,
            'shipping_cost' => $this->faker->randomElement([5000, 10000, 15000]),
            'min_free_shipping' => $this->faker->randomElement([0, 50000, 100000]),
            'is_active' => true,
        ];
    }
}
