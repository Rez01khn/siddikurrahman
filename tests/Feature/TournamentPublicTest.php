<?php

use App\Models\Tournament;
use App\Models\TournamentResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('an empty public schedule and homepage show honest empty states', function () {
    $this->get('/tournaments')->assertOk()->assertSee('No published upcoming tournaments match these filters.')
        ->assertSee('No published past tournaments match these filters.');
    $this->get('/')->assertOk()->assertSee('No confirmed tournaments are published here yet.')
        ->assertDontSee('Date TBC')->assertDontSee('Tournament Name');
});

test('public schedule displays only published events grouped by status', function () {
    Tournament::factory()->published()->create(['name' => 'Published Upcoming', 'status' => 'upcoming']);
    Tournament::factory()->published()->create(['name' => 'Published Ongoing', 'status' => 'ongoing']);
    Tournament::factory()->published()->create(['name' => 'Published Completed', 'status' => 'completed']);
    Tournament::factory()->published()->create(['name' => 'Published Cancelled', 'status' => 'cancelled']);
    Tournament::factory()->create(['name' => 'Private Tournament']);

    $this->get('/tournaments')->assertOk()->assertSee('Published Upcoming')->assertSee('Published Ongoing')
        ->assertSee('Published Completed')->assertSee('Published Cancelled')->assertDontSee('Private Tournament');
});

test('public year filtering includes tournaments overlapping the selected year', function () {
    Tournament::factory()->published()->create(['name' => 'Cross Year Event', 'start_date' => '2025-12-30', 'end_date' => '2026-01-02']);
    Tournament::factory()->published()->create(['name' => 'Outside Year', 'start_date' => '2024-03-01', 'end_date' => '2024-03-04']);

    $this->get('/tournaments?year=2026')->assertOk()->assertSee('Cross Year Event')->assertDontSee('Outside Year');
    $this->get('/tournaments?year=2025')->assertOk()->assertSee('Cross Year Event')->assertDontSee('Outside Year');
});

test('public status and search filtering use only matching published data', function () {
    Tournament::factory()->published()->create(['name' => 'Matching Completed', 'status' => 'completed']);
    Tournament::factory()->published()->create(['name' => 'Other Upcoming', 'status' => 'upcoming']);

    $this->get('/tournaments?status=completed&q=Matching')->assertOk()
        ->assertSee('Matching Completed')->assertDontSee('Other Upcoming');
});

test('public filters cannot request private publication states', function () {
    $this->getJson('/tournaments?publication=draft')->assertUnprocessable()->assertJsonValidationErrors('publication');
});

test('public detail routes reject draft and missing tournaments', function () {
    $tournament = Tournament::factory()->create();

    $this->get(route('tournaments.show', $tournament))->assertNotFound();
    $this->get('/tournaments/999999')->assertNotFound();
});

test('published tournament details include real supplied fields and safe links', function () {
    $tournament = Tournament::factory()->published()->create(['name' => 'Reviewed Detail Event', 'city' => 'Test City',
        'country' => 'Test Country', 'official_url' => 'https://example.com/official', 'source_url' => 'https://example.com/archive']);

    $this->get(route('tournaments.show', $tournament))->assertOk()->assertSee('Reviewed Detail Event')
        ->assertSee('Test Course')->assertSee('Test City')->assertSee('Test Country')
        ->assertSee('href="https://example.com/official"', false)->assertSee('rel="noopener noreferrer"', false);
});

test('draft player result values never appear on public cards or details', function () {
    $tournament = Tournament::factory()->published()->create(['status' => 'completed']);
    TournamentResult::factory()->for($tournament)->create(['final_position' => 'PrivatePosition', 'notes' => 'PrivateResultNotes']);

    $this->get('/tournaments')->assertOk()->assertDontSee('PrivatePosition')->assertDontSee('PrivateResultNotes');
    $this->get(route('tournaments.show', $tournament))->assertOk()->assertDontSee('PrivatePosition')
        ->assertDontSee('PrivateResultNotes')->assertSee('No published result is available.');
});

test('a published child result does not expose a private parent tournament', function () {
    $tournament = Tournament::factory()->create(['name' => 'Private Parent']);
    TournamentResult::factory()->for($tournament)->published()->create(['notes' => 'PrivateParentResult']);

    $this->get('/tournaments')->assertOk()->assertDontSee('Private Parent')->assertDontSee('PrivateParentResult');
    $this->get(route('tournaments.show', $tournament))->assertNotFound();
    $this->get('/')->assertOk()->assertDontSee('Private Parent');
});

test('published partial scorecards preserve null values and show source information', function () {
    $tournament = Tournament::factory()->published()->create(['status' => 'completed']);
    TournamentResult::factory()->for($tournament)->published()->create(['status' => 'missed_cut',
        'round_1' => 71, 'round_2' => 74, 'score_to_par' => 0, 'notes' => 'Reviewed partial scorecard']);

    $this->get(route('tournaments.show', $tournament))->assertOk()->assertSee('Missed cut')
        ->assertSee('71')->assertSee('74')->assertSee('Reviewed partial scorecard')->assertSee('Result source')
        ->assertSee('>E<', false)->assertDontSee('Reported player winnings:');
});

