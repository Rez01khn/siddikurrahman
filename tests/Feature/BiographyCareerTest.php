<?php

use App\Models\Achievement;
use App\Models\Biography;
use App\Models\CareerMilestone;
use App\Models\CareerStatistic;
use App\Models\User;
use Database\Seeders\BiographySeeder;
use Database\Seeders\VerifiedCareerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function careerTestData(string $type, array $overrides = []): array
{
    $data = ['description' => 'Test record description.', 'source_note' => 'Reviewed test source.',
        'is_published' => true, 'sort_order' => 10];
    $data += $type === 'statistics' ? ['label' => 'Synthetic metric', 'value' => '12']
        : ['title' => 'Synthetic '.$type, 'year' => 2013];
    if ($type === 'achievements') {
        $data += ['category' => 'tour_victory', 'event_name' => 'Test event', 'is_featured' => true];
    }

    return array_replace($data, $overrides);
}

function careerTestModel(string $type): string
{
    return match ($type) {
        'milestones' => CareerMilestone::class,
        'achievements' => Achievement::class,
        'statistics' => CareerStatistic::class,
    };
}

function biographyTestData(array $overrides = []): array
{
    return array_replace(['full_name' => 'Reviewed Athlete', 'professional_title' => 'Professional golfer',
        'short_introduction' => 'A reviewed test introduction.', 'full_biography' => "Test story.\n\nAnother paragraph.",
        'source_note' => 'Test source.', 'is_published' => true], $overrides);
}

test('biography and career admin routes reject guests', function (string $method, string $path) {
    $this->$method($path)->assertRedirect(route('admin.login'));
})->with([
    ['get', '/admin/biography'], ['put', '/admin/biography'],
    ['get', '/admin/milestones'], ['get', '/admin/milestones/create'], ['post', '/admin/milestones'],
    ['get', '/admin/milestones/1/edit'], ['put', '/admin/milestones/1'], ['patch', '/admin/milestones/1/toggle'], ['delete', '/admin/milestones/1'],
    ['get', '/admin/achievements'], ['get', '/admin/achievements/create'], ['post', '/admin/achievements'],
    ['get', '/admin/achievements/1/edit'], ['put', '/admin/achievements/1'], ['patch', '/admin/achievements/1/toggle'], ['delete', '/admin/achievements/1'],
    ['get', '/admin/statistics'], ['get', '/admin/statistics/create'], ['post', '/admin/statistics'],
    ['get', '/admin/statistics/1/edit'], ['put', '/admin/statistics/1'], ['patch', '/admin/statistics/1/toggle'], ['delete', '/admin/statistics/1'],
]);

test('ordinary users cannot enter biography or career administration', function (string $method, string $path) {
    $this->actingAs(User::factory()->create())->$method($path)->assertForbidden();
})->with([
    ['get', '/admin/biography'], ['put', '/admin/biography'],
    ['get', '/admin/milestones'], ['post', '/admin/milestones'], ['put', '/admin/milestones/1'], ['delete', '/admin/milestones/1'], ['patch', '/admin/milestones/1/toggle'],
    ['get', '/admin/achievements'], ['post', '/admin/achievements'], ['put', '/admin/achievements/1'], ['delete', '/admin/achievements/1'], ['patch', '/admin/achievements/1/toggle'],
    ['get', '/admin/statistics'], ['post', '/admin/statistics'], ['put', '/admin/statistics/1'], ['delete', '/admin/statistics/1'], ['patch', '/admin/statistics/1/toggle'],
]);

test('biography edit page loads without creating a record on get', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/biography')->assertOk()->assertSee('Save biography');

    $this->assertDatabaseCount('biographies', 0);
});

test('biography editing keeps a single official record and removes blank extra rows', function () {
    $data = biographyTestData(['additional_details' => [
        ['label' => null, 'value' => null, 'source_note' => null],
        ['label' => 'Home club', 'value' => 'Test club', 'source_note' => 'Test verification'],
    ], 'id' => 2]);

    $this->actingAs(User::factory()->admin()->create())->put('/admin/biography', $data)
        ->assertRedirect(route('admin.biography.edit'))->assertSessionHasNoErrors();
    $this->assertDatabaseCount('biographies', 1);
    $this->assertDatabaseHas('biographies', ['id' => 1, 'full_name' => 'Reviewed Athlete']);
    expect(Biography::findOrFail(1)->additional_details)->toHaveCount(1);
});

test('subsequent biography updates preserve singleton identity', function () {
    Biography::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->put('/admin/biography', biographyTestData())
        ->assertSessionHasNoErrors();
    $this->assertDatabaseCount('biographies', 1);
    $this->assertDatabaseHas('biographies', ['id' => 1, 'full_name' => 'Reviewed Athlete']);
});

