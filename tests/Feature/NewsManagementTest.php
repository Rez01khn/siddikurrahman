<?php

use App\Models\Article;
use App\Models\NewsCategory;
use App\Models\Tournament;
use App\Models\User;
use Database\Seeders\NewsCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function newsPayload(NewsCategory $category, array $overrides = []): array
{
    return array_replace(['title' => 'Reviewed test article', 'slug' => 'reviewed-test-article', 'news_category_id' => $category->id,
        'excerpt' => 'Test excerpt', 'body' => 'Test body', 'publication_date' => '2026-01-01', 'status' => 'published',
        'is_featured' => true, 'sort_order' => 10], $overrides);
}
test('news and category management redirect guests', function (string $method, string $path) {
    $article = Article::factory()->create();
    $path = str_replace(['{id}', '{category}'], [(string) $article->id, (string) $article->news_category_id], $path);
    $this->$method($path)->assertRedirect(route('admin.login'));
})->with([
    ['get', '/admin/news'], ['get', '/admin/news/create'], ['post', '/admin/news'], ['get', '/admin/news/{id}/edit'],
    ['get', '/admin/news/{id}/preview'], ['put', '/admin/news/{id}'], ['delete', '/admin/news/{id}'],
    ['get', '/admin/news-categories'], ['get', '/admin/news-categories/create'], ['post', '/admin/news-categories'],
    ['get', '/admin/news-categories/{category}/edit'], ['put', '/admin/news-categories/{category}'], ['delete', '/admin/news-categories/{category}'],
]);
test('ordinary users cannot access news administration or previews', function (string $method, string $path) {
    $article = Article::factory()->create();
    $path = str_replace(['{id}', '{category}'], [(string) $article->id, (string) $article->news_category_id], $path);
    $this->actingAs(User::factory()->create())->$method($path)->assertForbidden();
})->with([
    ['get', '/admin/news'], ['get', '/admin/news/create'], ['post', '/admin/news'], ['get', '/admin/news/{id}/edit'],
    ['get', '/admin/news/{id}/preview'], ['put', '/admin/news/{id}'], ['delete', '/admin/news/{id}'],
    ['get', '/admin/news-categories'], ['get', '/admin/news-categories/create'], ['post', '/admin/news-categories'],
    ['get', '/admin/news-categories/{category}/edit'], ['put', '/admin/news-categories/{category}'], ['delete', '/admin/news-categories/{category}'],
]);
test('news admin forms and saved preview render', function (string $path) {
    $article = Article::factory()->create();
    $path = str_replace(['{id}', '{category}'], [(string) $article->id, (string) $article->news_category_id], $path);
    $this->actingAs(User::factory()->admin()->create())->get($path)->assertOk();
})->with(['/admin/news', '/admin/news/create', '/admin/news/{id}/edit', '/admin/news/{id}/preview',
    '/admin/news-categories', '/admin/news-categories/create', '/admin/news-categories/{category}/edit']);
