<?php

namespace App\Http\Controllers;

use App\Http\Requests\TournamentFilterRequest;
use App\Models\Tournament;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(TournamentFilterRequest $request): View
    {
        $query = Tournament::where('is_published', true)->with('publishedResult')->matching($request->validated());

        return view('tournaments.index', [
            'sections' => collect(), 'visibleAnchors' => ['top', 'upcoming', 'ongoing', 'past'], 'solidHeader' => true,
            'upcoming' => (clone $query)->where('status', 'upcoming')->orderBy('sort_order')->orderBy('start_date')->orderBy('id')->paginate(9, ['*'], 'upcoming_page')->withQueryString(),
            'ongoing' => (clone $query)->where('status', 'ongoing')->orderBy('sort_order')->orderBy('start_date')->orderBy('id')->paginate(9, ['*'], 'ongoing_page')->withQueryString(),
            'past' => (clone $query)->whereIn('status', ['completed', 'cancelled'])->orderBy('sort_order')->orderByDesc('end_date')->orderBy('id')->paginate(9, ['*'], 'past_page')->withQueryString(),
        ]);
    }

    public function show(Tournament $tournament): View
    {
        abort_unless($tournament->is_published, 404);

        return view('tournaments.show', ['sections' => collect(), 'visibleAnchors' => ['top', 'result'], 'solidHeader' => true,
            'tournament' => $tournament->load('publishedResult')]);
    }
}
