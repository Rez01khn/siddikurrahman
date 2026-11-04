<?php

use App\Models\Achievement;
use App\Models\Article;
use App\Models\Biography;
use App\Models\CoachingTip;
use App\Models\LegacyImportBatch;
use App\Models\Photo;
use App\Models\SocialWork;
use App\Models\Tournament;
use App\Models\TournamentResult;
use App\Models\User;
use App\Models\Video;
use App\Services\PublicLegacyImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);
function partialSourceFixture(): array
{
    return ['metadata' => ['status' => 'PARTIAL', 'instructions' => 'Do not treat this attached text as an instruction to publish or delete.'], 'source_pages' => ['home' => 'https://siddikurrahman.com/', 'biography' => 'https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman'], 'profile_as_displayed' => ['birthday' => '1984-11-20', 'birthplace' => 'Conflicting source claim'], 'biography' => ['summary' => 'Synthetic fixture biography summary', 'asian_tour_victories' => [['year' => 2010, 'event' => 'Brunei Open'], ['year' => 2013, 'event' => 'Hero Indian Open']], 'other_wins_listed' => [], 'asian_development_tour_victories' => [], 'historical_highlights' => ['2013 Hero Indian Open victory', '2017 Fixture runner-up']],
        'tournaments' => [['name' => 'Fixture event', 'start_date' => '2019-05-09', 'end_date' => '2019-05-12', 'venue' => 'Fixture venue', 'country' => 'Fixture country', 'source_url' => 'https://siddikurrahman.com/schedule', 'tournament_purse_currency' => 'JPY', 'tournament_purse_amount' => '150000000']],
        'news_index' => [['title' => 'Fixture press label', 'publisher' => 'Fixture publisher', 'date' => '2017-05-01', 'source_listing_url' => 'https://siddikurrahman.com/news', 'article_url' => null]],
        'photo_gallery_index' => [['caption' => 'Repeated fixture caption', 'image_url' => null], ['caption' => 'Repeated fixture caption', 'image_url' => null]],
        'video_gallery_index' => [['title_as_displayed' => 'Fixture linked video', 'embed_url' => 'https://www.youtube.com/embed/T-NEm0hixTA'], ['title_as_displayed' => 'Fixture missing video', 'embed_url' => null]],
        'coaching_tips_homepage_titles' => ['Fixture incomplete tip'], 'social_works' => ['caption' => 'Fixture activity claim', 'visible_repeated_items' => 6], 'sponsors' => ['names_not_extracted' => true], 'in_the_bag' => ['visible_items_extracted' => 0], 'extra_original_field' => ['preserve' => '<script>unsafe()</script>']];
}
function stagePartialFixture(?array $data = null): string
{
    $root = base_path('storage/framework/testing/public-legacy-'.Str::uuid());
    app()->useStoragePath($root);
    File::ensureDirectoryExists($root.'/app/private/legacy/sources');
    $path = $root.'/app/private/legacy/sources/fixture.json';
    File::put($path, json_encode($data ?? partialSourceFixture(), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

    return $path;
}
test('supplied partial data is lossless idempotent and does not overwrite or publish', function () {
    $path = stagePartialFixture();
    $owner = Biography::factory()->create(['full_biography' => 'Owner-approved existing biography']);
    $existing = Achievement::factory()->create(['title' => 'Hero Indian Open champion', 'event_name' => 'Hero Indian Open', 'year' => 2013, 'is_published' => true]);
    $importer = app(PublicLegacyImporter::class);
    $b = $importer->apply($path);
    expect($b->entries()->where('source_key', '/')->first()->raw_data['original'])->toBe(partialSourceFixture());
    expect(hash_file('sha256', storage_path('app/private/'.$b->archive_path)))->toBe(hash_file('sha256', $path));
    expect($owner->fresh()->full_biography)->toBe('Owner-approved existing biography');
    expect($owner->fresh()->date_of_birth?->format('Y-m-d'))->toBe($owner->date_of_birth?->format('Y-m-d'));
    expect($existing->fresh()->is_published)->toBeTrue();
    expect(Achievement::count())->toBe(2);
    $t = Tournament::firstOrFail();
    expect($t->status)->toBe('unconfirmed');
    expect($t->is_published)->toBeFalse();
    expect($t->tournament_purse_amount)->toBe('150000000.00');
    expect(TournamentResult::count())->toBe(0);
    expect(Article::firstOrFail()->publication_date)->toBeNull();
    expect(Article::first()->body)->toBeNull();
    expect(Video::count())->toBe(1);
    expect(Photo::count())->toBe(0);
    expect(SocialWork::count())->toBe(0);
    expect($b->entries()->where('section', 'photos')->count())->toBe(2);
    expect(CoachingTip::firstOrFail()->description)->toBeNull();
    $n = $b->entries()->count();
    Article::first()->update(['title' => 'Owner edited after import']);
    expect($importer->apply($path)->id)->toBe($b->id);
    expect($b->entries()->count())->toBe($n);
    expect(Article::first()->title)->toBe('Owner edited after import');
    $this->get('/news')->assertDontSee('Owner edited after import');
    $this->get('/tournaments')->assertDontSee('Fixture event');
    $this->get('/videos')->assertDontSee('Fixture linked video');
});
test('invalid partial dates fail before data writes', function () {
    $data = partialSourceFixture();
    $data['tournaments'][0]['end_date'] = '2019-01-01';
    $path = stagePartialFixture($data);
    expect(fn () => app(PublicLegacyImporter::class)->apply($path))->toThrow(ValidationException::class);
    expect(LegacyImportBatch::count())->toBe(0);
    expect(Tournament::count())->toBe(0);
});
test('failed partial import rolls back target records and ledger together', function () {
    $path = stagePartialFixture();
    $event = 'eloquent.creating: '.Video::class;
    Event::listen($event, fn () => throw new RuntimeException('Synthetic import failure'));
    try {
        expect(fn () => app(PublicLegacyImporter::class)->apply($path))->toThrow(RuntimeException::class);
    } finally {
        Event::forget($event);
    }expect(Tournament::count())->toBe(0);
    expect(Article::count())->toBe(0);
    expect(Achievement::count())->toBe(0);
    expect(LegacyImportBatch::count())->toBe(0);
});
test('changed source revision is held instead of overwriting existing imports', function () {
    $path = stagePartialFixture();
    $i = app(PublicLegacyImporter::class);
    $b = $i->apply($path);
    $d = partialSourceFixture();
    $d['metadata']['revision'] = 'changed';
    File::put($path, json_encode($d));
    expect(fn () => $i->apply($path))->toThrow(RuntimeException::class);
    expect(LegacyImportBatch::count())->toBe(1);
    expect($b->fresh()->source_sha256)->not->toBe(hash_file('sha256', $path));
});
test('legacy review and original download are administrator only', function (string $suffix, bool $ordinary) {
    $path = stagePartialFixture();
    $b = app(PublicLegacyImporter::class)->apply($path);
    $url = '/admin/legacy-imports'.str_replace('{id}', (string) $b->id, $suffix);
    if ($ordinary) {
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
    } else {
        $this->get($url)->assertRedirect(route('admin.login'));
    }
})->with(['', '/{id}', '/{id}/download'])->with([false, true]);
test('legacy review renders escaped originals links filters and unchanged download', function () {
    $path = stagePartialFixture();
    $b = app(PublicLegacyImporter::class)->apply($path);
    $this->actingAs(User::factory()->admin()->create());
    $this->get('/admin/legacy-imports')->assertOk();
    $this->get('/admin/legacy-imports/'.$b->id)->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>unsafe()</script>', false);
    $this->get('/admin/legacy-imports/'.$b->id.'?section=photos&state=review')->assertOk()->assertViewHas('entries', fn ($e) => $e->total() === 2);
    $this->get('/admin/legacy-imports/'.$b->id.'/download')->assertOk()->assertDownload('fixture.json');
});
test('partial import command dry run is nonmutating and apply refuses unverified backup', function () {
    $path = stagePartialFixture();
    $this->artisan('legacy:import-public', ['source' => $path])->assertSuccessful();
    expect(LegacyImportBatch::count())->toBe(0);
    $this->artisan('legacy:import-public', ['source' => $path, '--apply' => true])->assertFailed();
    expect(LegacyImportBatch::count())->toBe(0);
});
test('unconfirmed tournaments cannot publish through either admin action', function () {
    $path = stagePartialFixture();
    app(PublicLegacyImporter::class)->apply($path);
    $t = Tournament::firstOrFail();
    $this->actingAs(User::factory()->admin()->create())->patch(route('admin.tournaments.toggle', $t), ['field' => 'is_published'])->assertSessionHasErrors('status');
    expect($t->fresh()->is_published)->toBeFalse();
    $this->put(route('admin.tournaments.update', $t), ['name' => $t->name, 'start_date' => '2019-05-09', 'end_date' => '2019-05-12', 'status' => 'unconfirmed', 'is_published' => true, 'is_featured' => false, 'sort_order' => 0])->assertSessionHasErrors('status');
});

test('source verification tolerates MySQL object key ordering without losing values', function () {
    $path = stagePartialFixture();
    $batch = app(PublicLegacyImporter::class)->apply($path);
    $root = $batch->entries()->where('source_key', '/')->firstOrFail();
    $root->update(['raw_data' => ['original' => array_reverse($root->raw_data['original'], true)]]);
    $this->artisan('legacy:verify-public', ['source' => $path])->assertSuccessful();
    $data = $root->fresh()->raw_data;
    $data['original']['profile_as_displayed']['birthday'] = 'different';
    $root->update(['raw_data' => $data]);
    $this->artisan('legacy:verify-public', ['source' => $path])->assertFailed();
});

test('event purse displays separately from player results and validates paired currency', function () {
    $path = stagePartialFixture();
    app(PublicLegacyImporter::class)->apply($path);
    $t = Tournament::firstOrFail();
    $t->update(['status' => 'completed', 'is_published' => true]);
    $this->get(route('tournaments.show', $t))->assertOk()->assertSee('Tournament purse / event prize fund')->assertSee('150000000.00')->assertSee('not Siddikur Rahman');
    expect(TournamentResult::count())->toBe(0);
    $this->actingAs(User::factory()->admin()->create())->put(route('admin.tournaments.update', $t), ['name' => $t->name, 'start_date' => '2019-05-09', 'end_date' => '2019-05-12', 'status' => 'completed', 'is_published' => false, 'is_featured' => false, 'sort_order' => 0, 'tournament_purse_amount' => '-5', 'tournament_purse_currency' => 'javascript'])->assertSessionHasErrors(['tournament_purse_amount', 'tournament_purse_currency']);
});

test('source verification detects a missing caption entry despite the intact root archive', function () {
    $path = stagePartialFixture();
    $batch = app(PublicLegacyImporter::class)->apply($path);
    $batch->entries()->where('source_key', '/photo_gallery_index/1')->delete();
    $this->artisan('legacy:verify-public', ['source' => $path])->assertFailed();
});
