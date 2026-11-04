<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Article> */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return ['title' => 'Synthetic test article', 'slug' => fake()->unique()->slug(4), 'news_category_id' => NewsCategory::factory(),
            'excerpt' => 'Synthetic excerpt for tests only.', 'body' => "Synthetic article paragraph.\n\nAnother test paragraph.",
            'publication_date' => today()->format('Y-m-d'), 'status' => 'draft', 'is_featured' => false, 'sort_order' => 0];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'published']);
    }
}
