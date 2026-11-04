<?php

use App\Models\HomepageSection;
use App\Models\User;
use Database\Seeders\HomepageSectionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('homepage CRUD endpoints redirect guests', function (string $method, string $path) {
    $section = HomepageSection::factory()->create();

    $this->$method(str_replace('{id}', (string) $section->id, $path))
        ->assertRedirect(route('admin.login'));
})->with([
    ['get', '/admin/homepage'], ['get', '/admin/homepage/create'],
    ['get', '/admin/homepage/{id}/edit'], ['post', '/admin/homepage'],
    ['put', '/admin/homepage/{id}'], ['patch', '/admin/homepage/{id}/toggle'],
    ['delete', '/admin/homepage/{id}'],
]);

test('homepage CRUD endpoints forbid ordinary users', function (string $method, string $path) {
    $section = HomepageSection::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)->$method(str_replace('{id}', (string) $section->id, $path))
        ->assertForbidden();
})->with([
    ['get', '/admin/homepage'], ['get', '/admin/homepage/create'],
    ['get', '/admin/homepage/{id}/edit'], ['post', '/admin/homepage'],
    ['put', '/admin/homepage/{id}'], ['patch', '/admin/homepage/{id}/toggle'],
    ['delete', '/admin/homepage/{id}'],
]);

test('admin can list and edit sections with a real homepage sidebar link', function () {
    $section = HomepageSection::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->get('/admin/homepage')
        ->assertOk()->assertSee('Shape the first impression.')
        ->assertSee(route('admin.homepage.edit', $section), false);
});

test('every existing section edit form renders its content fields', function (string $key) {
    $section = HomepageSection::factory()->section($key)->create();

    $this->actingAs(User::factory()->admin()->create())->get(route('admin.homepage.edit', $section))
        ->assertOk()->assertSee('Save homepage section')->assertSee('name="sort_order"', false);
})->with(['hero', 'introduction', 'career', 'journey', 'schedule', 'journal', 'media', 'partners', 'community', 'contact']);

test('admin can view available section creation choices', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/homepage/create')
        ->assertOk()->assertSee('Welcome & Introduction');
});

test('admin can add a missing section using its default content', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/homepage', ['section_key' => 'hero'])
        ->assertRedirect(route('admin.homepage.index'));

    $this->assertDatabaseHas('homepage_sections', ['section_key' => 'hero', 'title' => "A game.\nA journey.\nA purpose."]);
});

test('section creation rejects unknown or duplicate layouts', function (string $key) {
    HomepageSection::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->post('/admin/homepage', ['section_key' => $key])
        ->assertSessionHasErrors('section_key');
    $this->assertDatabaseCount('homepage_sections', 1);
})->with(['hero', 'unknown']);

test('admin can save homepage content and presentation settings', function () {
    $section = HomepageSection::factory()->create();
    $data = array_replace(config('homepage.hero.defaults'), [
        'title' => "A new game.\nA new purpose.", 'description' => 'Updated introduction.',
        'button_text' => 'Explore media', 'button_url' => '#media',
        'additional_data' => ['eyebrow' => 'A new chapter'], 'sort_order' => 7, 'is_active' => false,
    ]);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section), $data)
        ->assertRedirect(route('admin.homepage.edit', $section))->assertSessionHas('status');
    $section->refresh();
    expect($section->title)->toBe("A new game.\nA new purpose.");
    expect($section->is_active)->toBeFalse();
    expect($section->sort_order)->toBe(7);
    expect($section->additional_data['eyebrow'])->toBe('A new chapter');
    expect($section->section_key)->toBe('hero');
});

test('invalid content is rejected without changing the record', function (array $overrides, string $error) {
    $section = HomepageSection::factory()->create();
    $original = $section->title;
    $data = array_replace(config('homepage.hero.defaults'), $overrides);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section), $data)
        ->assertSessionHasErrors($error);
    expect($section->fresh()->title)->toBe($original);
})->with([
    [['title' => ''], 'title'],
    [['title' => str_repeat('A', 501)], 'title'],
    [['sort_order' => -1], 'sort_order'],
    [['sort_order' => 10001], 'sort_order'],
    [['is_active' => 'invalid'], 'is_active'],
    [['additional_data' => ['_secondary_image' => '../../private.txt']], 'additional_data'],
    [['additional_data' => ['eyebrow' => ['nested']]], 'additional_data.eyebrow'],
]);

test('unsafe homepage links are rejected', function (string $link) {
    $section = HomepageSection::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.hero.defaults'), ['button_url' => $link]))
        ->assertSessionHasErrors('button_url');
})->with(['javascript:alert(1)', 'https://evil.example', '//evil.example', '/\\evil.example', '/%2fevil.example', " /contact\n//evil.example"]);

