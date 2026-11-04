<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'slug', 'short_description', 'full_description', 'activity_date', 'location', 'image', 'source_url', 'is_featured', 'is_published', 'sort_order', 'seo_title', 'seo_description'])]
class SocialWork extends Model
{
    use HasCareerImages,HasFactory;

    public const IMAGE_DIRECTORY = 'community';

    protected function casts(): array
    {
        return ['activity_date' => 'date', 'is_featured' => 'boolean', 'is_published' => 'boolean', 'sort_order' => 'integer'];
    }

    public function images(): HasMany
    {
        return $this->hasMany(SocialWorkImage::class);
    }
}
