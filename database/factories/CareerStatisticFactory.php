<?php

namespace Database\Factories;

use App\Models\CareerStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CareerStatistic> */
class CareerStatisticFactory extends Factory
{
    public function definition(): array
    {
        return ['label' => 'Test metric', 'value' => '7', 'description' => 'Synthetic metric for tests only.', 'source_note' => 'Test source', 'sort_order' => 0, 'is_published' => false];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
