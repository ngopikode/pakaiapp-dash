<?php

namespace Database\Factories\Tenant\Core;

use App\Tenant\Models\Core\DeliverySlot;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliverySlotFactory extends Factory
{
    protected $model = DeliverySlot::class;

    public function definition(): array
    {
        return [
            'name' => 'Pagi (' . $this->faker->time('H:i') . ' - ' . $this->faker->time('H:i') . ')',
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
            'max_orders' => $this->faker->numberBetween(10, 50),
            'is_active' => true,
        ];
    }
}
