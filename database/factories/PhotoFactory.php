<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Photo> */
class PhotoFactory extends Factory
{
    public function definition(): array
    {
        return ['photo_album_id' => PhotoAlbum::factory(), 'image' => 'gallery/'.str_repeat('a', 40).'.jpg', 'alt_text' => 'Test photograph', 'is_published' => false, 'is_featured' => false, 'sort_order' => 0];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
