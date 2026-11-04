<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTournamentResultRequest;
use App\Http\Requests\TournamentFilterRequest;
use App\Models\Tournament;
use App\Models\TournamentResult;
use App\Rules\SafeWebUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TournamentResultController extends Controller
{
    public function index(TournamentFilterRequest $request): View
    {
        $query = TournamentResult::with('tournament')->whereHas('tournament', fn ($query) => $query->matching($request->validated()));
        if ($status = $request->validated('result_status')) {
            $query->where('status', $status);
        }
        if ($publication = $request->validated('publication')) {
            $query->where('is_published', $publication === 'published');
        }

        return view('admin.tournaments.results', ['title' => 'Tournament Results',
            'results' => $query->orderByDesc('id')->paginate(15)->withQueryString()]);
    }

    public function edit(Tournament $tournament): View
    {
        return view('admin.tournaments.result-form', ['title' => 'Manage Tournament Result', 'tournament' => $tournament,
            'result' => $tournament->result ?? new TournamentResult(['status' => 'pending'])]);
    }

    public function store(SaveTournamentResultRequest $request, Tournament $tournament): RedirectResponse
    {
        return $this->persist($request, $tournament, false);
    }

    public function update(SaveTournamentResultRequest $request, Tournament $tournament): RedirectResponse
    {
        return $this->persist($request, $tournament, true);
    }

    private function persist(SaveTournamentResultRequest $request, Tournament $tournament, bool $updating): RedirectResponse
    {
        DB::transaction(function () use ($request, $tournament, $updating): void {
            $current = Tournament::whereKey($tournament->id)->lockForUpdate()->firstOrFail();
            $result = $current->result;
            if ($updating) {
                abort_unless($result, 404);
                $result->update($request->safe()->except('tournament_id'));
            } else {
                if ($result) {
                    throw ValidationException::withMessages(['tournament_id' => 'A result already exists for this tournament. Edit it instead.']);
                }
                $current->result()->create($request->safe()->except('tournament_id'));
            }
        });

        return redirect()->route('admin.tournaments.show', $tournament)->with('status', 'Tournament result saved.');
    }

    public function toggle(Tournament $tournament): RedirectResponse
    {
        $changed = DB::transaction(function () use ($tournament): bool {
            $current = Tournament::whereKey($tournament->id)->lockForUpdate()->firstOrFail();
            $result = $current->result;
            abort_unless($result, 404);
            if (! $result->is_published && (! $result->source_url || ! SafeWebUrl::allows($result->source_url))) {
                return false;
            }
            $result->update(['is_published' => ! $result->is_published]);

            return true;
        });

        return $changed ? back()->with('status', 'Result publication updated.')
            : back()->withErrors(['source_url' => 'Add a valid source URL before publishing this result.']);
    }

    public function destroy(Tournament $tournament): RedirectResponse
    {
        DB::transaction(function () use ($tournament): void {
            $current = Tournament::whereKey($tournament->id)->lockForUpdate()->firstOrFail();
            $result = $current->result;
            abort_unless($result, 404);
            $result->delete();
        });

        return redirect()->route('admin.tournaments.show', $tournament)->with('status', 'Result deleted; tournament retained.');
    }
}
