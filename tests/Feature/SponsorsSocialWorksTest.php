<?php

use App\Models\SocialWork;
use App\Models\SocialWorkImage;
use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
function sponsorData(array $overrides = []): array
{
    return array_replace(['name' => 'Fixture partnership', 'slug' => 'fixture-partnership', 'description' => 'Synthetic partnership fixture.', 'category' => 'Equipment', 'website_url' => 'https://example.com', 'is_published' => true, 'is_featured' => false, 'sort_order' => 0], $overrides);
}
function communityData(array $overrides = []): array
{
    return array_replace(['title' => 'Fixture project', 'slug' => 'fixture-project', 'short_description' => 'Fixture summary', 'full_description' => 'Fixture full text', 'activity_date' => '2020-01-01', 'is_published' => true, 'is_featured' => false, 'sort_order' => 0], $overrides);
}
dataset('community protected routes', [
    ['get', '/admin/sponsors'], ['get', '/admin/sponsors/create'], ['post', '/admin/sponsors'], ['get', '/admin/sponsors/{s}/edit'], ['put', '/admin/sponsors/{s}'], ['delete', '/admin/sponsors/{s}'], ['patch', '/admin/sponsors/{s}/toggle'],
    ['get', '/admin/social-works'], ['get', '/admin/social-works/create'], ['post', '/admin/social-works'], ['get', '/admin/social-works/{p}/edit'], ['put', '/admin/social-works/{p}'], ['delete', '/admin/social-works/{p}'], ['patch', '/admin/social-works/{p}/toggle'],
    ['get', '/admin/social-works/{p}/images/{i}/edit'], ['put', '/admin/social-works/{p}/images/{i}'], ['delete', '/admin/social-works/{p}/images/{i}'],
]);
test('community routes reject unauthorized accounts', function (string $method, string $url, bool $ordinary) {
    $s = Sponsor::factory()->create();
    $p = SocialWork::factory()->create();
    $i = SocialWorkImage::factory()->for($p)->create();
    $url = str_replace(['{s}', '{p}', '{i}'], [$s->id, $p->id, $i->id], $url);
    if ($ordinary) {
        $this->actingAs(User::factory()->create())->$method($url)->assertForbidden();
    } else {
        $this->$method($url)->assertRedirect(route('admin.login'));
    }
})->with('community protected routes')->with([false, true]);
test('community admin views render', function (string $url) {
    $s = Sponsor::factory()->create();
    $p = SocialWork::factory()->create();
    $i = SocialWorkImage::factory()->for($p)->create();
    $url = str_replace(['{s}', '{p}', '{i}'], [$s->id, $p->id, $i->id], $url);
    $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk()->assertSee('name="_token"', false);
})->with(['/admin/sponsors', '/admin/sponsors/create', '/admin/sponsors/{s}/edit', '/admin/social-works', '/admin/social-works/create', '/admin/social-works/{p}/edit', '/admin/social-works/{p}/images/{i}/edit']);
test('sponsor CRUD uploads replaces removes and deletes logos', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create());
    $this->post('/admin/sponsors', sponsorData(['logo' => UploadedFile::fake()->image('logo.png', 32, 32)]))->assertSessionHasNoErrors()->assertRedirect(route('admin.sponsors.index'));
    $s = Sponsor::firstOrFail();
    $old = $s->logo;
    Storage::disk('public')->assertExists($old);
    $this->put(route('admin.sponsors.update', $s), sponsorData(['name' => 'Updated fixture', 'sort_order' => 7, 'logo' => UploadedFile::fake()->image('new.png', 100, 100)]))->assertSessionHasNoErrors();
    expect($s->fresh()->name)->toBe('Updated fixture');
    expect($s->fresh()->sort_order)->toBe(7);
    Storage::disk('public')->assertMissing($old);
    $new = $s->fresh()->logo;
    $this->put(route('admin.sponsors.update', $s), sponsorData(['remove_logo' => 1]))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($new);
    expect($s->fresh()->logo)->toBeNull();
    $this->patch(route('admin.sponsors.toggle', $s), ['field' => 'is_featured'])->assertSessionHasNoErrors();
    expect($s->fresh()->is_featured)->toBeTrue();
    $this->patch(route('admin.sponsors.toggle', $s), ['field' => 'is_published'])->assertSessionHasNoErrors();
    expect($s->fresh()->is_published)->toBeFalse();
    $this->delete(route('admin.sponsors.destroy', $s))->assertRedirect();
    $this->assertModelMissing($s);
});
test('sponsor validation rejects unsafe or invalid fields', function (array $override, string $field) {
    Sponsor::factory()->create(['slug' => 'duplicate']);
    $this->actingAs(User::factory()->admin()->create())->post('/admin/sponsors', sponsorData($override))->assertSessionHasErrors($field);
})->with([[['name' => ''], 'name'], [['slug' => 'duplicate'], 'slug'], [['slug' => '../unsafe'], 'slug'], [['website_url' => 'javascript:alert(1)'], 'website_url'], [['source_url' => 'https://user:pass@example.com'], 'source_url'], [['partnership_start_date' => '2025-02-01', 'partnership_end_date' => '2025-01-01'], 'partnership_end_date'], [['sort_order' => -1], 'sort_order'], [['is_published' => 'maybe'], 'is_published']]);
test('end only partnership dates are supported without inventing a start', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/sponsors', sponsorData(['partnership_end_date' => '2024-01-01']))->assertSessionHasNoErrors();
    expect(Sponsor::firstOrFail()->partnership_start_date)->toBeNull();
});
test('community upload validation rejects unsafe files', function (string $type) {
    Storage::fake('public');
    $file = match ($type) {
        'svg' => UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),'oversize' => UploadedFile::fake()->image('large.jpg', 200, 200)->size(5121),'tiny' => UploadedFile::fake()->image('tiny.png', 8, 8),default => UploadedFile::fake()->create('file.php', 2, 'application/x-php')
    };
    $this->actingAs(User::factory()->admin()->create())->post('/admin/sponsors', sponsorData(['logo' => $file]))->assertSessionHasErrors('logo');
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
})->with(['svg', 'oversize', 'tiny', 'php']);
test('social work CRUD supports batch uploads replacement ordering and cascading cleanup', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create());
    $this->post('/admin/social-works', communityData(['image' => UploadedFile::fake()->image('main.jpg', 300, 300), 'images' => [UploadedFile::fake()->image('a.jpg', 200, 200), UploadedFile::fake()->image('b.jpg', 200, 200)], 'gallery_alt_text' => 'Fixture batch photograph']))->assertSessionHasNoErrors();
    $p = SocialWork::firstOrFail();
    expect($p->images()->count())->toBe(2);
    $main = $p->image;
    $child = $p->images()->first();
    $childOld = $child->image;
    $this->put(route('admin.social-work-images.update', [$p, $child]), ['caption' => 'Revised caption', 'alt_text' => 'Revised alternative', 'sort_order' => 4, 'image' => UploadedFile::fake()->image('replacement.jpg', 200, 200)])->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($childOld);
    expect($child->fresh()->sort_order)->toBe(4);
    $this->put(route('admin.social-works.update', $p), communityData(['title' => 'Edited project', 'image' => UploadedFile::fake()->image('main2.jpg', 200, 200), 'images' => [UploadedFile::fake()->image('c.jpg', 200, 200)], 'gallery_alt_text' => 'Added fixture']))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($main);
    expect($p->fresh()->title)->toBe('Edited project');
    expect($p->images()->count())->toBe(3);
    $path = $child->fresh()->image;
    $this->delete(route('admin.social-work-images.destroy', [$p, $child]))->assertRedirect();
    Storage::disk('public')->assertMissing($path);
    $this->delete(route('admin.social-works.destroy', $p))->assertRedirect();
    expect(SocialWorkImage::count())->toBe(0);
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});
test('social works validate publication details dates urls and gallery inputs', function (array $override, string $field) {
    SocialWork::factory()->create(['slug' => 'duplicate']);
    $this->actingAs(User::factory()->admin()->create())->post('/admin/social-works', communityData($override))->assertSessionHasErrors($field);
})->with([[['title' => ''], 'title'], [['slug' => 'duplicate'], 'slug'], [['slug' => 'Bad Slug'], 'slug'], [['short_description' => null], 'short_description'], [['full_description' => null], 'full_description'], [['activity_date' => null], 'activity_date'], [['activity_date' => '2100-01-01'], 'activity_date'], [['activity_date' => '2020-02-30'], 'activity_date'], [['source_url' => 'data:text/html,unsafe'], 'source_url'], [['seo_title' => str_repeat('a', 161)], 'seo_title'], [['sort_order' => 10001], 'sort_order']]);
test('draft projects cannot bypass publication requirements using toggle', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/social-works', communityData(['is_published' => false, 'short_description' => null, 'full_description' => null, 'activity_date' => null]))->assertSessionHasNoErrors();
    $p = SocialWork::firstOrFail();
    $this->patch(route('admin.social-works.toggle', $p), ['field' => 'is_published'])->assertSessionHasErrors();
    expect($p->fresh()->is_published)->toBeFalse();
    $this->put(route('admin.social-works.update', $p), communityData(['is_published' => false]))->assertSessionHasNoErrors();
    $this->patch(route('admin.social-works.toggle', $p), ['field' => 'is_published'])->assertSessionHasNoErrors();
    expect($p->fresh()->is_published)->toBeTrue();
    $this->patch(route('admin.social-works.toggle', $p), ['field' => 'is_featured'])->assertSessionHasNoErrors();
    expect($p->fresh()->is_featured)->toBeTrue();
    $this->patch(route('admin.social-works.toggle', $p), ['field' => 'title'])->assertSessionHasErrors('field');
});
test('image routes enforce parent scope', function (string $method, string $suffix) {
    $p = SocialWork::factory()->create();
    $other = SocialWorkImage::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->$method('/admin/social-works/'.$p->id.'/images/'.$other->id.$suffix, ['alt_text' => 'Changed', 'sort_order' => 0])->assertNotFound();
})->with([['get', '/edit'], ['put', ''], ['delete', '']]);
test('batch upload validation leaves original project and files untouched', function () {
    Storage::fake('public');
    $p = SocialWork::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->put(route('admin.social-works.update', $p), communityData(['images' => [UploadedFile::fake()->image('a.jpg', 200, 200)], 'gallery_alt_text' => null]))->assertSessionHasErrors('gallery_alt_text');
    expect($p->fresh()->title)->toBe($p->title);
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
    $this->put(route('admin.social-works.update', $p), communityData(['images' => array_map(fn () => UploadedFile::fake()->image('a.jpg', 200, 200), range(1, 11)), 'gallery_alt_text' => 'Batch']))->assertSessionHasErrors('images');
});
test('shared community media remain until the final reference is deleted', function () {
    Storage::fake('public');
    $path = 'community/'.str_repeat('a', 40).'.jpg';
    Storage::disk('public')->put($path, 'fixture');
    $s = Sponsor::factory()->create(['logo' => $path]);
    $p = SocialWork::factory()->create(['image' => $path]);
    $i = SocialWorkImage::factory()->for($p)->create(['image' => $path]);
    $this->actingAs(User::factory()->admin()->create());
    $this->delete(route('admin.sponsors.destroy', $s))->assertRedirect();
    Storage::disk('public')->assertExists($path);
    $this->delete(route('admin.social-work-images.destroy', [$p, $i]))->assertRedirect();
    Storage::disk('public')->assertExists($path);
    $this->delete(route('admin.social-works.destroy', $p))->assertRedirect();
    Storage::disk('public')->assertMissing($path);
});
test('unmanaged file paths are never deleted', function () {
    Storage::fake('public');
    Storage::disk('public')->put('other/legacy.jpg', 'fixture');
    $s = Sponsor::factory()->create(['logo' => 'other/legacy.jpg']);
    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.sponsors.destroy', $s));
    Storage::disk('public')->assertExists('other/legacy.jpg');
});
test('admin lists apply search category publication featured and pagination', function () {
    $this->actingAs(User::factory()->admin()->create());
    Sponsor::factory()->count(16)->create(['category' => 'Other']);
    $s = Sponsor::factory()->create(['name' => 'Unique filter fixture', 'category' => 'Equipment', 'is_published' => false, 'is_featured' => true]);
    $this->get('/admin/sponsors?q=Unique&category=Equipment&publication=draft&featured=yes')->assertOk()->assertViewHas('sponsors', fn ($rows) => $rows->total() === 1 && $rows->first()->id === $s->id);
    $this->get('/admin/sponsors')->assertViewHas('sponsors', fn ($rows) => $rows->count() === 15 && $rows->total() === 17);
    SocialWork::factory()->create(['title' => 'Unique project filter', 'is_published' => false, 'is_featured' => true]);
    SocialWork::factory()->create();
    $this->get('/admin/social-works?q=Unique&publication=draft&featured=yes')->assertOk()->assertViewHas('projects', fn ($rows) => $rows->total() === 1);
});
test('public community pages show published records escape text and filter', function () {
    $s = Sponsor::factory()->create(['name' => 'Visible sponsor fixture', 'category' => 'Equipment', 'is_featured' => true, 'description' => '<script>alert(1)</script>']);
    Sponsor::factory()->create(['name' => 'Secret sponsor fixture', 'is_published' => false]);
    Sponsor::factory()->create(['category' => 'Other']);
    $p = SocialWork::factory()->create(['title' => 'Visible project fixture', 'is_featured' => true, 'seo_title' => 'Fixture SEO', 'location' => 'Fixture city', 'full_description' => '<script>bad()</script>']);
    $hidden = SocialWork::factory()->create(['title' => 'Secret project fixture', 'is_published' => false]);
    $this->get('/sponsors?category=Equipment&featured=yes')->assertOk()->assertSee($s->name)->assertDontSee('Secret sponsor fixture')->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertViewHas('sponsors', fn ($r) => $r->total() === 1);
    $this->get('/social-works?q=Visible&featured=yes')->assertOk()->assertSee($p->title)->assertDontSee($hidden->title);
    $this->get(route('social-works.show', $p->slug))->assertOk()->assertSee('Fixture SEO')->assertSee('Fixture city')->assertSee('&lt;script&gt;bad()&lt;/script&gt;', false)->assertDontSee($hidden->title);
    $this->get(route('social-works.show', $hidden->slug))->assertNotFound();
});
test('public detail renders gallery lightbox ordering and related projects', function () {
    Storage::fake('public');
    $p = SocialWork::factory()->create();
    $a = SocialWorkImage::factory()->for($p)->create(['sort_order' => 10, 'caption' => 'Later photograph']);
    $b = SocialWorkImage::factory()->for($p)->create(['sort_order' => 1, 'caption' => 'Earlier photograph']);
    foreach ([$a, $b] as $i) {
        Storage::disk('public')->put($i->image, 'fixture');
    }
    SocialWork::factory()->create(['title' => 'Related visible fixture']);
    SocialWork::factory()->create(['title' => 'Private related fixture', 'is_published' => false]);
    $this->get(route('social-works.show', $p->slug))->assertOk()->assertSee('data-gallery=', false)->assertSeeInOrder(['Earlier photograph', 'Later photograph'])->assertSee('Related visible fixture')->assertDontSee('Private related fixture')->assertViewHas('related', fn ($rows) => ! $rows->contains('id', $p->id));
});
test('homepage community previews show only featured published content', function () {
    Sponsor::factory()->create(['name' => 'Home featured sponsor', 'is_featured' => true]);
    Sponsor::factory()->create(['name' => 'Hidden featured sponsor', 'is_featured' => true, 'is_published' => false]);
    Sponsor::factory()->create(['name' => 'Unfeatured sponsor']);
    SocialWork::factory()->create(['title' => 'Home featured project', 'is_featured' => true]);
    SocialWork::factory()->create(['title' => 'Hidden featured project', 'is_featured' => true, 'is_published' => false]);
    SocialWork::factory()->create(['title' => 'Unfeatured project']);
    $this->get('/')->assertOk()->assertSee('Home featured sponsor')->assertSee('Home featured project')->assertDontSee('Hidden featured sponsor')->assertDontSee('Hidden featured project')->assertDontSee('Unfeatured sponsor')->assertDontSee('Unfeatured project');
});
test('community public pages and homepage have honest empty states', function () {
    $this->get('/sponsors')->assertOk()->assertSee('No verified sponsors');
    $this->get('/social-works')->assertOk()->assertSee('No verified social projects');
    $this->get('/')->assertOk()->assertSee('No featured');
});
test('legacy community module links redirect to functional lists', function () {
    $this->actingAs(User::factory()->admin()->create());
    $this->get('/admin/modules/sponsors')->assertRedirect(route('admin.sponsors.index'));
    $this->get('/admin/modules/social-works')->assertRedirect(route('admin.social-works.index'));
});

