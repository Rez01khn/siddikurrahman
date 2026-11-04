<?php

use App\Models\Tournament;
use App\Models\TournamentResult;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function tournamentPayload(array $overrides = []): array
{
    return array_replace(['name' => 'Reviewed Test Event', 'start_date' => '2026-11-10', 'end_date' => '2026-11-13',
        'status' => 'upcoming', 'is_published' => true, 'is_featured' => false, 'sort_order' => 10,
        'official_url' => 'https://example.com/tournament', 'source_url' => 'https://example.com/source'], $overrides);
}

function tournamentResultPayload(Tournament $tournament, array $overrides = []): array
{
    return array_replace(['tournament_id' => $tournament->id, 'status' => 'completed', 'is_published' => true,
        'source_url' => 'https://example.com/scorecard'], $overrides);
}

test('tournament management and result routes redirect guests', function (string $method, string $url) {
    $tournament = Tournament::factory()->create();

    $this->$method(str_replace('{id}', (string) $tournament->id, $url))->assertRedirect(route('admin.login'));
})->with([
    ['get', '/admin/tournaments'], ['get', '/admin/tournaments/create'], ['post', '/admin/tournaments'],
    ['get', '/admin/tournaments/{id}'], ['get', '/admin/tournaments/{id}/edit'], ['put', '/admin/tournaments/{id}'],
    ['delete', '/admin/tournaments/{id}'], ['patch', '/admin/tournaments/{id}/toggle'],
    ['get', '/admin/tournament-results'], ['get', '/admin/tournaments/{id}/result'],
    ['post', '/admin/tournaments/{id}/result'], ['put', '/admin/tournaments/{id}/result'],
    ['delete', '/admin/tournaments/{id}/result'], ['patch', '/admin/tournaments/{id}/result/toggle'],
]);

test('ordinary users cannot manage tournaments or results', function (string $method, string $url) {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->create())->$method(str_replace('{id}', (string) $tournament->id, $url))->assertForbidden();
})->with([
    ['get', '/admin/tournaments'], ['get', '/admin/tournaments/create'], ['post', '/admin/tournaments'],
    ['get', '/admin/tournaments/{id}'], ['get', '/admin/tournaments/{id}/edit'], ['put', '/admin/tournaments/{id}'],
    ['delete', '/admin/tournaments/{id}'], ['patch', '/admin/tournaments/{id}/toggle'],
    ['get', '/admin/tournament-results'], ['get', '/admin/tournaments/{id}/result'],
    ['post', '/admin/tournaments/{id}/result'], ['put', '/admin/tournaments/{id}/result'],
    ['delete', '/admin/tournaments/{id}/result'], ['patch', '/admin/tournaments/{id}/result/toggle'],
]);

test('admin tournament and result screens render', function (string $url) {
    $tournament = Tournament::factory()->create();
    TournamentResult::factory()->for($tournament)->create();

    $this->actingAs(User::factory()->admin()->create())->get(str_replace('{id}', (string) $tournament->id, $url))->assertOk();
})->with(['/admin/tournaments', '/admin/tournaments/create', '/admin/tournaments/{id}', '/admin/tournaments/{id}/edit', '/admin/tournaments/{id}/result', '/admin/tournament-results']);

test('administrators can create a tournament', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/tournaments', tournamentPayload())
        ->assertRedirect(route('admin.tournaments.index'))->assertSessionHasNoErrors();
    $this->assertDatabaseHas('tournaments', ['name' => 'Reviewed Test Event', 'is_published' => true, 'sort_order' => 10]);
});

test('administrators can update tournament fields and order', function () {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.tournaments.update', $tournament),
        tournamentPayload(['name' => 'Edited Event', 'status' => 'completed', 'sort_order' => 42]))
        ->assertSessionHasNoErrors();
    expect($tournament->fresh()->name)->toBe('Edited Event');
    expect($tournament->fresh()->sort_order)->toBe(42);
    expect($tournament->fresh()->status)->toBe('completed');
});