test('biography validation rejects incomplete and conflicting personal details', function (array $overrides, string $field) {
    $this->actingAs(User::factory()->admin()->create())->put('/admin/biography', biographyTestData($overrides))
        ->assertSessionHasErrors($field);
    $this->assertDatabaseCount('biographies', 0);
})->with([
    [['full_name' => ''], 'full_name'],
    [['professional_title' => ''], 'professional_title'],
    [['date_of_birth' => '2999-01-01'], 'date_of_birth'],
    [['date_of_birth' => '1984-02-31'], 'date_of_birth'],
    [['professional_start_year' => 1800], 'professional_start_year'],
    [['date_of_birth' => '1984-01-01', 'professional_start_year' => 1980], 'professional_start_year'],
    [['seo_title' => str_repeat('a', 161)], 'seo_title'],
    [['source_note' => null], 'source_note'],
    [['additional_details' => [['label' => 'Club', 'value' => 'Test', 'source_note' => null]]], 'additional_details.0.source_note'],
    [['additional_details' => [['label' => 'Club', 'value' => 'Test', 'source_note' => 'Source', 'image' => 'bad']]], 'additional_details.0'],
]);

test('career create and edit forms use the existing layout', function (string $type) {
    $class = careerTestModel($type);
    $record = $class::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->get('/admin/'.$type.'/create')->assertOk()->assertSee('Save record');
    $this->get('/admin/'.$type.'/'.$record->id.'/edit')->assertOk()->assertSee('Confirm permanent deletion');
})->with(['milestones', 'achievements', 'statistics']);

test('administrators can create each career record type', function (string $type) {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/'.$type, careerTestData($type))
        ->assertRedirect(route('admin.'.$type.'.index'))->assertSessionHasNoErrors();

    $class = careerTestModel($type);
    expect($class::count())->toBe(1);
    expect($class::first()->is_published)->toBeTrue();
})->with(['milestones', 'achievements', 'statistics']);

test('administrators can update content and display order', function (string $type) {
    $class = careerTestModel($type);
    $record = $class::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->put('/admin/'.$type.'/'.$record->id,
        careerTestData($type, ['sort_order' => 42, 'description' => 'Edited description.']))
        ->assertSessionHasNoErrors();
    expect($record->fresh()->sort_order)->toBe(42);
    expect($record->fresh()->description)->toBe('Edited description.');
})->with(['milestones', 'achievements', 'statistics']);

test('administrators can delete each career record type', function (string $type) {
    $class = careerTestModel($type);
    $record = $class::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->delete('/admin/'.$type.'/'.$record->id)
        ->assertRedirect(route('admin.'.$type.'.index'));
    $this->assertModelMissing($record);
})->with(['milestones', 'achievements', 'statistics']);

test('career record publishing can be toggled', function (string $type) {
    $class = careerTestModel($type);
    $record = $class::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->patch('/admin/'.$type.'/'.$record->id.'/toggle')
        ->assertSessionHasNoErrors();
    expect($record->fresh()->is_published)->toBeTrue();
})->with(['milestones', 'achievements', 'statistics']);

test('publication shortcut cannot bypass the source requirement', function () {
    $record = CareerMilestone::factory()->create(['source_note' => null]);

    $this->actingAs(User::factory()->admin()->create())->patch('/admin/milestones/'.$record->id.'/toggle')
        ->assertSessionHasErrors('source_note');
    expect($record->fresh()->is_published)->toBeFalse();
});

test('milestone and achievement validation rejects invalid dates and order', function (array $overrides, string $field) {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/achievements', careerTestData('achievements', $overrides))
        ->assertSessionHasErrors($field);
    $this->assertDatabaseCount('achievements', 0);
})->with([
    [['title' => ''], 'title'],
    [['description' => ''], 'description'],
    [['year' => null, 'event_date' => null], 'year'],
    [['year' => 2013, 'event_date' => '2014-01-01'], 'year'],
    [['year' => null, 'event_date' => '2999-01-01'], 'event_date'],
    [['sort_order' => -1], 'sort_order'],
    [['sort_order' => 10001], 'sort_order'],
    [['category' => 'unknown'], 'category'],
    [['source_note' => null], 'source_note'],
    [['is_published' => 'invalid'], 'is_published'],
]);

test('exact dates may be supplied without a year', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/milestones',
        careerTestData('milestones', ['year' => null, 'event_date' => '2013-11-10']))->assertSessionHasNoErrors();

    expect(CareerMilestone::first()->event_date->format('Y-m-d'))->toBe('2013-11-10');
});

test('statistics require a label value and source even while draft', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/statistics',
        careerTestData('statistics', ['label' => null, 'value' => null, 'source_note' => null, 'is_published' => false]))
        ->assertSessionHasErrors(['label', 'value', 'source_note']);
});

