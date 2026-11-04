<?php

namespace Database\Factories;

use App\Models\CareerMilestone;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CareerMilestone> */
class CareerMilestoneFactory extends Factory
{
    public function definition(): array
    {
        return ['title' => 'Test milestone', 'year' => 2013, 'description' => 'Synthetic milestone for tests only.', 'source_note' => 'Test source', 'sort_order' => 0, 'is_published' => false];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
