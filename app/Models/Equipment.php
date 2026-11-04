<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'slug', 'category', 'excerpt', 'description', 'image', 'image_alt', 'source_url', 'is_published', 'is_featured', 'sort_order', 'seo_title', 'seo_description', 'brand', 'model_name'])]
class Equipment extends Model
{
    use HasCareerImages;

    public const IMAGE_DIRECTORY = 'training';

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'is_featured' => 'boolean', 'sort_order' => 'integer'];
    }
}