test('achievement search filters and paginates without exposing other matches', function () {
    Achievement::factory()->count(17)->create(['title' => 'Matching tournament', 'category' => 'tour_victory']);
    Achievement::factory()->create(['title' => 'Unrelated achievement']);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/achievements?q=Matching&category=tour_victory&status=draft');

    $response->assertOk()->assertSee('Matching tournament')->assertDontSee('Unrelated achievement');
    expect($response->viewData('records')->total())->toBe(17);
    expect($response->viewData('records')->count())->toBe(15);
});

test('malformed search input is validated', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/achievements?q[]=bad')
        ->assertSessionHasErrors('q');
});

test('biography images upload and replace safely', function () {
    Storage::fake('public');
    $old = UploadedFile::fake()->image('old.jpg')->store('career', 'public');
    Biography::factory()->create(['profile_image' => $old]);

    $this->actingAs(User::factory()->admin()->create())->put('/admin/biography',
        biographyTestData(['profile_image' => UploadedFile::fake()->image('profile.png', 400, 600),
            'banner_image' => UploadedFile::fake()->image('banner.jpg', 1200, 600)]))->assertSessionHasNoErrors();
    $biography = Biography::findOrFail(1);
    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($biography->profile_image);
    Storage::disk('public')->assertExists($biography->banner_image);
    $this->get('/biography')->assertOk()->assertSee($biography->imageUrl('profile_image'), false)
        ->assertSee($biography->imageUrl('banner_image'), false);
});