test('tournament validation preserves existing data', function (array $overrides, string $field) {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.tournaments.update', $tournament),
        tournamentPayload($overrides))->assertSessionHasErrors($field);
    expect($tournament->fresh()->name)->toBe('Synthetic Test Tournament');
})->with([
    [['name' => ''], 'name'],
    [['start_date' => '2026-02-30'], 'start_date'],
    [['end_date' => '2026-11-09'], 'end_date'],
    [['status' => 'unconfirmed'], 'status'],
    [['sort_order' => -1], 'sort_order'],
    [['is_published' => 'invalid'], 'is_published'],
]);

test('unsafe official and source links are rejected', function (string $url) {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/tournaments',
        tournamentPayload(['official_url' => $url, 'source_url' => $url]))->assertSessionHasErrors(['official_url', 'source_url']);
    $this->assertDatabaseCount('tournaments', 0);
})->with(['javascript:alert(1)', '//evil.example', 'ftp://example.com/file', 'https://user:pass@example.com', 'https://example.com/\\evil', "https://example.com/\npath"]);

test('same day tournaments are accepted', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/tournaments', tournamentPayload(['end_date' => '2026-11-10']))
        ->assertSessionHasNoErrors();
});

test('featured and publication controls update only permitted flags', function (string $field) {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->patch(route('admin.tournaments.toggle', $tournament), ['field' => $field])
        ->assertSessionHasNoErrors();
    expect($tournament->fresh()->getAttribute($field))->toBeTrue();
})->with(['is_featured', 'is_published']);

test('the toggle cannot change an arbitrary tournament column', function () {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->patch(route('admin.tournaments.toggle', $tournament), ['field' => 'name'])
        ->assertSessionHasErrors('field');
});

test('admin filters search status year and publication with pagination', function () {
    Tournament::factory()->count(17)->create(['name' => 'Matching Event', 'start_date' => '2026-01-10', 'end_date' => '2026-01-13', 'status' => 'completed']);
    Tournament::factory()->create(['name' => 'Unrelated Event']);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/tournaments?q=Matching&status=completed&year=2026&publication=draft');

    $response->assertOk()->assertSee('Matching Event')->assertDontSee('Unrelated Event');
    expect($response->viewData('tournaments')->total())->toBe(17);
    expect($response->viewData('tournaments')->count())->toBe(15);
});

test('malformed tournament filters are rejected', function (string $query, string $field) {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/tournaments?'.$query)->assertSessionHasErrors($field);
})->with([['year[]=2026', 'year'], ['year=1800', 'year'], ['status=invalid', 'status'], ['q[]=bad', 'q']]);

test('tournament images upload and replace using the isolated directory', function () {
    Storage::fake('public');
    $old = UploadedFile::fake()->image('old.jpg')->store('tournaments', 'public');
    $tournament = Tournament::factory()->create(['image' => $old]);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.tournaments.update', $tournament),
        tournamentPayload(['image' => UploadedFile::fake()->image('new.jpg', 500, 500)]))->assertSessionHasNoErrors();
    $path = $tournament->fresh()->image;
    expect($path)->toStartWith('tournaments/');
    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($path);
    $this->get(route('tournaments.show', $tournament))->assertOk()->assertSee($tournament->fresh()->imageUrl(), false);
});

test('invalid image types sizes and dimensions preserve the old image', function (string $type) {
    Storage::fake('public');
    $old = UploadedFile::fake()->image('old.jpg')->store('tournaments', 'public');
    $tournament = Tournament::factory()->create(['image' => $old]);
    $image = match ($type) {
        'svg' => UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
        'php' => UploadedFile::fake()->createWithContent('bad.php', '<?php echo 1;'),
        'large' => UploadedFile::fake()->image('large.jpg')->size(5121),
        'tiny' => UploadedFile::fake()->image('tiny.jpg', 10, 10),
    };

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.tournaments.update', $tournament),
        tournamentPayload(['image' => $image]))->assertSessionHasErrors('image');
    expect($tournament->fresh()->image)->toBe($old);
    Storage::disk('public')->assertExists($old);
})->with(['svg', 'php', 'large', 'tiny']);

test('tournament image removal clears only the selected upload', function () {
    Storage::fake('public');
    $old = UploadedFile::fake()->image('old.jpg')->store('tournaments', 'public');
    $tournament = Tournament::factory()->create(['image' => $old]);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.tournaments.update', $tournament),
        tournamentPayload(['remove_image' => true]))->assertSessionHasNoErrors();
    expect($tournament->fresh()->image)->toBeNull();
    Storage::disk('public')->assertMissing($old);
});

