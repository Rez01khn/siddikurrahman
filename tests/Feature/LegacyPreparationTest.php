<?php

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);
function privateLegacyTestDirectory(): string
{
    $root = base_path('storage/framework/testing/legacy-'.Str::uuid());
    app()->useStoragePath($root);
    foreach (['app/public', 'app/private/legacy/sources', 'framework/views', 'logs'] as $sub) {
        File::ensureDirectoryExists($root.'/'.$sub);
    }

    return $root;
}
test('legacy audit is read only reports duplicates and omits authentication secrets', function () {
    $root = privateLegacyTestDirectory();
    User::factory()->create(['email' => 'do-not-export@example.com']);
    Sponsor::factory()->count(2)->create(['source_url' => 'https://example.com/same']);
    $this->artisan('legacy:audit')->assertSuccessful();
    $report = File::get($root.'/app/private/legacy/current-audit.json');
    $json = json_decode($report, true);
    expect($json['tables']['sponsors']['count'])->toBe(2);
    expect($json['duplicates'])->toHaveCount(1);
    expect($report)->not->toContain('do-not-export@example.com')->not->toContain('remember_token');
    expect(Sponsor::count())->toBe(2);
});
test('legacy backup rejects nonlocal environment and non mysql drivers', function () {
    $this->artisan('legacy:backup')->expectsOutputToContain('local MySQL')->assertFailed();
    $this->app->instance('env', 'production');
    $this->artisan('legacy:backup')->expectsOutputToContain('local MySQL')->assertFailed();
});
test('legacy plan is dry run only and blocks paths outside private staging', function () {
    $root = privateLegacyTestDirectory();
    $this->artisan('legacy:plan', ['manifest' => 'outside.json'])->expectsOutputToContain('Only --dry-run')->assertFailed();
    $this->artisan('legacy:plan', ['manifest' => base_path('composer.json'), '--dry-run' => true])->expectsOutputToContain('inside the private')->assertFailed();
});
test('legacy plan refuses empty unverified and published staging rows without writes', function (string $kind) {
    $root = privateLegacyTestDirectory();
    $data = ['version' => 1, 'records' => []];
    if ($kind !== 'empty') {
        $data['records'][] = ['legacy_id' => 'fixture-1', 'table' => 'sponsors', 'source_url' => 'https://example.com/fixture', 'source_verified' => $kind !== 'unverified', 'reuse_authorized' => true, 'reviewed_by' => 'Synthetic test reviewer', 'data' => ['name' => 'Fixture', 'slug' => 'fixture', 'is_published' => $kind === 'published']];
    }File::put($root.'/app/private/legacy/sources/test.json', json_encode($data));
    $this->artisan('legacy:plan', ['manifest' => $root.'/app/private/legacy/sources/test.json', '--dry-run' => true])->assertFailed();
    expect(Sponsor::count())->toBe(0);
    expect(json_decode(File::get($root.'/app/private/legacy/last-plan.json'), true)['imported'])->toBe(0);
})->with(['empty', 'unverified', 'published']);
test('legacy dry run reports existing matches and never overwrites them', function () {
    $root = privateLegacyTestDirectory();
    $s = Sponsor::factory()->create(['slug' => 'fixture']);
    $data = ['version' => 1, 'records' => [['legacy_id' => 'fixture-1', 'table' => 'sponsors', 'source_url' => 'https://example.com/fixture', 'source_verified' => true, 'reuse_authorized' => true, 'reviewed_by' => 'Synthetic reviewer', 'data' => ['name' => 'Must not overwrite', 'slug' => 'fixture', 'is_published' => false]]]];
    File::put($root.'/app/private/legacy/sources/test.json', json_encode($data));
    $this->artisan('legacy:plan', ['manifest' => $root.'/app/private/legacy/sources/test.json', '--dry-run' => true])->assertFailed();
    expect($s->fresh()->name)->toBe($s->name);
});
test('valid legacy plan remains nonmutating and explicitly has no apply mode', function () {
    $root = privateLegacyTestDirectory();
    $data = ['version' => 1, 'records' => [['legacy_id' => 'fixture-1', 'table' => 'sponsors', 'source_url' => 'https://example.com/fixture', 'source_verified' => true, 'reuse_authorized' => true, 'reviewed_by' => 'Synthetic reviewer', 'data' => ['name' => 'Fixture', 'slug' => 'fixture', 'is_published' => false]]]];
    File::put($root.'/app/private/legacy/sources/test.json', json_encode($data));
    $this->artisan('legacy:plan', ['manifest' => $root.'/app/private/legacy/sources/test.json', '--dry-run' => true])->assertSuccessful();
    expect(Sponsor::count())->toBe(0);
    $r = json_decode(File::get($root.'/app/private/legacy/last-plan.json'), true);
    expect($r['apply_available'])->toBeFalse();
});

test('legacy inventory retains stable row keys and valid source pages', function () {
    $inventory = json_decode(file_get_contents(base_path('database/data/legacy-phase5-index.json')), true, 512, JSON_THROW_ON_ERROR);
    expect(count($inventory['records']))->toBe($inventory['candidate_count']);
    expect(collect($inventory['records'])->every(fn ($row) => parse_url($row['source_url'], PHP_URL_HOST) === 'siddikurrahman.com'))->toBeTrue();
    expect(collect($inventory['records'])->every(fn ($row) => preg_match('/\A[a-z0-9-]+\z/', $row['legacy_key']) === 1))->toBeTrue();
    expect(collect($inventory['records'])->pluck('legacy_key')->unique()->count())->toBe($inventory['candidate_count']);
});
