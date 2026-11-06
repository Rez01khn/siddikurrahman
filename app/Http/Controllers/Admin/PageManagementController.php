<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Biography;
use App\Models\CoachingTip;
use App\Models\HomepageSection;
use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\Video;
use Illuminate\View\View;

class PageManagementController extends Controller
{
    public function home(): View
    {
        $heroSection = HomepageSection::where('section_key', 'hero')->first();
        $highlightsSection = HomepageSection::where('section_key', 'career_highlights')->first();
        $beyondSection = HomepageSection::where('section_key', 'beyond')->first();

        $tournamentsCount = Tournament::count();
        $upcomingTournaments = Tournament::where('is_published', true)
            ->where('end_date', '>=', now()->toDateString())
            ->count();

        $coachingCount = CoachingTip::count();
        $photosCount = Photo::count();
        $albumsCount = PhotoAlbum::count();
        $videosCount = Video::count();
        $sponsorsCount = Sponsor::count();

        return view('admin.pages.home', [
            'title' => 'Homepage Manager',
            'hero' => $heroSection,
            'highlights' => $highlightsSection,
            'beyond' => $beyondSection,
            'tournamentsCount' => $tournamentsCount,
            'upcomingTournaments' => $upcomingTournaments,
            'coachingCount' => $coachingCount,
            'photosCount' => $photosCount,
            'albumsCount' => $albumsCount,
            'videosCount' => $videosCount,
            'sponsorsCount' => $sponsorsCount,
        ]);
    }

    public function about(): View
    {
        $biography = Biography::first();
        $achievementsCount = Achievement::count();
        $activeAchievementsCount = Achievement::where('is_published', true)->count();
        $recentAchievements = Achievement::orderByDesc('year')->take(5)->get();

        return view('admin.pages.about', [
            'title' => 'About Page Manager',
            'biography' => $biography,
            'achievementsCount' => $achievementsCount,
            'activeAchievementsCount' => $activeAchievementsCount,
            'recentAchievements' => $recentAchievements,
        ]);
    }
}