test('failed tournament saves clean new files without losing old data', function () {
    Storage::fake('public');
    $old = UploadedFile::fake()->image('old.jpg')->store('tournaments', 'public');
    $tournament = Tournament::factory()->create(['image' => $old]);
    Tournament::updating(function () {
        throw new RuntimeException('Test write failure');
    });

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.tournaments.update', $tournament),
        tournamentPayload(['image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))->assertSessionHasErrors('save');
    expect($tournament->fresh()->image)->toBe($old);
    expect(Storage::disk('public')->allFiles())->toBe([$old]);
});

test('tournament deletion removes its linked result and image', function () {
    Storage::fake('public');
    $image = UploadedFile::fake()->image('old.jpg')->store('tournaments', 'public');
    $tournament = Tournament::factory()->create(['image' => $image]);
    $result = TournamentResult::factory()->for($tournament)->create();

    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.tournaments.destroy', $tournament))
        ->assertRedirect(route('admin.tournaments.index'));
    $this->assertModelMissing($tournament);
    $this->assertModelMissing($result);
    Storage::disk('public')->assertMissing($image);
});

test('deletion never removes career or unrelated image paths', function () {
    Storage::fake('public');
    $path = 'career/'.str_repeat('a', 40).'.jpg';
    Storage::disk('public')->put($path, 'keep');
    $tournament = Tournament::factory()->create(['image' => $path]);

    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.tournaments.destroy', $tournament))->assertRedirect();
    Storage::disk('public')->assertExists($path);
});

test('results can be created with unknown values and incomplete rounds', function () {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->post(route('admin.tournaments.result.store', $tournament),
        tournamentResultPayload($tournament, ['status' => 'missed_cut', 'round_1' => 72, 'round_2' => 73]))
        ->assertRedirect(route('admin.tournaments.show', $tournament))->assertSessionHasNoErrors();
    $result = $tournament->result;
    expect($result->round_1)->toBe(72);
    expect($result->round_3)->toBeNull();
    expect($result->total_score)->toBeNull();
    expect($result->prize_amount)->toBeNull();
    expect($result->final_position)->toBeNull();
});

test('result updates keep the parent and support reported money and even par', function () {
    $tournament = Tournament::factory()->create();
    $result = TournamentResult::factory()->for($tournament)->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.tournaments.result.update', $tournament),
        tournamentResultPayload($tournament, ['final_position' => 'T12', 'total_score' => 280, 'score_to_par' => 0,
            'prize_amount' => '1000.50', 'prize_currency' => 'USD']))->assertSessionHasNoErrors();
    expect($result->fresh()->final_position)->toBe('T12');
    expect($result->fresh()->prize_amount)->toBe('1000.50');
    expect($result->fresh()->parLabel())->toBe('E');
});

test('duplicate result submissions cannot replace an existing record', function () {
    $tournament = Tournament::factory()->create();
    $result = TournamentResult::factory()->for($tournament)->create(['notes' => 'Keep existing']);

    $this->actingAs(User::factory()->admin()->create())->post(route('admin.tournaments.result.store', $tournament),
        tournamentResultPayload($tournament, ['notes' => 'Replacement']))->assertSessionHasErrors('tournament_id');
    $this->assertDatabaseCount('tournament_results', 1);
    expect($result->fresh()->notes)->toBe('Keep existing');
});

test('database uniqueness rejects duplicate result records directly', function () {
    $tournament = Tournament::factory()->create();
    TournamentResult::factory()->for($tournament)->create();

    expect(fn () => TournamentResult::factory()->for($tournament)->create())->toThrow(QueryException::class);
});

test('a nested result cannot be moved to a different tournament', function () {
    $tournament = Tournament::factory()->create();
    $other = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->post(route('admin.tournaments.result.store', $tournament),
        tournamentResultPayload($other))->assertSessionHasErrors('tournament_id');
    $this->assertDatabaseCount('tournament_results', 0);
});

test('result validation rejects invalid scores money and sources', function (array $overrides, string $field) {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->post(route('admin.tournaments.result.store', $tournament),
        tournamentResultPayload($tournament, $overrides))->assertSessionHasErrors($field);
    $this->assertDatabaseCount('tournament_results', 0);
})->with([
    [['round_1' => -1], 'round_1'], [['round_2' => '72.5'], 'round_2'],
    [['total_score' => 0], 'total_score'], [['score_to_par' => 1000], 'score_to_par'],
    [['prize_amount' => '-1', 'prize_currency' => 'USD'], 'prize_amount'],
    [['prize_amount' => '10.123', 'prize_currency' => 'USD'], 'prize_amount'],
    [['prize_amount' => '1000000000000', 'prize_currency' => 'USD'], 'prize_amount'],
    [['prize_amount' => '50', 'prize_currency' => null], 'prize_currency'],
    [['prize_currency' => 'US dollars'], 'prize_currency'],
    [['status' => 'invalid'], 'status'], [['source_url' => null], 'source_url'],
    [['source_url' => 'javascript:alert(1)'], 'source_url'],
]);

test('draft results can be saved without a source but cannot use the publication shortcut', function () {
    $tournament = Tournament::factory()->create();
    $result = TournamentResult::factory()->for($tournament)->create(['source_url' => null]);

    $this->actingAs(User::factory()->admin()->create())->patch(route('admin.tournaments.result.toggle', $tournament))
        ->assertSessionHasErrors('source_url');
    expect($result->fresh()->is_published)->toBeFalse();
});

test('result publication can be toggled independently', function () {
    $tournament = Tournament::factory()->create();
    $result = TournamentResult::factory()->for($tournament)->published()->create();

    $this->actingAs(User::factory()->admin()->create())->patch(route('admin.tournaments.result.toggle', $tournament))
        ->assertSessionHasNoErrors();
    expect($result->fresh()->is_published)->toBeFalse();
});

test('deleting a result preserves its tournament', function () {
    $tournament = Tournament::factory()->create();
    $result = TournamentResult::factory()->for($tournament)->create();

    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.tournaments.result.destroy', $tournament))
        ->assertRedirect(route('admin.tournaments.show', $tournament));
    $this->assertModelMissing($result);
    $this->assertModelExists($tournament);
});

test('the results list supports status and parent year filters', function () {
    $tournament = Tournament::factory()->create(['start_date' => '2025-11-10', 'end_date' => '2025-11-13']);
    TournamentResult::factory()->for($tournament)->create(['status' => 'missed_cut']);

    $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/tournament-results?result_status=missed_cut&year=2025');

    $response->assertOk()->assertSee('Missed cut');
    expect($response->viewData('results')->total())->toBe(1);
});

test('legacy schedule links redirect to the working editor', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/modules/tournaments')->assertRedirect(route('admin.tournaments.index'));
});

