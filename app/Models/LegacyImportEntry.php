<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['source_key', 'section', 'state', 'raw_data', 'target_table', 'target_id', 'review_note'])]
class LegacyImportEntry extends Model
{
    protected function casts(): array
    {
        return ['raw_data' => 'array'];
    }
}
