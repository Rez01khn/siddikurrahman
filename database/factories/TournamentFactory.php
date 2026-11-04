<?php

namespace Database\Factories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Tournament> */
class TournamentFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => 'Synthetic Test Tournament', 'tour' => 'Test Tour', 'venue' => 'Test Course',
            'start_date' => today()->addDays(5)->format('Y-m-d'), 'end_date' => today()->addDays(8)->format('Y-m-d'),
            'description' => 'Synthetic tournament for automated tests only.',
            'status' => 'upcoming', 'is_published' => false, 'is_featured' => false, 'sort_order' => 0];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