test('failed gallery insert rolls back project updates and cleans new uploads', function () {
    Storage::fake('public');
    $old = 'community/'.str_repeat('b', 40).'.jpg';
    Storage::disk('public')->put($old, 'original fixture');
    $p = SocialWork::factory()->create(['image' => $old]);
    $event = 'eloquent.creating: '.SocialWorkImage::class;
    Event::listen($event, function () {
        throw new RuntimeException('Simulated database insert failure');
    });
    try {
        $this->actingAs(User::factory()->admin()->create())->put(route('admin.social-works.update', $p), communityData(['image' => UploadedFile::fake()->image('new.jpg', 200, 200), 'images' => [UploadedFile::fake()->image('child.jpg', 200, 200)], 'gallery_alt_text' => 'Fixture']))->assertSessionHasErrors('save');
    } finally {
        Event::forget($event);
    }
    expect($p->fresh()->title)->toBe($p->title);
    expect($p->fresh()->image)->toBe($old);
    expect($p->images()->count())->toBe(0);
    expect(Storage::disk('public')->allFiles())->toBe([$old]);
});
test('public ordering and missing image fallbacks are safe', function () {
    Storage::fake('public');
    Sponsor::factory()->create(['name' => 'Later sponsor fixture', 'sort_order' => 9]);
    Sponsor::factory()->create(['name' => 'Earlier sponsor fixture', 'sort_order' => 1]);
    $p = SocialWork::factory()->create(['title' => 'Earlier project fixture', 'sort_order' => 1]);
    SocialWork::factory()->create(['title' => 'Later project fixture', 'sort_order' => 9]);
    SocialWorkImage::factory()->for($p)->create();
    $this->get('/sponsors')->assertOk()->assertSeeInOrder(['Earlier sponsor fixture', 'Later sponsor fixture']);
    $this->get('/social-works')->assertOk()->assertSeeInOrder(['Earlier project fixture', 'Later project fixture']);
    $this->get(route('social-works.show', $p->slug))->assertOk()->assertSee('This photograph is currently unavailable.');
    $this->get('/sponsors?publication=draft')->assertSessionHasErrors('publication');
    $this->get('/social-works?featured=invalid')->assertSessionHasErrors('featured');
});

test('community writes enforce csrf protection', function (string $url) {
    $this->app->instance('env', 'local');
    $this->actingAs(User::factory()->admin()->create())->post($url)->assertStatus(419);
})->with(['/admin/sponsors', '/admin/social-works']);
