<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'slug', 'description', 'cover_image', 'album_date', 'is_featured', 'is_published', 'sort_order'])]
class PhotoAlbum extends Model
{
    use HasCareerImages, HasFactory;

    public const IMAGE_DIRECTORY = 'gallery';

    protected function casts(): array
    {
        return ['album_date' => 'date', 'is_featured' => 'boolean', 'is_published' => 'boolean', 'sort_order' => 'integer'];
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }
}
