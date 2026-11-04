<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['social_work_id', 'image', 'caption', 'alt_text', 'sort_order'])]
class SocialWorkImage extends Model
{
    use HasCareerImages,HasFactory;

    public const IMAGE_DIRECTORY = 'community';

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function socialWork(): BelongsTo
    {
        return $this->belongsTo(SocialWork::class);
    }
}
