<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Article;
use App\Models\Biography;
use App\Models\CareerStatistic;
use App\Models\CoachingTip;
use App\Models\Equipment;
use App\Models\HomepageSection;
use App\Models\Photo;
use App\Models\SocialWork;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\Video;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $saved = Schema::hasTable('homepage_sections')
            ? HomepageSection::all()->keyBy('section_key') : collect();
        $sections = collect(config('homepage'))->map(function (array $definition, string $key) use ($saved): HomepageSection {
            return $saved->get($key) ?? new HomepageSection($definition['defaults']);
        })->filter(fn (HomepageSection $section) => $section->is_active)
            ->sortBy(fn (HomepageSection $section) => sprintf('%05d-%s', $section->sort_order, $section->section_key));

        $visibleAnchors = $sections->map(fn (HomepageSection $section) => $section->definition()['anchor'])->values();
        if ($visibleAnchors->contains('media')) {
            $visibleAnchors->push('videos');
        }

        $tournamentPreview = collect();
        if (Schema::hasTable('tournaments')) {
            $query = Tournament::where('is_published', true)->with('publishedResult');
            $next = (clone $query)->where('status', 'upcoming')->whereDate('start_date', '>=', today())->orderBy('start_date')->orderBy('sort_order')->orderBy('id')->first();
            $recent = (clone $query)->where('status', 'completed')->whereDate('end_date', '<=', today())->orderByDesc('end_date')->orderBy('sort_order')->orderBy('id')->limit(3)->get();
            $featured = (clone $query)->where('is_featured', true)->where('status', '!=', 'cancelled')->orderBy('sort_order')->orderBy('start_date')->orderBy('id')->limit(3)->get();
            $tournamentPreview = collect([$next, $recent->first(), $featured->first()])->filter()->concat($recent)->concat($featured)->unique('id')->take(3)->values();
        }

        return view('home', [
            'featuredEquipment' => Schema::hasTable('equipment') ? Equipment::where('is_published', true)->where('is_featured', true)->orderBy('sort_order')->orderBy('id')->limit(2)->get() : collect(),
            'featuredTips' => Schema::hasTable('coaching_tips') ? CoachingTip::where('is_published', true)->where('is_featured', true)->orderBy('sort_order')->orderBy('id')->limit(2)->get() : collect(),
            'featuredSponsors' => Schema::hasTable('sponsors') ? Sponsor::where('is_published', true)->where('is_featured', true)->orderBy('sort_order')->orderBy('id')->limit(6)->get() : collect(),
            'featuredSocialWorks' => Schema::hasTable('social_works') ? SocialWork::where('is_published', true)->where('is_featured', true)->orderBy('sort_order')->orderByDesc('activity_date')->orderBy('id')->limit(3)->get() : collect(),
            'galleryPhotos' => Schema::hasTable('photos') ? Photo::visible()->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id')->limit(12)->get()->filter(fn ($photo) => $photo->imageUrl() !== null)->take(2)->values() : collect(),
            'galleryVideo' => Schema::hasTable('videos') ? Video::where('is_published', true)->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id')->first() : null,
            'latestArticles' => Schema::hasTable('articles') ? Article::visible()->with('category')->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('publication_date')->orderByDesc('id')->limit(2)->get() : collect(),
            'tournamentPreview' => $tournamentPreview,
            'publicBiography' => Schema::hasTable('biographies') ? Biography::whereKey(1)->where('is_published', true)->first() : null,
            'featuredAchievements' => Schema::hasTable('achievements') ? Achievement::where('is_published', true)->where('is_featured', true)->orderBy('sort_order')->orderBy('id')->limit(3)->get() : collect(),
            'careerStatistics' => Schema::hasTable('career_statistics') ? CareerStatistic::where('is_published', true)->orderBy('sort_order')->orderBy('id')->limit(3)->get() : collect(),
            'sections' => $sections,
            'visibleAnchors' => $visibleAnchors->all(),
        ]);
    }
}
