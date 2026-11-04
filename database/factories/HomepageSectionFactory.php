<?php

namespace Database\Factories;

use App\Models\HomepageSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<HomepageSection> */
class HomepageSectionFactory extends Factory
{
    public function definition(): array
    {
        return config('homepage.hero.defaults');
    }

    public function section(string $key): static
    {
        return $this->state(fn (array $attributes) => config('homepage.'.$key.'.defaults'));
    }
}
