<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'category', 'year', 'event_date', 'event_name', 'description', 'image', 'image_alt', 'source_note', 'is_featured', 'is_published', 'sort_order'])]
class Achievement extends Model
{
    use HasCareerImages, HasFactory;

    protected function casts(): array
    {
        return ['event_date' => 'date', 'year' => 'integer', 'is_featured' => 'boolean', 'is_published' => 'boolean', 'sort_order' => 'integer'];
    }
}