test('missing tournament and result records return not found', function () {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->get('/admin/tournaments/999999')->assertNotFound();
    $this->delete(route('admin.tournaments.result.destroy', $tournament))->assertNotFound();
});

test('tournament and result writes enforce csrf outside the test bypass', function (string $method, string $suffix) {
    $this->app->instance('env', 'local');
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->$method('/admin/tournaments/'.$tournament->id.$suffix)->assertStatus(419);
})->with([['put', ''], ['post', '/result'], ['delete', '/result']]);

test('a new result form renders without requiring any scores', function () {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->get(route('admin.tournaments.result.edit', $tournament))
        ->assertOk()->assertSee('Save result');
    $this->assertDatabaseCount('tournament_results', 0);
});

test('draft results accept an unknown source and all unknown score fields', function () {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->post(route('admin.tournaments.result.store', $tournament),
        tournamentResultPayload($tournament, ['status' => 'pending', 'source_url' => null, 'is_published' => false]))
        ->assertSessionHasNoErrors();
    expect($tournament->result->source_url)->toBeNull();
    expect($tournament->result->is_published)->toBeFalse();
});

test('result updates do not create missing results implicitly', function () {
    $tournament = Tournament::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.tournaments.result.update', $tournament),
        tournamentResultPayload($tournament))->assertNotFound();
    $this->assertDatabaseCount('tournament_results', 0);
});
