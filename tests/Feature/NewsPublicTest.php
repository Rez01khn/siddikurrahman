<?php

use App\Models\Article;
use App\Models\HomepageSection;
use App\Models\NewsCategory;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
test('empty news and homepage sections show clean states without invented stories', function () {
    $this->get('/news')->assertOk()->assertSee('No published articles match these filters.');
    $this->get('/')->assertOk()->assertSee('No reviewed articles have been published yet.')
        ->assertDontSee('The rhythm of a tournament week.');
});
test('published article routes use their unique slug', function () {
    $article = Article::factory()->published()->create(['slug' => 'reviewed-story', 'author_name' => 'Reviewed Author']);
    $this->get('/news')->assertOk()->assertSee(route('news.show', 'reviewed-story'), false);
    $this->get('/news/reviewed-story')->assertOk()->assertSee($article->title)->assertSee('Reviewed Author')
        ->assertSee('Another test paragraph.');
});
test('draft archived future undated and hidden-category articles are excluded everywhere', function (string $state) {
    $article = Article::factory()->published()->create(['title' => 'Private headline', 'excerpt' => 'Private excerpt', 'is_featured' => true]);
    match ($state) {
        'draft' => $article->update(['status' => 'draft']),
        'archived' => $article->update(['status' => 'archived']),
        'future' => $article->update(['publication_date' => today()->addDays(5)]),
        'undated' => $article->update(['publication_date' => null]),
        'category' => $article->category->update(['is_published' => false]),
    };
    $this->get('/news')->assertOk()->assertDontSee('Private headline')->assertDontSee('Private excerpt');
    $this->get('/news/'.$article->slug)->assertNotFound();
    $this->get('/')->assertOk()->assertDontSee('Private headline');
})->with(['draft', 'archived', 'future', 'undated', 'category']);
test('a future publication becomes visible when its date arrives without a background job', function () {
    $this->travelTo(now()->setDate(2026, 11, 4)->startOfDay());
    $article = Article::factory()->published()->create(['publication_date' => '2026-11-05']);
    $this->get('/news/'.$article->slug)->assertNotFound();
    $this->travelTo(now()->setDate(2026, 11, 5)->startOfDay());
    $this->get('/news/'.$article->slug)->assertOk();
});
test('public news filters support category search and date range', function () {
    $category = NewsCategory::factory()->create(['name' => 'Selected category']);
    Article::factory()->published()->create(['title' => 'Matching headline', 'news_category_id' => $category->id, 'publication_date' => '2025-04-10']);
    Article::factory()->published()->create(['title' => 'Other headline', 'publication_date' => '2025-01-01']);
    $this->get('/news?q=Matching&category='.$category->id.'&from=2025-04-01&to=2025-04-30')
        ->assertOk()->assertSee('Matching headline')->assertDontSee('Other headline');
});
test('public filtering cannot request draft statuses', function () {
    $this->getJson('/news?status=draft')->assertUnprocessable()->assertJsonValidationErrors('status');
});
test('homepage prioritizes featured visible articles and respects the section visibility', function () {
    Article::factory()->published()->create(['title' => 'Featured homepage story', 'is_featured' => true]);
    Article::factory()->published()->create(['title' => 'Regular homepage story', 'sort_order' => 1]);
    Article::factory()->published()->create(['title' => 'Third excluded story', 'sort_order' => 20]);
    $this->get('/')->assertOk()->assertSeeInOrder(['Featured homepage story', 'Regular homepage story'])
        ->assertDontSee('Third excluded story');
    HomepageSection::factory()->section('journal')->create(['is_active' => false]);
    $this->get('/')->assertOk()->assertDontSee('Featured homepage story');
});
test('article bodies captions authors and seo are escaped', function () {
    $article = Article::factory()->published()->create(['title' => '<script>alert(1)</script>', 'body' => '<img src=x onerror=alert(1)>',
        'author_name' => '<b>Unsafe</b>', 'seo_title' => 'Escaped SEO', 'seo_description' => '" onload="alert(1)']);
    $this->get('/news/'.$article->slug)->assertOk()->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false)->assertSee('<img src=x onerror=alert(1)>')
        ->assertDontSee('<img src=x onerror=alert(1)>', false)->assertDontSee('<b>Unsafe</b>', false)
        ->assertSee('Escaped SEO')->assertDontSee('content="" onload=', false);
});
test('private related tournament information is not exposed', function () {
    $tournament = Tournament::factory()->create(['name' => 'Private linked tournament']);
    $article = Article::factory()->published()->create(['tournament_id' => $tournament->id]);
    $this->get('/news/'.$article->slug)->assertOk()->assertDontSee('Private linked tournament');
    $tournament->update(['is_published' => true]);
    $this->get('/news/'.$article->slug)->assertOk()->assertSee('Private linked tournament')
        ->assertSee(route('tournaments.show', $tournament), false);
});
test('missing article files never produce broken image urls', function () {
    Storage::fake('public');
    $article = Article::factory()->published()->create(['image' => 'articles/'.str_repeat('a', 40).'.jpg']);
    $this->get('/news/'.$article->slug)->assertOk()->assertDontSee('storage/articles/', false);
});
test('safe external source links are attributed without exposing unsafe protocols', function () {
    $article = Article::factory()->published()->create(['source_url' => 'https://example.com/original']);
    $this->get('/news/'.$article->slug)->assertOk()->assertSee('href="https://example.com/original"', false)
        ->assertSee('rel="noopener noreferrer"', false);
    $article->update(['source_url' => 'javascript:alert(1)']);
    $this->get('/news/'.$article->slug)->assertOk()->assertDontSee('href="javascript:', false);
});
test('public articles paginate and preserve filters and display order', function () {
    Article::factory()->published()->count(12)->create(['title' => 'Matching headline']);
    $response = $this->get('/news?q=Matching&page=2');
    $response->assertOk();
    expect($response->viewData('articles')->total())->toBe(12);
    expect($response->viewData('articles')->count())->toBe(3);
    expect($response->viewData('articles')->url(1))->toContain('q=Matching');
});
test('unknown news slugs return not found',function () {
    $this->get('/news/does-not-exist')->assertNotFound();
});