test('reported player money and signed par values are displayed without inferred figures', function () {
    $tournament = Tournament::factory()->published()->create();
    TournamentResult::factory()->for($tournament)->published()->create(['prize_amount' => '1000.50',
        'prize_currency' => 'USD', 'score_to_par' => -7, 'final_position' => 'T12']);

    $this->get(route('tournaments.show', $tournament))->assertOk()->assertSee('USD 1000.50')
        ->assertSee('T12')->assertSee('>-7<', false);
});

test('stored tournament result text and seo values are escaped', function () {
    $tournament = Tournament::factory()->published()->create(['name' => '<script>alert(1)</script>',
        'description' => '" onload="alert(1)', 'official_url' => 'javascript:alert(1)']);
    TournamentResult::factory()->for($tournament)->published()->create(['notes' => '<img src=x onerror=alert(1)>']);

    $this->get(route('tournaments.show', $tournament))->assertOk()->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false)->assertSee('<img src=x onerror=alert(1)>')
        ->assertDontSee('<img src=x onerror=alert(1)>', false)->assertDontSee('href="javascript:', false)
        ->assertDontSee('content="" onload=', false);
});

test('public schedule order follows stored order with a stable tie breaker', function () {
    Tournament::factory()->published()->create(['name' => 'Later Ordered Event', 'sort_order' => 20]);
    Tournament::factory()->published()->create(['name' => 'Earlier Ordered Event', 'sort_order' => 1]);

    $this->get('/tournaments')->assertOk()->assertSeeInOrder(['Earlier Ordered Event', 'Later Ordered Event']);
});

test('public group pagination preserves year filters', function () {
    Tournament::factory()->published()->count(12)->create(['start_date' => '2026-01-01', 'end_date' => '2026-01-04']);

    $response = $this->get('/tournaments?year=2026&upcoming_page=2');

    $response->assertOk();
    expect($response->viewData('upcoming')->total())->toBe(12);
    expect($response->viewData('upcoming')->count())->toBe(3);
    expect($response->viewData('upcoming')->url(1))->toContain('year=2026');
});

test('homepage preview shows the next upcoming recent completed and featured records without duplicates', function () {
    $this->travelTo(now()->setDate(2026, 11, 4)->startOfDay());
    $next = Tournament::factory()->published()->create(['name' => 'Next Confirmed Event', 'start_date' => '2026-11-10', 'end_date' => '2026-11-13']);
    Tournament::factory()->published()->create(['name' => 'Later Upcoming Event', 'start_date' => '2026-12-10', 'end_date' => '2026-12-13']);
    $recent = Tournament::factory()->published()->create(['name' => 'Recent Completed Event', 'status' => 'completed', 'start_date' => '2026-10-20', 'end_date' => '2026-10-23']);
    Tournament::factory()->published()->create(['name' => 'Featured Ongoing Event', 'is_featured' => true, 'status' => 'ongoing', 'start_date' => '2026-11-03', 'end_date' => '2026-11-06']);
    Tournament::factory()->published()->create(['name' => 'Cancelled Featured Event', 'is_featured' => true, 'status' => 'cancelled']);
    Tournament::factory()->create(['name' => 'Private Upcoming Event']);
    TournamentResult::factory()->for($recent)->published()->create(['status' => 'completed', 'final_position' => 'T18']);

    $response = $this->get('/');

    $response->assertOk()->assertSee('Next Confirmed Event')->assertSee('Recent Completed Event')
        ->assertSee('Featured Ongoing Event')->assertSee('Position T18')->assertDontSee('Cancelled Featured Event')
        ->assertDontSee('Private Upcoming Event')->assertDontSee('Later Upcoming Event');
    expect($response->viewData('tournamentPreview')->pluck('id')->unique()->count())->toBe(3);
});

test('outdated upcoming records cannot be selected as the next event by date', function () {
    $this->travelTo(now()->setDate(2026, 11, 4)->startOfDay());
    Tournament::factory()->published()->create(['name' => 'Expired Upcoming', 'start_date' => '2026-01-01', 'end_date' => '2026-01-04']);
    Tournament::factory()->published()->create(['name' => 'Future Upcoming', 'start_date' => '2026-11-10', 'end_date' => '2026-11-13']);

    $this->get('/')->assertOk()->assertSee('Future Upcoming')->assertDontSee('Expired Upcoming');
});

test('missing tournament image files produce no broken storage image urls', function () {
    Storage::fake('public');
    $tournament = Tournament::factory()->published()->create(['image' => 'tournaments/'.str_repeat('a', 40).'.jpg']);

    $this->get(route('tournaments.show', $tournament))->assertOk()->assertDontSee('storage/tournaments/', false);
});
