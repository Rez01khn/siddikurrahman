<?php

namespace Database\Factories;

use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<NewsCategory> */
class NewsCategoryFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => 'Synthetic category', 'slug' => fake()->unique()->slug(3), 'is_published' => true, 'sort_order' => 0];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
