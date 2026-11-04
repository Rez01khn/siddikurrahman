<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['full_name', 'professional_title', 'short_introduction', 'full_biography', 'date_of_birth', 'birthplace', 'nationality', 'professional_start_year', 'profile_image', 'banner_image', 'profile_image_alt', 'banner_image_alt', 'additional_details', 'seo_title', 'seo_description', 'source_note', 'is_published'])]
class Biography extends Model
{
    use HasCareerImages, HasFactory;

    public $incrementing = false;

    protected function casts(): array
    {
        return ['date_of_birth' => 'date', 'additional_details' => 'array', 'is_published' => 'boolean', 'professional_start_year' => 'integer'];
    }
}