test('local homepage links are accepted', function (string $link) {
    $section = HomepageSection::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.hero.defaults'), ['button_url' => $link]))
        ->assertSessionHasNoErrors();
    expect($section->fresh()->button_url)->toBe($link);
})->with(['#journey', '/contact', '/']);

test('a valid image is stored and shown in the public homepage', function () {
    Storage::fake('public');
    $section = HomepageSection::factory()->create();
    $data = array_replace(config('homepage.hero.defaults'), ['image' => UploadedFile::fake()->image('hero.jpg', 1200, 800)]);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section), $data)
        ->assertSessionHasNoErrors();
    $path = $section->fresh()->image;
    Storage::disk('public')->assertExists($path);
    expect(HomepageSection::managedImage($path))->toBeTrue();
    $this->get('/')->assertOk()->assertSee(asset('storage/'.$path), false);
});

test('replacement removes the old upload only after saving the new image', function () {
    Storage::fake('public');
    $oldPath = UploadedFile::fake()->image('old.jpg')->store('homepage', 'public');
    $section = HomepageSection::factory()->create(['image' => $oldPath]);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.hero.defaults'), ['image' => UploadedFile::fake()->image('new.webp', 400, 300)]))
        ->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($section->fresh()->image);
});

test('invalid files preserve the current stored image', function (string $type) {
    Storage::fake('public');
    $oldPath = UploadedFile::fake()->image('old.jpg')->store('homepage', 'public');
    $section = HomepageSection::factory()->create(['image' => $oldPath]);
    $file = match ($type) {
        'svg' => UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
        'php' => UploadedFile::fake()->createWithContent('bad.php', '<?php echo 1;'),
        'large' => UploadedFile::fake()->image('large.jpg')->size(5121),
        'small' => UploadedFile::fake()->image('tiny.jpg', 10, 10),
        'extension' => UploadedFile::fake()->image('image.php', 400, 300),
    };

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.hero.defaults'), ['image' => $file]))->assertSessionHasErrors('image');
    expect($section->fresh()->image)->toBe($oldPath);
    Storage::disk('public')->assertExists($oldPath);
})->with(['svg', 'php', 'large', 'small', 'extension']);

test('failed database saves clean new files and retain old content and files', function () {
    Storage::fake('public');
    $oldPath = UploadedFile::fake()->image('old.jpg')->store('homepage', 'public');
    $section = HomepageSection::factory()->create(['image' => $oldPath]);
    $originalTitle = $section->title;
    HomepageSection::updating(function () {
        throw new RuntimeException('Simulated database save failure');
    });

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.hero.defaults'), ['title' => 'Changed', 'image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))
        ->assertSessionHasErrors('save');
    expect($section->fresh()->title)->toBe($originalTitle);
    expect($section->fresh()->image)->toBe($oldPath);
    expect(Storage::disk('public')->allFiles('homepage'))->toBe([$oldPath]);
});

test('admin can remove an image and return to the original fallback', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('old.jpg')->store('homepage', 'public');
    $section = HomepageSection::factory()->create(['image' => $path]);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.hero.defaults'), ['remove_image' => true]))->assertSessionHasNoErrors();
    expect($section->fresh()->image)->toBeNull();
    Storage::disk('public')->assertMissing($path);
    $this->get('/')->assertOk()->assertSee(asset('images/fairway.jpg'), false);
});

test('admin can upload and replace the secondary media image', function () {
    Storage::fake('public');
    $oldPath = UploadedFile::fake()->image('old.jpg')->store('homepage', 'public');
    $section = HomepageSection::factory()->section('media')->create(['additional_data' => ['_secondary_image' => $oldPath]]);
    $data = array_replace(config('homepage.media.defaults'), ['secondary_image' => UploadedFile::fake()->image('new.png', 400, 300)]);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section), $data)
        ->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($section->fresh()->additional_data['_secondary_image']);
    $this->get('/')->assertOk()->assertSee($section->fresh()->imageUrl('secondary'), false);
});

test('welcome image is optional and renders when uploaded', function () {
    Storage::fake('public');
    $section = HomepageSection::factory()->section('introduction')->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.introduction.defaults'), ['image' => UploadedFile::fake()->image('welcome.jpg', 500, 500)]))
        ->assertSessionHasNoErrors();
    $this->get('/')->assertOk()->assertSee($section->fresh()->imageUrl(), false);
});

test('homepage renders database content as escaped text without hiding missing sections', function () {
    HomepageSection::factory()->create(['title' => "<script>alert(1)</script>\nA second line.", 'additional_data' => ['eyebrow' => '<img src=x onerror=alert(1)>']]);

    $this->get('/')->assertOk()->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('<img src=x onerror=alert(1)>')
        ->assertDontSee('<img src=x onerror=alert(1)>', false)
        ->assertSee('A second line.')->assertSee('Inside the ropes.');
});

test('inactive sections and their navigation links are omitted', function () {
    HomepageSection::factory()->section('journey')->create(['is_active' => false]);

    $this->get('/')->assertOk()->assertDontSee('id="journey"', false)
        ->assertDontSee('href="#journey"', false)->assertSee('href="#main"', false);
});

