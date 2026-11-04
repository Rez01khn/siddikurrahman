<?php

use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
function albumPayload(array $overrides = []): array
{
    return array_replace(['title' => 'Reviewed Album', 'slug' => 'reviewed-album', 'description' => 'Test album', 'is_published' => true, 'is_featured' => false, 'sort_order' => 0], $overrides);
}
function photoPayload(array $overrides = []): array
{
    return array_replace(['alt_text' => 'Reviewed photo description', 'caption' => 'Reviewed caption', 'is_published' => true, 'is_featured' => false, 'sort_order' => 0], $overrides);
}
test('gallery management redirects guests', function (string $method, string $url) {
    $album = PhotoAlbum::factory()->create();
    $photo = Photo::factory()->for($album, 'album')->create();
    $url = str_replace(['{album}', '{photo}'], [(string) $album->id, (string) $photo->id], $url);
    $this->$method($url)->assertRedirect(route('admin.login'));
})->with([
    ['get', '/admin/photo-albums'], ['get', '/admin/photo-albums/create'], ['post', '/admin/photo-albums'],
    ['get', '/admin/photo-albums/{album}/edit'], ['put', '/admin/photo-albums/{album}'], ['delete', '/admin/photo-albums/{album}'],
    ['get', '/admin/photos'], ['get', '/admin/photo-albums/{album}/photos/create'], ['post', '/admin/photo-albums/{album}/photos'],
    ['get', '/admin/photo-albums/{album}/photos/{photo}/edit'], ['put', '/admin/photo-albums/{album}/photos/{photo}'], ['delete', '/admin/photo-albums/{album}/photos/{photo}'],
]);
test('ordinary users cannot manage galleries', function (string $method, string $url) {
    $album = PhotoAlbum::factory()->create();
    $photo = Photo::factory()->for($album, 'album')->create();
    $url = str_replace(['{album}', '{photo}'], [(string) $album->id, (string) $photo->id], $url);
    $this->actingAs(User::factory()->create())->$method($url)->assertForbidden();
})->with([
    ['get', '/admin/photo-albums'], ['post', '/admin/photo-albums'], ['put', '/admin/photo-albums/{album}'], ['delete', '/admin/photo-albums/{album}'],
    ['get', '/admin/photos'], ['post', '/admin/photo-albums/{album}/photos'], ['put', '/admin/photo-albums/{album}/photos/{photo}'], ['delete', '/admin/photo-albums/{album}/photos/{photo}'],
]);
test('gallery admin screens render', function (string $url) {
    $album = PhotoAlbum::factory()->create();
    $photo = Photo::factory()->for($album, 'album')->create();
    $url = str_replace(['{album}', '{photo}'], [(string) $album->id, (string) $photo->id], $url);
    $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
})->with(['/admin/photo-albums', '/admin/photo-albums/create', '/admin/photo-albums/{album}/edit',
    '/admin/photos', '/admin/photo-albums/{album}/photos/create', '/admin/photo-albums/{album}/photos/{photo}/edit']);
test('album CRUD saves metadata and handles an image cover', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create())->post('/admin/photo-albums', albumPayload(['cover_image' => UploadedFile::fake()->image('cover.jpg', 400, 300)]))->assertSessionHasNoErrors();
    $album = PhotoAlbum::firstOrFail();
    $old = $album->cover_image;
    Storage::disk('public')->assertExists($old);
    $this->put(route('admin.photo-albums.update', $album), albumPayload(['title' => 'Edited Album', 'sort_order' => 20, 'cover_image' => UploadedFile::fake()->image('new.jpg', 500, 400)]))->assertSessionHasNoErrors();
    expect($album->fresh()->title)->toBe('Edited Album');
    expect($album->fresh()->sort_order)->toBe(20);
    Storage::disk('public')->assertMissing($old);
    $newPath = $album->fresh()->cover_image;
    $this->delete(route('admin.photo-albums.destroy', $album))->assertRedirect();
    Storage::disk('public')->assertMissing($newPath);
    $this->assertModelMissing($album);
});
test('album validation enforces slugs dates and ordering', function (array $override, string $field) {
    PhotoAlbum::factory()->create(['slug' => 'existing']);
    $this->actingAs(User::factory()->admin()->create())->post('/admin/photo-albums', albumPayload($override))->assertSessionHasErrors($field);
})->with([[['title' => ''], 'title'], [['slug' => 'existing'], 'slug'], [['slug' => '../bad'], 'slug'],
    [['album_date' => '2999-01-01'], 'album_date'], [['sort_order' => -1], 'sort_order']]);
