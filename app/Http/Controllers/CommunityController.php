<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommunityFilterRequest;
use App\Models\SocialWork;
use App\Models\Sponsor;
use Illuminate\View\View;

class CommunityController extends Controller
{
    private function layout(): array
    {
        return ['sections' => collect(), 'visibleAnchors' => ['top'], 'solidHeader' => true];
    }

    public function sponsors(CommunityFilterRequest $request): View
    {
        $q = Sponsor::where('is_published', true);
        $f = $request->validated();
        if (! empty($f['q'])) {
            $q->where('name', 'like', '%'.$f['q'].'%');
        }
        if (! empty($f['category'])) {
            $q->where('category', $f['category']);
        }
        if (isset($f['featured'])) {
            $q->where('is_featured', $f['featured'] === 'yes');
        }

        return view('community.sponsors', $this->layout() + ['sponsors' => $q->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id')->paginate(12)->withQueryString(),
            'categories' => Sponsor::where('is_published', true)->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')]);
    }

    public function projects(CommunityFilterRequest $request): View
    {
        $q = SocialWork::where('is_published', true);
        $f = $request->validated();
        if (! empty($f['q'])) {
            $q->where(fn ($q) => $q->where('title', 'like', '%'.$f['q'].'%')->orWhere('location', 'like', '%'.$f['q'].'%'));
        }
        if (isset($f['featured'])) {
            $q->where('is_featured', $f['featured'] === 'yes');
        }

        return view('community.projects', $this->layout() + ['projects' => $q->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('activity_date')->orderBy('id')->paginate(9)->withQueryString()]);
    }

    public function project(SocialWork $social_work): View
    {
        abort_unless($social_work->is_published, 404);

        return view('community.project', $this->layout() + ['project' => $social_work,
            'images' => $social_work->images()->orderBy('sort_order')->orderBy('id')->paginate(12),
            'related' => SocialWork::where('is_published', true)->whereKeyNot($social_work->id)->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('activity_date')->limit(3)->get()]);
    }
}
