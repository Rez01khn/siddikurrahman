<?php

namespace Database\Factories;

use App\Models\Sponsor;
use Illuminate\Database\Eloquent\Factories\Factory;

class SponsorFactory extends Factory
{
    protected $model = Sponsor::class;

    public function definition(): array
    {
        return ['name' => 'Synthetic sponsor '.fake()->unique()->numerify('######'), 'slug' => fake()->unique()->slug(), 'category' => 'Test category', 'description' => 'Synthetic test fixture, not an actual partnership.', 'is_published' => true, 'is_featured' => false, 'sort_order' => 0];
    }
}