test('public homepage sections follow database display order', function () {
    HomepageSection::factory()->section('community')->create(['sort_order' => 0, 'title' => 'Community first marker']);
    HomepageSection::factory()->create(['sort_order' => 999, 'title' => 'Hero later marker']);

    $this->get('/')->assertOk()->assertSeeInOrder(['Community first marker', 'Inside the ropes.', 'Hero later marker'])
        ->assertSee('data-solid-header="true"', false);
});

test('visibility toggle persists and removes the section from rendering', function () {
    $section = HomepageSection::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->patch(route('admin.homepage.toggle', $section))
        ->assertSessionHas('status');
    expect($section->fresh()->is_active)->toBeFalse();
    $this->get('/')->assertOk()->assertDontSee('id="top"', false);
});

test('deleted or missing image files use a working fallback', function () {
    Storage::fake('public');
    HomepageSection::factory()->create(['image' => 'homepage/'.str_repeat('a', 40).'.jpg']);

    $this->get('/')->assertOk()->assertSee(asset('images/fairway.jpg'), false)
        ->assertDontSee('storage/homepage/', false);
});

test('delete customization removes uploads and restores the default public section', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('hero.jpg')->store('homepage', 'public');
    $section = HomepageSection::factory()->create(['title' => 'Custom title to delete', 'image' => $path]);

    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.homepage.destroy', $section))
        ->assertRedirect(route('admin.homepage.index'));
    $this->assertModelMissing($section);
    Storage::disk('public')->assertMissing($path);
    $this->get('/')->assertOk()->assertDontSee('Custom title to delete')->assertSee('A game.');
});

test('cleanup never deletes paths outside managed homepage uploads', function () {
    Storage::fake('public');
    Storage::disk('public')->put('other/keep.jpg', 'keep');
    $section = HomepageSection::factory()->create(['image' => 'other/keep.jpg']);

    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.homepage.destroy', $section))
        ->assertRedirect();
    Storage::disk('public')->assertExists('other/keep.jpg');
});

test('seeding is idempotent and preserves edits and visibility', function () {
    HomepageSection::factory()->create(['title' => 'Keep this edit', 'is_active' => false]);
    $this->seed(HomepageSectionSeeder::class);
    $this->seed(HomepageSectionSeeder::class);

    $this->assertDatabaseCount('homepage_sections', 10);
    $this->assertDatabaseHas('homepage_sections', ['section_key' => 'hero', 'title' => 'Keep this edit', 'is_active' => false]);
});

test('unknown homepage section ids return not found', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/homepage/99999/edit')->assertNotFound();
});

test('homepage writes require csrf tokens', function () {
    $this->app->instance('env', 'local');
    $section = HomepageSection::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        config('homepage.hero.defaults'))->assertStatus(419);
});

test('every existing homepage layout renders saved titles', function (string $key) {
    HomepageSection::factory()->section($key)->create(['title' => 'Saved title for '.$key]);

    $this->get('/')->assertOk()->assertSee('Saved title for '.$key);
})->with(['hero', 'introduction', 'career', 'journey', 'schedule', 'journal', 'media', 'partners', 'community', 'contact']);

test('the video anchor remains usable while the media section is active', function () {
    HomepageSection::factory()->create(['button_url' => '#videos']);

    $this->get('/')->assertOk()->assertSee('href="#videos"', false)->assertSee('id="videos"', false);
});

test('layouts without image slots reject uploaded files', function () {
    Storage::fake('public');
    $section = HomepageSection::factory()->section('career')->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.career.defaults'), ['image' => UploadedFile::fake()->image('unused.jpg', 400, 300)]))
        ->assertSessionHasErrors('image');
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

test('a new upload takes priority over the remove image checkbox', function () {
    Storage::fake('public');
    $section = HomepageSection::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.hero.defaults'), ['remove_image' => true, 'image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))
        ->assertSessionHasNoErrors();
    Storage::disk('public')->assertExists($section->fresh()->image);
});

test('secondary image removal restores the original media image', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('secondary.jpg')->store('homepage', 'public');
    $section = HomepageSection::factory()->section('media')->create(['additional_data' => ['_secondary_image' => $path]]);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.homepage.update', $section),
        array_replace(config('homepage.media.defaults'), ['remove_secondary_image' => true]))
        ->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($path);
    expect($section->fresh()->imageUrl('secondary'))->toBe(asset('images/course.jpg'));
});

test('homepage html keeps the approved default sections in their original order', function () {
    $this->get('/')->assertOk()->assertSeeInOrder([
        'id="top"', 'id="introduction"', 'id="career"', 'id="journey"', 'id="schedule"',
        'id="journal"', 'id="media"', 'id="partners"', 'id="community"', 'id="contact"',
    ], false);
});
