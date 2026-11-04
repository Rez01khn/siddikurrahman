<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class HomepageSectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('homepage') as $key => $definition) {
            HomepageSection::firstOrCreate(['section_key' => $key], $definition['defaults']);
        }
    }
}
