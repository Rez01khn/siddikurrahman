<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tournament_id', 'final_position', 'total_score', 'score_to_par', 'round_1', 'round_2', 'round_3', 'round_4', 'prize_amount', 'prize_currency', 'status', 'notes', 'source_url', 'is_published'])]
class TournamentResult extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'total_score' => 'integer', 'score_to_par' => 'integer',
            'round_1' => 'integer', 'round_2' => 'integer', 'round_3' => 'integer', 'round_4' => 'integer', 'prize_amount' => 'decimal:2'];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function parLabel(): ?string
    {
        return $this->score_to_par === null ? null : ($this->score_to_par === 0 ? 'E' : sprintf('%+d', $this->score_to_par));
    }
}
