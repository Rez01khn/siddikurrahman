<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'full_name',
    'professional_title',
    'short_introduction',
    'full_biography',
    'date_of_birth',
    'birthplace',
    'nationality',
    'professional_start_year',
    'profile_image',
    'banner_image',
    'profile_image_alt',
    'banner_image_alt',
    'additional_details',
    'seo_title',
    'seo_description',
    'source_note',
    'is_published',
])]
class Biography extends Model
{
    use HasCareerImages, HasFactory;

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'additional_details' => 'array',
            'is_published' => 'boolean',
            'professional_start_year' => 'integer',
        ];
    }

    public function profileInfo(): array
    {
        $themeDefaults = config('theme.personal_info', []);
        $details = $this->additional_details['profile_info'] ?? [];

        return array_merge($themeDefaults, [
            'full_name' => $this->full_name ?: ($themeDefaults['full_name'] ?? 'Mohammad Siddikur Rahman'),
            'dob' => $this->date_of_birth ? $this->date_of_birth->format('d M Y') : ($themeDefaults['dob'] ?? '20 Nov 1984'),
            'location' => $this->birthplace ?: ($themeDefaults['location'] ?? 'Dhaka, Bangladesh'),
            'turned_pro' => $this->professional_start_year ? (string) $this->professional_start_year : ($themeDefaults['turned_pro'] ?? '2005'),
        ], $details);
    }
}
