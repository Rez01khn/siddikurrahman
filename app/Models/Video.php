<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use App\Support\VideoUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'slug', 'description', 'video_url', 'image', 'video_date', 'is_featured', 'is_published', 'sort_order'])]
class Video extends Model
{
    use HasCareerImages, HasFactory;

    public const IMAGE_DIRECTORY = 'gallery';

    protected function casts(): array
    {
        return ['video_date' => 'date', 'is_featured' => 'boolean', 'is_published' => 'boolean', 'sort_order' => 'integer'];
    }

    public function embedUrl(): ?string
    {
        return VideoUrl::embed($this->video_url);
    }
}