test('career record images upload and replace safely', function (string $type) {
    Storage::fake('public');
    $old = UploadedFile::fake()->image('old.jpg')->store('career', 'public');
    $class = careerTestModel($type);
    $record = $class::factory()->create(['image' => $old]);

    $this->actingAs(User::factory()->admin()->create())->put('/admin/'.$type.'/'.$record->id,
        careerTestData($type, ['image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($record->fresh()->image);
})->with(['milestones', 'achievements']);

test('invalid uploads cannot change existing biography data', function (string $kind) {
    Storage::fake('public');
    $biography = Biography::factory()->create();
    $file = match ($kind) {
        'svg' => UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
        'php' => UploadedFile::fake()->createWithContent('bad.php', '<?php echo 1;'),
        'large' => UploadedFile::fake()->image('large.jpg')->size(5121),
        'small' => UploadedFile::fake()->image('small.jpg', 10, 10),
    };

    $this->actingAs(User::factory()->admin()->create())->put('/admin/biography',
        biographyTestData(['profile_image' => $file]))->assertSessionHasErrors('profile_image');
    expect($biography->fresh()->full_name)->toBe('Test Athlete');
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
})->with(['svg', 'php', 'large', 'small']);

test('image removal keeps biography content and removes only the managed image', function () {
    Storage::fake('public');
    $old = UploadedFile::fake()->image('profile.jpg')->store('career', 'public');
    Biography::factory()->create(['profile_image' => $old]);

    $this->actingAs(User::factory()->admin()->create())->put('/admin/biography',
        biographyTestData(['remove_profile_image' => true]))->assertSessionHasNoErrors();
    expect(Biography::findOrFail(1)->profile_image)->toBeNull();
    Storage::disk('public')->assertMissing($old);
});

test('deleting a career record deletes its managed image', function () {
    Storage::fake('public');
    $image = UploadedFile::fake()->image('milestone.jpg')->store('career', 'public');
    $record = CareerMilestone::factory()->create(['image' => $image]);

    $this->actingAs(User::factory()->admin()->create())->delete('/admin/milestones/'.$record->id)->assertRedirect();
    Storage::disk('public')->assertMissing($image);
});

test('failed saves clean new uploads and preserve old files and records', function () {
    Storage::fake('public');
    $old = UploadedFile::fake()->image('old.jpg')->store('career', 'public');
    $record = Achievement::factory()->create(['image' => $old]);
    Achievement::updating(function () {
        throw new RuntimeException('Simulated save error');
    });

    $this->actingAs(User::factory()->admin()->create())->put('/admin/achievements/'.$record->id,
        careerTestData('achievements', ['image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))
        ->assertSessionHasErrors('save');
    expect($record->fresh()->image)->toBe($old);
    expect(Storage::disk('public')->allFiles())->toBe([$old]);
});

test('record deletion never removes unrelated storage paths', function () {
    Storage::fake('public');
    Storage::disk('public')->put('other/keep.jpg', 'keep');
    $record = Achievement::factory()->create(['image' => 'other/keep.jpg']);

    $this->actingAs(User::factory()->admin()->create())->delete('/admin/achievements/'.$record->id)->assertRedirect();
    Storage::disk('public')->assertExists('other/keep.jpg');
});

test('the public biography escapes text and SEO fields and preserves reviewed profile values', function () {
    Biography::factory()->published()->create([
        'full_name' => '<script>alert(1)</script>', 'birthplace' => 'Test birthplace', 'professional_start_year' => 2005,
        'date_of_birth' => '1984-11-20', 'seo_title' => 'Test SEO', 'seo_description' => '" onload="alert(1)',
    ]);

    $this->get('/biography')->assertOk()->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false)->assertSee('Test birthplace')
        ->assertSee('20 November 1984')->assertSee('Test SEO')
        ->assertDontSee('content="" onload="alert(1)', false)->assertSee('Synthetic second paragraph.');
});

test('draft biography and career records do not leak to public pages', function () {
    Biography::factory()->create(['full_name' => 'Private Athlete', 'short_introduction' => 'Private intro']);
    CareerMilestone::factory()->create(['title' => 'Private milestone']);
    Achievement::factory()->create(['title' => 'Private achievement', 'is_featured' => true]);
    CareerStatistic::factory()->create(['label' => 'Private statistic']);

    $this->get('/biography')->assertOk()->assertDontSee('Private Athlete')->assertDontSee('Private milestone')
        ->assertDontSee('Private achievement')->assertDontSee('Private statistic');
    $this->get('/')->assertOk()->assertDontSee('Private intro')->assertDontSee('Private achievement');
});

test('published records follow saved display order and show verification notes', function () {
    CareerMilestone::factory()->published()->create(['title' => 'Later milestone', 'sort_order' => 20]);
    CareerMilestone::factory()->published()->create(['title' => 'Earlier milestone', 'sort_order' => 1]);
    Achievement::factory()->published()->create(['title' => 'Reviewed achievement']);
    CareerStatistic::factory()->published()->create(['label' => 'Reviewed metric']);

    $this->get('/biography')->assertOk()->assertSeeInOrder(['Earlier milestone', 'Later milestone'])
        ->assertSee('Reviewed achievement')->assertSee('Reviewed metric')->assertSee('Test source');
});

test('homepage previews use only published biography featured achievements and manual statistics', function () {
    Biography::factory()->published()->create(['short_introduction' => 'Published homepage introduction']);
    Achievement::factory()->published()->create(['title' => 'Featured homepage achievement', 'is_featured' => true]);
    Achievement::factory()->published()->create(['title' => 'Unfeatured achievement']);
    CareerStatistic::factory()->published()->create(['label' => 'Published manual metric']);

    $this->get('/')->assertOk()->assertSee('Published homepage introduction')->assertSee('Featured homepage achievement')
        ->assertSee('Published manual metric')->assertDontSee('Unfeatured achievement');
});

test('statistics are never generated from achievement counts', function () {
    Achievement::factory()->published()->count(4)->create();

    $this->get('/biography')->assertOk()->assertSee('No verified career metrics have been published.');
    $this->assertDatabaseCount('career_statistics', 0);
});

test('missing biography images render without broken storage urls', function () {
    Storage::fake('public');
    Biography::factory()->published()->create(['profile_image' => 'career/'.str_repeat('a', 40).'.jpg']);

    $this->get('/biography')->assertOk()->assertDontSee('storage/career/', false)
        ->assertSee('Approved athlete photography is awaiting upload.');
});

test('legacy biography and achievements module links redirect to functional screens', function (string $type, string $route) {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/modules/'.$type)->assertRedirect(route($route));
})->with([['biography', 'admin.biography.edit'], ['achievements', 'admin.achievements.index']]);

test('biography and career seeds are idempotent and preserve edits', function () {
    $this->seed(BiographySeeder::class);
    $this->seed(VerifiedCareerSeeder::class);
    Biography::findOrFail(1)->update(['full_name' => 'Keep edited name']);
    Achievement::firstOrFail()->update(['description' => 'Keep edited description', 'is_published' => false]);
    $this->seed(BiographySeeder::class);
    $this->seed(VerifiedCareerSeeder::class);

    $this->assertDatabaseCount('biographies', 1);
    $this->assertDatabaseCount('achievements', 1);
    $this->assertDatabaseHas('biographies', ['full_name' => 'Keep edited name']);
    $this->assertDatabaseHas('achievements', ['description' => 'Keep edited description', 'is_published' => false]);
    $this->assertDatabaseCount('career_statistics', 0);
});

test('missing career record ids return not found', function (string $type) {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/'.$type.'/99999/edit')->assertNotFound();
})->with(['milestones', 'achievements', 'statistics']);

test('career writes enforce csrf outside the test bypass', function (string $path) {
    $this->app->instance('env', 'local');

    $this->actingAs(User::factory()->admin()->create())->post($path)->assertStatus(419);
})->with(['/admin/milestones', '/admin/achievements', '/admin/statistics']);
