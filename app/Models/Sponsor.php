<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug', 'logo', 'website_url', 'category', 'description', 'partnership_start_date', 'partnership_end_date', 'source_url', 'is_featured', 'is_published', 'sort_order'])]
class Sponsor extends Model
{
    use HasCareerImages,HasFactory;

    public const IMAGE_DIRECTORY = 'community';

    protected function casts(): array
    {
        return ['partnership_start_date' => 'date', 'partnership_end_date' => 'date', 'is_featured' => 'boolean', 'is_published' => 'boolean', 'sort_order' => 'integer'];
    }
}
