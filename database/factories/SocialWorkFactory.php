<?php

namespace Database\Factories;

use App\Models\SocialWork;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialWorkFactory extends Factory
{
    protected $model = SocialWork::class;

    public function definition(): array
    {
        return ['title' => 'Synthetic project '.fake()->unique()->numerify('######'), 'slug' => fake()->unique()->slug(), 'short_description' => 'Synthetic fixture introduction.', 'full_description' => 'Synthetic fixture body, not a historical activity.', 'activity_date' => '2020-05-10', 'is_published' => true, 'is_featured' => false, 'sort_order' => 0];
    }
}
