<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'slug', 'news_category_id', 'excerpt', 'body', 'image', 'image_caption', 'author_name', 'publication_date', 'status', 'is_featured', 'tournament_id', 'source_url', 'seo_title', 'seo_description', 'sort_order'])]
class Article extends Model
{
    use HasCareerImages, HasFactory;

    public const IMAGE_DIRECTORY = 'articles';

    protected function casts(): array
    {
        return ['publication_date' => 'date', 'is_featured' => 'boolean', 'sort_order' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereDate('publication_date', '<=', today())
            ->whereHas('category', fn (Builder $category) => $category->where('is_published', true));
    }

    public function scopeMatching(Builder $query, array $filters): Builder
    {
        if (isset($filters['q']) && $filters['q'] !== '') {
            $query->where(fn (Builder $q) => $q->where('title', 'like', '%'.$filters['q'].'%')->orWhere('excerpt', 'like', '%'.$filters['q'].'%'));
        }
        if (! empty($filters['category'])) {
            $query->where('news_category_id', $filters['category']);
        }
        if (! empty($filters['from'])) {
            $query->whereDate('publication_date', '>=', $filters['from']);
        }
        if (! empty($filters['to'])) {
            $query->whereDate('publication_date', '<=', $filters['to']);
        }

        return $query;
    }
}
