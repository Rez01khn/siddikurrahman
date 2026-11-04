<?php

namespace Database\Factories;

use App\Models\SocialWork;
use App\Models\SocialWorkImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SocialWorkImageFactory extends Factory
{
    protected $model = SocialWorkImage::class;

    public function definition(): array
    {
        return ['social_work_id' => SocialWork::factory(), 'image' => 'community/'.Str::random(40).'.jpg', 'alt_text' => 'Synthetic project photograph', 'sort_order' => 0];
    }
}