test('categories can be created updated searched and deleted', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/admin/news-categories',
        ['name' => 'Test Category', 'slug' => 'test-category', 'description' => 'Test', 'is_published' => true, 'sort_order' => 5])
        ->assertSessionHasNoErrors()->assertRedirect(route('admin.news-categories.index'));
    $category = NewsCategory::firstOrFail();
    $this->put(route('admin.news-categories.update', $category),
        ['name' => 'Renamed Category', 'slug' => 'test-category', 'description' => 'Edited', 'is_published' => false, 'sort_order' => 3])
        ->assertSessionHasNoErrors();
    expect($category->fresh()->name)->toBe('Renamed Category');
    expect($category->fresh()->is_published)->toBeFalse();
    $this->get('/admin/news-categories?q=Renamed')->assertOk()->assertSee('Renamed Category');
    $this->delete(route('admin.news-categories.destroy', $category))->assertRedirect();
    $this->assertModelMissing($category);
});
test('categories reject invalid or duplicate slugs', function (string $slug) {
    NewsCategory::factory()->create(['slug' => 'existing']);
    $this->actingAs(User::factory()->admin()->create())->post('/admin/news-categories',
        ['name' => 'Category', 'slug' => $slug, 'is_published' => true, 'sort_order' => 0])->assertSessionHasErrors('slug');
})->with(['existing', 'UPPER CASE', '../unsafe', '-start', 'trailing-']);
test('categories containing articles are protected from deletion', function () {
    $article = Article::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.news-categories.destroy', $article->news_category_id))
        ->assertSessionHasErrors('delete');
    $this->assertModelExists($article);
    $this->assertDatabaseHas('news_categories', ['id' => $article->news_category_id]);
});
test('articles can be created with relationships and updated without changing identity', function () {
    $category = NewsCategory::factory()->create();
    $tournament = Tournament::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->post('/admin/news',
        newsPayload($category, ['tournament_id' => $tournament->id]))->assertSessionHasNoErrors();
    $article = Article::firstOrFail();
    expect($article->category->id)->toBe($category->id);
    expect($article->tournament->id)->toBe($tournament->id);
    $this->put(route('admin.news.update', $article), newsPayload($category, ['title' => 'Edited headline', 'status' => 'archived', 'sort_order' => 7]))
        ->assertSessionHasNoErrors();
    expect($article->fresh()->title)->toBe('Edited headline');
    expect($article->fresh()->status)->toBe('archived');
    expect($article->fresh()->sort_order)->toBe(7);
});
test('draft articles allow unknown publication date and body', function () {
    $category = NewsCategory::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->post('/admin/news',
        newsPayload($category, ['status' => 'draft', 'publication_date' => null, 'body' => null]))
        ->assertSessionHasNoErrors();
    expect(Article::first()->publication_date)->toBeNull();
});
test('invalid article content is rejected', function (array $overrides, string $field) {
    $category = NewsCategory::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->post('/admin/news', newsPayload($category, $overrides))
        ->assertSessionHasErrors($field);
    $this->assertDatabaseCount('articles', 0);
})->with([
    [['title' => ''], 'title'], [['slug' => '../invalid'], 'slug'], [['news_category_id' => 99999], 'news_category_id'],
    [['body' => null], 'body'], [['publication_date' => null], 'publication_date'],
    [['publication_date' => '2026-02-30'], 'publication_date'], [['status' => 'invalid'], 'status'],
    [['sort_order' => -1], 'sort_order'], [['is_featured' => 'invalid'], 'is_featured'],
    [['tournament_id' => 99999], 'tournament_id'], [['source_url' => 'javascript:alert(1)'], 'source_url'],
    [['seo_title' => str_repeat('x', 161)], 'seo_title'],
]);
test('article slug uniqueness does not allow overwriting another article', function () {
    $other = Article::factory()->create(['slug' => 'existing-article']);
    $article = Article::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->put(route('admin.news.update', $article),
        newsPayload($article->category, ['slug' => $other->slug]))->assertSessionHasErrors('slug');
    expect($other->fresh()->slug)->toBe('existing-article');
});
test('article list filters search category status and publication dates with pagination', function () {
    $category = NewsCategory::factory()->create();
    Article::factory()->count(17)->create(['news_category_id' => $category->id, 'title' => 'Matching headline',
        'publication_date' => '2025-04-10', 'status' => 'published']);
    Article::factory()->create(['title' => 'Unrelated headline']);
    $response = $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/news?q=Matching&category='.$category->id.'&status=published&from=2025-04-01&to=2025-04-30');
    $response->assertOk()->assertSee('Matching headline')->assertDontSee('Unrelated headline');
    expect($response->viewData('articles')->total())->toBe(17);
    expect($response->viewData('articles')->count())->toBe(15);
});
test('filters reject malformed values', function (string $query, string $field) {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/news?'.$query)->assertSessionHasErrors($field);
})->with([['q[]=bad', 'q'], ['category=999999', 'category'], ['status=invalid', 'status'],
    ['from=2026-05-01&to=2026-04-01', 'to']]);
