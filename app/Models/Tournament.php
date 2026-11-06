<?php

namespace App\Models;

use App\Models\Concerns\HasCareerImages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'tour', 'venue', 'city', 'country', 'start_date', 'end_date',
    'description', 'official_url', 'source_url', 'status', 'is_featured',
    'is_published', 'sort_order', 'image', 'image_alt',
    'tournament_purse_amount', 'tournament_purse_currency',
])]
class Tournament extends Model
{
    use HasCareerImages, HasFactory;

    public const IMAGE_DIRECTORY = 'tournaments';

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'tournament_purse_amount' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function scopeMatching(Builder $query, array $filters): Builder
    {
        if ($search = $filters['q'] ?? null) {
            $query->where(function (Builder $query) use ($search): void {
                foreach (['name', 'tour', 'venue', 'city', 'country'] as $index => $field) {
                    $query->{$index === 0 ? 'where' : 'orWhere'}($field, 'like', '%'.$search.'%');
                }
            });
        }
        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }
        if ($year = $filters['year'] ?? null) {
            $query->whereDate('start_date', '<=', $year.'-12-31')->whereDate('end_date', '>=', $year.'-01-01');
        }

        return $query;
    }

    public function location(): string
    {
        return implode(', ', array_filter([$this->venue, $this->city, $this->country]));
    }

    public function formattedDateRange(): string
    {
        if (! $this->start_date && ! $this->end_date) {
            return '';
        }
        if ($this->start_date && ! $this->end_date) {
            return $this->start_date->format('d M Y');
        }
        if (! $this->start_date && $this->end_date) {
            return $this->end_date->format('d M Y');
        }

        if ($this->start_date->format('Y') === $this->end_date->format('Y')) {
            if ($this->start_date->format('M') === $this->end_date->format('M')) {
                return $this->start_date->format('d').' – '.$this->end_date->format('d M Y');
            }

            return $this->start_date->format('d M').' – '.$this->end_date->format('d M Y');
        }

        return $this->start_date->format('d M Y').' – '.$this->end_date->format('d M Y');
    }
}
