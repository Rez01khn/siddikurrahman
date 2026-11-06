<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTournamentRequest;
use App\Http\Requests\TournamentFilterRequest;
use App\Models\Tournament;
use App\Services\CareerContentWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class TournamentController extends Controller
{
    public function index(TournamentFilterRequest $request): View
    {
        $query = Tournament::matching($request->validated());
        if ($publication = $request->validated('publication')) {
            $query->where('is_published', $publication === 'published');
        }

        return view('admin.tournaments.index', ['title' => 'Tournament Schedule',
            'tournaments' => $query->orderBy('sort_order')->orderByDesc('start_date')->orderBy('id')->paginate(15)->withQueryString()]);
    }

    public function create(): View
    {
        return view('admin.tournaments.form', ['title' => 'Add Tournament',
            'tournament' => new Tournament(['status' => 'upcoming', 'sort_order' => 0])]);
    }

    public function show(Tournament $tournament): View
    {
        return view('admin.tournaments.show', ['title' => $tournament->name, 'tournament' => $tournament->load('result')]);
    }

    public function edit(Tournament $tournament): View
    {
        return view('admin.tournaments.form', ['title' => 'Edit Tournament', 'tournament' => $tournament]);
    }

    public function store(SaveTournamentRequest $request, CareerContentWriter $writer): RedirectResponse
    {
        return $this->persist($request, new Tournament, $writer);
    }

    public function update(SaveTournamentRequest $request, Tournament $tournament, CareerContentWriter $writer): RedirectResponse
    {
        return $this->persist($request, $tournament, $writer);
    }

    private function persist(SaveTournamentRequest $request, Tournament $tournament, CareerContentWriter $writer): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'remove_image']);
        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $data['sort_order'] = $tournament->exists ? $tournament->sort_order : ((int) Tournament::max('sort_order') + 1);
        }
        if (! isset($data['is_published'])) {
            $data['is_published'] = $tournament->exists ? $tournament->is_published : true;
        }
        if (! isset($data['is_featured'])) {
            $data['is_featured'] = $tournament->exists ? $tournament->is_featured : false;
        }
        try {
            $cleaned = $writer->save($tournament, $data, $request, ['image'], 'tournaments');
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput($request->except('image'))->withErrors(['save' => 'The tournament could not be saved. Existing content and images were kept.']);
        }

        return redirect()->route('admin.tournaments.index')->with('status', 'Tournament saved.')
            ->with('warning', $cleaned ? null : 'Saved, but an unused old image could not be removed.');
    }

    public function toggle(Request $request, Tournament $tournament): RedirectResponse
    {
        $field = $request->validate(['field' => ['required', 'in:is_published,is_featured']])['field'];
        DB::transaction(function () use ($tournament, $field): void {
            $current = Tournament::whereKey($tournament->id)->lockForUpdate()->firstOrFail();
            if ($field === 'is_published' && ! $current->is_published && $current->status === 'unconfirmed') {
                throw ValidationException::withMessages(['status' => 'Confirm the event status before publication.']);
            }
            $current->update([$field => ! $current->getAttribute($field)]);
        });

        return back()->with('status', 'Tournament visibility / featured status updated.');
    }

    public function destroy(Tournament $tournament, CareerContentWriter $writer): RedirectResponse
    {
        try {
            $cleaned = $writer->delete($tournament, ['image']);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['delete' => 'The tournament could not be deleted. Please try again.']);
        }

        return redirect()->route('admin.tournaments.index')->with('status', 'Tournament and its linked result deleted.')
            ->with('warning', $cleaned ? null : 'Deleted, but an unused image could not be removed.');
    }
}
