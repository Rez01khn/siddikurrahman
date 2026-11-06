<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Biography;
use App\Models\CoachingTip;
use App\Models\HomepageSection;
use App\Models\Photo;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\Video;
use App\Models\WebsiteSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $homepageSections = HomepageSection::where('is_active', true)->get()->keyBy('section_key');
        $biography = Biography::find(1);
        $achievements = Achievement::where('is_published', true)->orderBy('sort_order')->orderBy('id')->get();
        $tournaments = Tournament::where('is_published', true)->orderBy('sort_order')->orderBy('start_date')->get();
        $photos = Photo::where('is_published', true)->orderBy('sort_order')->orderBy('id')->take(6)->get();
        $videos = Video::where('is_published', true)->orderBy('sort_order')->orderBy('id')->take(6)->get();
        $coachingTips = CoachingTip::where('is_published', true)->orderBy('sort_order')->orderBy('id')->take(6)->get();
        $sponsors = Sponsor::where('is_published', true)->orderBy('sort_order')->orderBy('id')->get();
        $siteSettings = WebsiteSetting::find(1);

        return view('home', [
            'theme' => config('theme'),
            'homepageSections' => $homepageSections,
            'hero' => $homepageSections->get('hero'),
            'careerHighlights' => $homepageSections->get('career_highlights'),
            'beyond' => $homepageSections->get('beyond'),
            'biography' => $biography,
            'achievements' => $achievements,
            'tournaments' => $tournaments,
            'photos' => $photos,
            'videos' => $videos,
            'coachingTips' => $coachingTips,
            'sponsors' => $sponsors,
            'siteSettings' => $siteSettings,
        ]);
    }
}
