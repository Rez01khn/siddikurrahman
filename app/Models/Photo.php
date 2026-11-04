<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['photo_album_id', 'image', 'caption', 'alt_text', 'photographer_credit', 'photo_date', 'is_featured', 'is_published', 'sort_order'])]
class Photo extends Model
{
    use HasCareerImages, HasFactory;

    public const IMAGE_DIRECTORY = 'gallery';

    protected function casts(): array
    {
        return ['photo_date' => 'date', 'is_featured' => 'boolean', 'is_published' => 'boolean', 'sort_order' => 'integer'];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(PhotoAlbum::class, 'photo_album_id');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_published', true)->whereHas('album', fn (Builder $album) => $album->where('is_published', true));
    }
}