test('multiple uploads create complete photo records with consecutive order', function () {
    Storage::fake('public');
    $album = PhotoAlbum::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->post(route('admin.photos.store', $album),
        photoPayload(['sort_order' => 10, 'images' => [UploadedFile::fake()->image('one.jpg', 500, 400), UploadedFile::fake()->image('two.png', 500, 400)]]))->assertSessionHasNoErrors();
    expect($album->photos()->count())->toBe(2);
    expect($album->photos()->orderBy('id')->pluck('sort_order')->all())->toBe([10, 11]);
    foreach ($album->photos as $photo) {
        Storage::disk('public')->assertExists($photo->image);
        expect($photo->alt_text)->toBe('Reviewed photo description');
    }
});
test('invalid batches create no records or files', function (string $kind) {
    Storage::fake('public');
    $album = PhotoAlbum::factory()->create();
    $bad = match ($kind) {
        'svg' => UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
        'php' => UploadedFile::fake()->createWithContent('bad.php', '<?php echo 1;'),
        'large' => UploadedFile::fake()->image('large.jpg')->size(5121),
        'tiny' => UploadedFile::fake()->image('tiny.jpg', 10, 10),
    };
    $this->actingAs(User::factory()->admin()->create())->post(route('admin.photos.store', $album),
        photoPayload(['images' => [UploadedFile::fake()->image('valid.jpg', 400, 300), $bad]]))->assertSessionHasErrors('images.1');
    $this->assertDatabaseCount('photos', 0);
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
})->with(['svg', 'php', 'large', 'tiny']);
test('batch uploads require alt text and one to ten files', function () {
    Storage::fake('public');
    $album = PhotoAlbum::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->post(route('admin.photos.store', $album), photoPayload(['alt_text' => '', 'images' => []]))
        ->assertSessionHasErrors(['alt_text', 'images']);
});
test('photo replacement updates metadata and removes an unused old image', function () {
    Storage::fake('public');
    $album = PhotoAlbum::factory()->create();
    $path = UploadedFile::fake()->image('old.jpg')->store('gallery', 'public');
    $photo = Photo::factory()->for($album, 'album')->create(['image' => $path]);
    $this->actingAs(User::factory()->admin()->create())->put(route('admin.photos.update', [$album, $photo]),
        photoPayload(['caption' => 'Edited caption', 'sort_order' => 30, 'image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($path);
    Storage::disk('public')->assertExists($photo->fresh()->image);
    expect($photo->fresh()->caption)->toBe('Edited caption');
    expect($photo->fresh()->sort_order)->toBe(30);
});
test('nested photo routes cannot access photos belonging to another album', function (string $method, string $suffix) {
    $album = PhotoAlbum::factory()->create();
    $other = PhotoAlbum::factory()->create();
    $photo = Photo::factory()->for($other, 'album')->create();
    $this->actingAs(User::factory()->admin()->create())->$method('/admin/photo-albums/'.$album->id.'/photos/'.$photo->id.$suffix, photoPayload())->assertNotFound();
    $this->assertModelExists($photo);
})->with([['get', '/edit'], ['put', ''], ['delete', '']]);
test('shared photos and cover files are kept until their last reference is deleted', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('shared.jpg')->store('gallery', 'public');
    $album = PhotoAlbum::factory()->create(['cover_image' => $path]);
    $other = PhotoAlbum::factory()->create();
    $photo = Photo::factory()->for($other, 'album')->create(['image' => $path]);
    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.photo-albums.destroy', $album))->assertRedirect();
    Storage::disk('public')->assertExists($path);
    $this->delete(route('admin.photos.destroy', [$other, $photo]))->assertRedirect();
    Storage::disk('public')->assertMissing($path);
});
test('album deletion preserves files shared with video thumbnails', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('shared.jpg')->store('gallery', 'public');
    $album = PhotoAlbum::factory()->create(['cover_image' => $path]);
    Photo::factory()->for($album, 'album')->create(['image' => $path]);
    Video::factory()->create(['image' => $path]);
    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.photo-albums.destroy', $album))->assertRedirect();
    $this->assertModelMissing($album);
    $this->assertDatabaseCount('photos', 0);
    Storage::disk('public')->assertExists($path);
});
test('replacement retains the old image when another photo references it', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('shared.jpg')->store('gallery', 'public');
    $album = PhotoAlbum::factory()->create();
    $photo = Photo::factory()->for($album, 'album')->create(['image' => $path]);
    Photo::factory()->for($album, 'album')->create(['image' => $path]);
    $this->actingAs(User::factory()->admin()->create())->put(route('admin.photos.update', [$album, $photo]),
        photoPayload(['image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))->assertSessionHasNoErrors();
    Storage::disk('public')->assertExists($path);
});
test('failed batch saves roll back all records and remove new uploads', function () {
    Storage::fake('public');
    $album = PhotoAlbum::factory()->create();
    Photo::creating(function () {
        throw new RuntimeException('Test batch failure');
    });
    $this->actingAs(User::factory()->admin()->create())->post(route('admin.photos.store', $album),
        photoPayload(['images' => [UploadedFile::fake()->image('one.jpg', 400, 300), UploadedFile::fake()->image('two.jpg', 400, 300)]]))->assertSessionHasErrors('save');
    $this->assertDatabaseCount('photos', 0);
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});
test('photo filters search publication and album with pagination', function () {
    $album = PhotoAlbum::factory()->create();
    Photo::factory()->for($album, 'album')->published()->count(23)->create(['caption' => 'Matching']);
    $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/photos?album='.$album->id.'&q=Matching&publication=published');
    $response->assertOk();
    expect($response->viewData('photos')->total())->toBe(23);
    expect($response->viewData('photos')->count())->toBe(20);
});
test('public galleries exclude draft albums and draft photo metadata', function () {
    $album = PhotoAlbum::factory()->create(['title' => 'Private Album']);
    Photo::factory()->for($album, 'album')->published()->create(['caption' => 'Private photo']);
    $this->get('/photos')->assertOk()->assertDontSee('Private Album');
    $this->get('/photos/'.$album->slug)->assertNotFound();
    $this->get('/')->assertOk()->assertDontSee('Private photo');
});
test('published albums show escaped captions credits and the existing lightbox', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('published.jpg')->store('gallery', 'public');
    $album = PhotoAlbum::factory()->published()->create();
    Photo::factory()->for($album, 'album')->published()->create(['image' => $path, 'caption' => '<script>alert(1)</script>', 'photographer_credit' => 'Test credit']);
    Photo::factory()->for($album, 'album')->create(['caption' => 'Private caption']);
    $this->get('/photos/'.$album->slug)->assertOk()->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('Test credit')->assertSee('data-gallery=', false)->assertDontSee('Private caption');
    $this->get('/')->assertOk()->assertSee(asset('storage/'.$path), false);
});
test('album public photos are ordered and paginated', function () {
    $album = PhotoAlbum::factory()->published()->create();
    Photo::factory()->for($album, 'album')->published()->count(25)->create();
    $response = $this->get('/photos/'.$album->slug.'?page=2');
    $response->assertOk();
    expect($response->viewData('photos')->count())->toBe(1);
});
test('gallery writes require csrf tokens',function () {
    $this->app->instance('env','local');
    $this->actingAs(User::factory()->admin()->create())->post('/admin/photo-albums',albumPayload())->assertStatus(419);
});
