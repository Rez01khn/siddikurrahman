<?php

namespace Database\Factories;

use App\Models\PhotoAlbum;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PhotoAlbum> */
class PhotoAlbumFactory extends Factory
{
    public function definition(): array
    {
        return ['title' => 'Test Album', 'slug' => fake()->unique()->slug(3), 'is_published' => false, 'is_featured' => false, 'sort_order' => 0];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
