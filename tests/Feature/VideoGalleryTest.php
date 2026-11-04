<?php

use App\Models\Photo;
use App\Models\User;
use App\Models\Video;
use App\Support\VideoUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
function videoPayload(array $overrides = []): array
{
    return array_replace(['title' => 'Reviewed video', 'slug' => 'reviewed-video', 'video_url' => 'https://www.youtube.com/watch?v=abcdefghijk',
        'description' => 'Reviewed description', 'is_featured' => true, 'is_published' => true, 'sort_order' => 10], $overrides);
}
test('video admin routes redirect guests', function (string $method, string $path) {
    $video = Video::factory()->create();
    $path = str_replace('{id}', (string) $video->id, $path);
    $this->$method($path)->assertRedirect(route('admin.login'));
})->with([['get', '/admin/videos'], ['get', '/admin/videos/create'], ['post', '/admin/videos'], ['get', '/admin/videos/{id}/edit'], ['put', '/admin/videos/{id}'], ['delete', '/admin/videos/{id}']]);
test('ordinary users cannot manage videos', function (string $method, string $path) {
    $video = Video::factory()->create();
    $path = str_replace('{id}', (string) $video->id, $path);
    $this->actingAs(User::factory()->create())->$method($path)->assertForbidden();
})->with([['get', '/admin/videos'], ['get', '/admin/videos/create'], ['post', '/admin/videos'], ['get', '/admin/videos/{id}/edit'], ['put', '/admin/videos/{id}'], ['delete', '/admin/videos/{id}']]);
test('video admin screens render', function (string $path) {
    $video = Video::factory()->create();
    $path = str_replace('{id}', (string) $video->id, $path);
    $this->actingAs(User::factory()->admin()->create())->get($path)->assertOk();
})->with(['/admin/videos', '/admin/videos/create', '/admin/videos/{id}/edit']);
test('video CRUD saves metadata and replaces thumbnails', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create())->post('/admin/videos', videoPayload(['image' => UploadedFile::fake()->image('thumb.jpg', 500, 300)]))->assertSessionHasNoErrors();
    $video = Video::firstOrFail();
    $old = $video->image;
    Storage::disk('public')->assertExists($old);
    $this->put(route('admin.videos.update', $video), videoPayload(['title' => 'Edited video', 'sort_order' => 5, 'image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($old);
    expect($video->fresh()->title)->toBe('Edited video');
    $current = $video->fresh()->image;
    $this->delete(route('admin.videos.destroy', $video))->assertRedirect();
    $this->assertModelMissing($video);
    Storage::disk('public')->assertMissing($current);
});
test('video URL parser generates only canonical trusted embeds', function (string $url, string $expected) {
    expect(VideoUrl::embed($url))->toBe($expected);
})->with([
    ['https://youtu.be/abcdefghijk', 'https://www.youtube-nocookie.com/embed/abcdefghijk'],
    ['https://www.youtube.com/watch?v=abcdefghijk&feature=share', 'https://www.youtube-nocookie.com/embed/abcdefghijk'],
    ['https://youtube.com/shorts/abcdefghijk', 'https://www.youtube-nocookie.com/embed/abcdefghijk'],
    ['https://www.youtube.com/embed/abcdefghijk', 'https://www.youtube-nocookie.com/embed/abcdefghijk'],
    ['https://vimeo.com/12345678', 'https://player.vimeo.com/video/12345678'],
    ['https://player.vimeo.com/video/12345678', 'https://player.vimeo.com/video/12345678'],
]);
test('unsafe or unsupported video URLs are rejected', function (string $url) {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/videos', videoPayload(['video_url' => $url]))->assertSessionHasErrors('video_url');
    $this->assertDatabaseCount('videos', 0);
})->with(['javascript:alert(1)', '<iframe src=x></iframe>', 'https://youtube.com.evil.example/watch?v=abcdefghijk',
    'https://evil.example/abcdefghijk', 'http://youtu.be/abcdefghijk', 'https://user:password@youtube.com/watch?v=abcdefghijk',
    'https://www.youtube.com/watch?v[]=abcdefghijk', 'https://youtu.be/too-short', 'https://www.youtube.com/redirect?v=abcdefghijk',
    'https://vimeo.com/video-not-a-number']);
test('video slugs dates and status fields are validated', function (array $override, string $field) {
    Video::factory()->create(['slug' => 'existing']);
    $this->actingAs(User::factory()->admin()->create())->post('/admin/videos', videoPayload($override))->assertSessionHasErrors($field);
})->with([[['title' => ''], 'title'], [['slug' => 'existing'], 'slug'], [['slug' => '../bad'], 'slug'],
    [['video_date' => '2999-01-01'], 'video_date'], [['is_published' => 'invalid'], 'is_published'], [['sort_order' => -1], 'sort_order']]);
test('video list filters and paginates', function () {
    Video::factory()->published()->count(17)->create(['title' => 'Matching video']);
    Video::factory()->create(['title' => 'Other video']);
    $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/videos?q=Matching&publication=published');
    $response->assertOk()->assertDontSee('Other video');
    expect($response->viewData('videos')->total())->toBe(17);
    expect($response->viewData('videos')->count())->toBe(15);
});
test('video deletion retains a thumbnail shared with a photo', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('shared.jpg')->store('gallery', 'public');
    $video = Video::factory()->create(['image' => $path]);
    Photo::factory()->create(['image' => $path]);
    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.videos.destroy', $video))->assertRedirect();
    Storage::disk('public')->assertExists($path);
});
test('unsupported thumbnail uploads are rejected', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/videos', videoPayload(['image' => UploadedFile::fake()->createWithContent('bad.php', '<?php echo 1;')]))
        ->assertSessionHasErrors('image');
});
test('draft videos are absent from public pages and homepage', function () {
    $video = Video::factory()->create(['title' => 'Private video']);
    $this->get('/videos')->assertOk()->assertDontSee('Private video');
    $this->get('/videos/'.$video->slug)->assertNotFound();
    $this->get('/')->assertOk()->assertDontSee('Private video');
});
test('published video uses safe embed output and escaped title text', function () {
    $video = Video::factory()->published()->create(['title' => '<script>alert(1)</script>', 'description' => '<img src=x onerror=alert(1)>']);
    $this->get('/videos/'.$video->slug)->assertOk()->assertSee('https://www.youtube-nocookie.com/embed/abcdefghijk', false)
        ->assertDontSee('<script>alert(1)</script>', false)->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<img src=x onerror=alert(1)>', false);
});
test('invalid stored video URLs never create arbitrary iframes', function () {
    $video = Video::factory()->published()->create(['video_url' => 'https://evil.example/embed/test']);
    $this->get('/videos/'.$video->slug)->assertOk()->assertSee('This video link is unavailable.')
        ->assertDontSee('<iframe', false);
});
test('homepage shows a published featured video and its detail link', function () {
    $video = Video::factory()->published()->create(['title' => 'Featured film', 'is_featured' => true]);
    $this->get('/')->assertOk()->assertSee('Featured film')->assertSee(route('videos.show', $video->slug), false);
});
test('legacy gallery module paths redirect to completed screens', function (string $module) {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/modules/'.$module)->assertRedirect(route('admin.'.$module.'.index'));
})->with(['photos', 'videos', 'photo-albums']);
test('unknown gallery slugs return not found', function () {
    $this->get('/photos/missing')->assertNotFound();
    $this->get('/videos/missing')->assertNotFound();
});
test('video writes enforce csrf', function () {
    $this->app->instance('env','local');
    $this->actingAs(User::factory()->admin()->create())->post('/admin/videos',videoPayload())->assertStatus(419);
});
