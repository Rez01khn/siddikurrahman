<?php

namespace Database\Factories;

use App\Models\Tournament;
use App\Models\TournamentResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TournamentResult> */
class TournamentResultFactory extends Factory
{
    public function definition(): array
    {
        return ['tournament_id' => Tournament::factory(), 'status' => 'pending',
            'source_url' => 'https://example.com/test-scorecard', 'notes' => 'Synthetic result for tests only.',
            'is_published' => false];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
