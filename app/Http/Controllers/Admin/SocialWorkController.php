<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveSocialWorkRequest;
use App\Http\Requests\CommunityFilterRequest;
use App\Models\SocialWork;
use App\Services\CommunityWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class SocialWorkController extends Controller
{
    public function index(CommunityFilterRequest $request): View
    {
        $q = SocialWork::withCount('images');
        $f = $request->validated();
        if (! empty($f['q'])) {
            $q->where(fn ($q) => $q->where('title', 'like', '%'.$f['q'].'%')->orWhere('location', 'like', '%'.$f['q'].'%'));
        }
        if (! empty($f['publication'])) {
            $q->where('is_published', $f['publication'] === 'published');
        }
        if (isset($f['featured'])) {
            $q->where('is_featured', $f['featured'] === 'yes');
        }

        return view('admin.community.projects', ['title' => 'Social Works', 'projects' => $q->orderBy('sort_order')->orderByDesc('activity_date')->orderBy('id')->paginate(15)->withQueryString()]);
    }

    public function create(): View
    {
        return $this->form(new SocialWork(['sort_order' => 0]));
    }

    public function edit(SocialWork $social_work): View
    {
        return $this->form($social_work);
    }

    private function form(SocialWork $project): View
    {
        return view('admin.community.project-form', ['title' => 'Social Work', 'project' => $project, 'images' => $project->exists ? $project->images()->orderBy('sort_order')->orderBy('id')->paginate(12) : null]);
    }

    public function store(SaveSocialWorkRequest $request, CommunityWriter $writer): RedirectResponse
    {
        return $this->persist($request, new SocialWork, $writer);
    }

    public function update(SaveSocialWorkRequest $request, SocialWork $social_work, CommunityWriter $writer): RedirectResponse
    {
        return $this->persist($request, $social_work, $writer);
    }

    private function persist(SaveSocialWorkRequest $request, SocialWork $project, CommunityWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->save($project, $request->safe()->except(['image', 'remove_image', 'images', 'gallery_alt_text']), $request, 'image');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput($request->except(['image', 'images']))->withErrors(['save' => 'Project could not be saved. Existing content was kept; newly uploaded files were cleaned up.']);
        }

        return redirect()->route('admin.social-works.index')->with('status', 'Project saved.')->with('warning', $ok ? null : 'An unused image could not be removed.');
    }

    public function toggle(Request $request, SocialWork $social_work): RedirectResponse
    {
        $field = $request->validate(['field' => ['required', 'in:is_published,is_featured']])['field'];
        $ok = DB::transaction(function () use ($field, $social_work): bool {
            $current = SocialWork::whereKey($social_work->id)->lockForUpdate()->firstOrFail();
            if ($field === 'is_published' && ! $current->is_published && (! $current->activity_date || $current->activity_date->isFuture() || ! trim((string) $current->short_description) || ! trim((string) $current->full_description))) {
                return false;
            }
            $current->update([$field => ! $current->getAttribute($field)]);

            return true;
        });

        return $ok ? back()->with('status', 'Project flag updated.') : back()->withErrors(['publish' => 'Add reviewed descriptions and a valid activity date before publishing.']);
    }

    public function destroy(SocialWork $social_work, CommunityWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->delete($social_work);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['delete' => 'Project could not be deleted.']);
        }

        return redirect()->route('admin.social-works.index')->with('status', 'Project and its image records deleted. Shared files were retained.')->with('warning', $ok ? null : 'An unused image could not be removed.');
    }
}
