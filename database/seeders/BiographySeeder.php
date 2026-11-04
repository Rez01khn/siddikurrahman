<?php

namespace Database\Seeders;

use App\Models\Biography;
use Illuminate\Database\Seeder;

class BiographySeeder extends Seeder
{
    public function run(): void
    {
        $biography = Biography::find(1);
        if (! $biography) {
            $biography = new Biography([
                'full_name' => 'Siddikur Rahman',
                'professional_title' => 'Professional golfer',
                'short_introduction' => 'Professional golfer from Bangladesh.',
                'nationality' => 'Bangladeshi',
                'source_note' => 'Identity, profession and country supplied by the website owner. Historical biography and profile fields await review.',
                'is_published' => true,
            ]);
            $biography->id = 1;
            $biography->save();
        }
    }
}
