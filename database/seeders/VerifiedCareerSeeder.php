<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class VerifiedCareerSeeder extends Seeder
{
    public function run(): void
    {
        Achievement::firstOrCreate([
            'title' => 'Hero Indian Open champion', 'year' => 2013, 'category' => 'tour_victory',
        ], [
            'event_name' => 'Hero Indian Open',
            'description' => 'Winner of the 2013 Hero Indian Open.',
            'source_note' => 'Indian Golf Union, Indian Open winners table, 2013 entry: https://indiangolfunion.org/india-open/ (reviewed 4 November 2026).',
            'sort_order' => 10,
            'is_featured' => true,
            'is_published' => true,
        ]);
    }
}
