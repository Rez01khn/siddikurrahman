<?php

namespace Database\Factories;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Achievement> */
class AchievementFactory extends Factory
{
    public function definition(): array
    {
        return ['title' => 'Test achievement', 'category' => 'career_highlight', 'year' => 2013, 'description' => 'Synthetic achievement for tests only.', 'source_note' => 'Test source', 'sort_order' => 0, 'is_featured' => false, 'is_published' => false];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
