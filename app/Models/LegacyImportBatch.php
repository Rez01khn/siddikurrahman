<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['dataset_key', 'source_sha256', 'original_filename', 'archive_path', 'summary'])]
class LegacyImportBatch extends Model
{
    protected function casts(): array
    {
        return ['summary' => 'array'];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(LegacyImportEntry::class);
    }
}