test('article images replace safely and use a separate namespace', function () {
    Storage::fake('public');
    $old = UploadedFile::fake()->image('old.jpg')->store('articles', 'public');
    $article = Article::factory()->create(['image' => $old]);
    $this->actingAs(User::factory()->admin()->create())->put(route('admin.news.update', $article),
        newsPayload($article->category, ['image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($article->fresh()->image);
    expect($article->fresh()->image)->toStartWith('articles/');
});
test('invalid uploads preserve old article content', function (string $kind) {
    Storage::fake('public');
    $article = Article::factory()->create();
    $file = match ($kind) {
        'svg' => UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
        'php' => UploadedFile::fake()->createWithContent('bad.php', '<?php echo 1;'),
        'large' => UploadedFile::fake()->image('large.jpg')->size(5121),
        'small' => UploadedFile::fake()->image('small.jpg', 10, 10),
    };
    $this->actingAs(User::factory()->admin()->create())->put(route('admin.news.update', $article),
        newsPayload($article->category, ['image' => $file]))->assertSessionHasErrors('image');
    expect($article->fresh()->title)->toBe('Synthetic test article');
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
})->with(['svg', 'php', 'large', 'small']);
test('image removal preserves the article while clearing the image', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('old.jpg')->store('articles', 'public');
    $article = Article::factory()->create(['image' => $path]);
    $this->actingAs(User::factory()->admin()->create())->put(route('admin.news.update', $article),
        newsPayload($article->category, ['remove_image' => true]))->assertSessionHasNoErrors();
    expect($article->fresh()->image)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
test('failed article saves clean the new file but retain the old content and image', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('old.jpg')->store('articles', 'public');
    $article = Article::factory()->create(['image' => $path]);
    Article::updating(function () {
        throw new RuntimeException('Simulated save failure');
    });
    $this->actingAs(User::factory()->admin()->create())->put(route('admin.news.update', $article),
        newsPayload($article->category, ['image' => UploadedFile::fake()->image('new.jpg', 400, 300)]))->assertSessionHasErrors('save');
    expect($article->fresh()->image)->toBe($path);
    expect(Storage::disk('public')->allFiles())->toBe([$path]);
});
test('deleting an article removes its image but retains its category', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('old.jpg')->store('articles', 'public');
    $article = Article::factory()->create(['image' => $path]);
    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.news.destroy', $article))->assertRedirect();
    $this->assertModelMissing($article);
    Storage::disk('public')->assertMissing($path);
    $this->assertDatabaseHas('news_categories', ['id' => $article->news_category_id]);
});
test('deleting a tournament retains related articles and clears only the relationship', function () {
    $tournament = Tournament::factory()->create();
    $article = Article::factory()->create(['tournament_id' => $tournament->id]);
    $tournament->delete();
    $this->assertModelExists($article);
    expect($article->fresh()->tournament_id)->toBeNull();
});
test('legacy news module link redirects to the working module', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/modules/news')->assertRedirect(route('admin.news.index'));
});
test('proposed category seed is idempotent and never creates articles', function () {
    $this->seed(NewsCategorySeeder::class);
    NewsCategory::where('slug', 'latest-news')->update(['name' => 'Edited label']);
    $this->seed(NewsCategorySeeder::class);
    $this->assertDatabaseCount('news_categories', 5);
    $this->assertDatabaseHas('news_categories', ['slug' => 'latest-news', 'name' => 'Edited label']);
    $this->assertDatabaseCount('articles', 0);
});
test('admin preview is private and not cacheable', function () {
    $article = Article::factory()->create();
    $this->actingAs(User::factory()->admin()->create())->get(route('admin.news.preview', $article))
        ->assertOk()->assertSee('Private admin preview')->assertHeader('Cache-Control', 'no-store, private')
        ->assertSee('noindex, nofollow');
});
test('news writes enforce csrf outside the test bypass',function (string $path) {
    $this->app->instance('env','local');
    $this->actingAs(User::factory()->admin()->create())->post($path)->assertStatus(419);
})->with(['/admin/news', '/admin/news-categories']);
