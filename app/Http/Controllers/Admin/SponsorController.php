<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveSponsorRequest;
use App\Http\Requests\CommunityFilterRequest;
use App\Models\Sponsor;
use App\Services\CareerContentWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class SponsorController extends Controller
{
    public function index(CommunityFilterRequest $request): View
    {
        $q = Sponsor::query();
        $f = $request->validated();
        if (! empty($f['q'])) {
            $q->where('name', 'like', '%'.$f['q'].'%');
        }
        if (! empty($f['category'])) {
            $q->where('category', $f['category']);
        }
        if (! empty($f['publication'])) {
            $q->where('is_published', $f['publication'] === 'published');
        }
        if (isset($f['featured'])) {
            $q->where('is_featured', $f['featured'] === 'yes');
        }

        return view('admin.sponsors.index', [
            'title' => 'Sponsors & Partners',
            'sponsors' => $q->orderBy('sort_order')->orderBy('id')->paginate(15)->withQueryString(),
            'categories' => Sponsor::whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Sponsor(['sort_order' => 0]));
    }

    public function edit(Sponsor $sponsor): View
    {
        return $this->form($sponsor);
    }

    private function form(Sponsor $sponsor): View
    {
        return view('admin.sponsors.form', ['title' => 'Sponsor', 'sponsor' => $sponsor]);
    }

    public function store(SaveSponsorRequest $request, CareerContentWriter $writer): RedirectResponse
    {
        return $this->persist($request, new Sponsor, $writer);
    }

    public function update(SaveSponsorRequest $request, Sponsor $sponsor, CareerContentWriter $writer): RedirectResponse
    {
        return $this->persist($request, $sponsor, $writer);
    }

    private function persist(SaveSponsorRequest $request, Sponsor $sponsor, CareerContentWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->save($sponsor, $request->safe()->except(['logo', 'remove_logo']), $request, ['logo'], 'sponsors');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput($request->except('logo'))->withErrors(['save' => 'Sponsor could not be saved. Existing content and files were kept.']);
        }

        return redirect()->route('admin.sponsors.index')->with('status', 'Sponsor saved.')->with('warning', $ok ? null : 'An unused image could not be removed.');
    }

    public function toggle(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $field = $request->validate(['field' => ['required', 'in:is_published,is_featured']])['field'];
        DB::transaction(function () use ($field, $sponsor): void {
            $current = Sponsor::whereKey($sponsor->id)->lockForUpdate()->firstOrFail();
            $current->update([$field => ! $current->getAttribute($field)]);
        });

        return back()->with('status', 'Sponsor flag updated.');
    }

    public function destroy(Sponsor $sponsor, CareerContentWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->delete($sponsor, ['logo']);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['delete' => 'Sponsor could not be deleted.']);
        }

        return redirect()->route('admin.sponsors.index')->with('status', 'Sponsor deleted.')->with('warning', $ok ? null : 'An unused image could not be removed.');
    }
}
