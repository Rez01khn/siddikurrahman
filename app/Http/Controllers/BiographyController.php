<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Biography;
use App\Models\CareerMilestone;
use App\Models\CareerStatistic;
use Illuminate\View\View;

class BiographyController extends Controller
{
    public function __invoke(): View
    {
        return view('biography', [
            'biography' => Biography::whereKey(1)->where('is_published', true)->first(),
            'milestones' => CareerMilestone::where('is_published', true)->orderBy('sort_order')->orderBy('id')->paginate(12, ['*'], 'milestones_page'),
            'achievements' => Achievement::where('is_published', true)->orderBy('sort_order')->orderBy('id')->paginate(12, ['*'], 'achievements_page'),
            'statistics' => CareerStatistic::where('is_published', true)->orderBy('sort_order')->orderBy('id')->paginate(12, ['*'], 'statistics_page'),
            'sections' => collect(),
            'visibleAnchors' => ['top', 'profile', 'journey', 'career', 'statistics', 'photography'],
            'solidHeader' => true,
        ]);
    }
}
