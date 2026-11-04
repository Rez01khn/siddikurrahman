<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Latest News', 'Tournament Updates', 'Press Coverage', 'Interviews', 'Announcements'] as $order => $name) {
            NewsCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'is_published' => true, 'sort_order' => ($order + 1) * 10]);
        }
    }
}
