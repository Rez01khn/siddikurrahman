<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'year', 'event_date', 'description', 'image', 'image_alt', 'source_note', 'is_published', 'sort_order'])]
class CareerMilestone extends Model
{
    use HasCareerImages, HasFactory;

    protected function casts(): array
    {
        return ['event_date' => 'date', 'year' => 'integer', 'is_published' => 'boolean', 'sort_order' => 'integer'];
    }
}
