<?php

use App\Models\Article;
use App\Models\CoachingTip;
use App\Models\PhotoAlbum;
use App\Models\SocialWork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
test('public metadata has canonical and open graph with safe defaults', function () {
    config(['app.url' => 'https://official.example', 'seo.indexable' => true]);
    $this->get('/news?q=fixture')->assertOk()->assertSee('https://official.example/news', false)->assertSee('property="og:title"', false)->assertSee('noindex, nofollow');
    $this->get('/news')->assertSee('index, follow');
});
test('database metadata cannot inject markup or attributes', function () {
    $tip = CoachingTip::create(['title' => 'Fixture tip', 'slug' => 'fixture-tip', 'description' => 'Fixture description', 'is_published' => true, 'seo_title' => '<script>bad()</script> " onload="bad', 'seo_description' => '" /><script>bad()</script>']);
    $this->get('/coaching-tips/'.$tip->slug)->assertOk()->assertDontSee('<script>bad()</script>', false)->assertDontSee('content="" /><script>', false)->assertSee('&quot;', false);
});
test('robots honors explicit indexing configuration', function () {
    config(['seo.indexable' => false]);
    $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
    config(['seo.indexable' => true, 'app.url' => 'https://official.example']);
    $this->get('/robots.txt')->assertSee('Disallow: /admin')->assertSee('https://official.example/sitemap.xml');
});
test('sitemap includes only publicly visible records and trusted origin', function () {
    config(['app.url' => 'https://official.example']);
    $visible = Article::factory()->create(['status' => 'published', 'publication_date' => today()]);
    $hidden = Article::factory()->create(['status' => 'draft']);
    $future = Article::factory()->create(['status' => 'published', 'publication_date' => today()->addDay()]);
    $project = SocialWork::factory()->create();
    $private = SocialWork::factory()->create(['is_published' => false]);
    $album = PhotoAlbum::factory()->create(['is_published' => true]);
    $xml = $this->get('/sitemap.xml')->assertOk()->streamedContent();
    expect($xml)->toContain('https://official.example/news/'.$visible->slug)->toContain('/social-works/'.$project->slug)->toContain('/photos/'.$album->slug)->not->toContain('/news/'.$hidden->slug)->not->toContain('/news/'.$future->slug)->not->toContain('/social-works/'.$private->slug)->not->toContain('/admin');
    expect(simplexml_load_string($xml))->not->toBeFalse();
});
test('admin article previews remain nonindexable', function () {
    config(['seo.indexable' => true]);
    $article = Article::factory()->create(['status' => 'draft']);
    $this->actingAs(User::factory()->admin()->create())->get(route('admin.news.preview', $article))->assertOk()->assertSee('noindex, nofollow');
});
