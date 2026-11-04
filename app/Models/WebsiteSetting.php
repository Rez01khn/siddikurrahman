<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['site_name', 'logo', 'favicon', 'contact_email', 'contact_phone', 'contact_address', 'footer_text', 'seo_title', 'seo_description', 'social_links'])]
class WebsiteSetting extends Model
{
    use HasCareerImages;

    public const IMAGE_DIRECTORY = 'settings';

    protected function casts(): array
    {
        return ['social_links' => 'array'];
    }
}
