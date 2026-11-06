<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Biography;
use App\Models\WebsiteSetting;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $theme = config('theme');

        // Mark 'About' active in navigation for this page
        if (isset($theme['navigation'])) {
            foreach ($theme['navigation'] as &$item) {
                $item['active'] = ($item['label'] === 'About');
            }
        }

        $biography = Biography::find(1);
        $siteSettings = WebsiteSetting::find(1);
        $achievements = Achievement::where('is_published', true)->orderBy('sort_order')->orderBy('id')->get();

        return view('about', [
            'theme' => $theme,
            'biography' => $biography,
            'siteSettings' => $siteSettings,
            'achievements' => $achievements,
        ]);
    }
}
