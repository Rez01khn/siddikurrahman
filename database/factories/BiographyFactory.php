<?php

namespace Database\Factories;

use App\Models\Biography;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Biography> */
class BiographyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => 1, 'full_name' => 'Test Athlete', 'professional_title' => 'Test professional golfer',
            'short_introduction' => 'Synthetic biography introduction for automated tests.',
            'full_biography' => "Synthetic first paragraph.\n\nSynthetic second paragraph.",
            'source_note' => 'Synthetic verification note for tests only.', 'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
