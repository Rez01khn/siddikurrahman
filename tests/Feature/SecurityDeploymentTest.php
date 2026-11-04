<?php

use App\Models\Biography;
use App\Models\CareerMilestone;
use App\Models\SocialWork;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Services\CareerContentWriter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
test('public and private responses include security headers', function () {
    $this->get('/')->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('X-Frame-Options', 'SAMEORIGIN')->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')->assertHeaderMissing('Strict-Transport-Security');
    $this->get('/admin/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertHeader('Cache-Control', 'no-store, private');
    $this->get('/contact')->assertHeader('Cache-Control', 'no-store, private');
});
test('HSTS is enabled only for production HTTPS', function () {
    $this->app->instance('env', 'production');
    $this->get('https://localhost/contact')->assertOk()->assertHeader('Strict-Transport-Security', 'max-age=31536000');
});
test('shared career and settings files remain until all references are gone', function () {
    Storage::fake('public');
    $path = 'career/'.str_repeat('c', 40).'.jpg';
    Storage::disk('public')->put($path, 'fixture');
    $b = Biography::factory()->create(['profile_image' => $path, 'banner_image' => $path]);
    $m = CareerMilestone::factory()->create(['image' => $path]);
    $writer = app(CareerContentWriter::class);
    $writer->delete($m, ['image']);
    Storage::disk('public')->assertExists($path);
    $writer->save($b, [], Request::create('/', 'PUT', ['remove_profile_image' => 1]), ['profile_image', 'banner_image']);
    Storage::disk('public')->assertExists($path);
    $writer->save($b->fresh(), [], Request::create('/', 'PUT', ['remove_banner_image' => 1]), ['profile_image', 'banner_image']);
    Storage::disk('public')->assertMissing($path);
    $icon = 'settings/'.str_repeat('d', 40).'.png';
    Storage::disk('public')->put($icon, 'fixture');
    WebsiteSetting::create(['id' => 1, 'logo' => $icon, 'favicon' => $icon]);
    $this->actingAs(User::factory()->admin()->create())->put('/admin/settings', ['remove_logo' => 1])->assertSessionHasNoErrors();
    Storage::disk('public')->assertExists($icon);
    $this->put('/admin/settings', ['remove_favicon' => 1])->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($icon);
});
test('existing sessions are rejected after password hash changes', function () {
    $u = User::factory()->admin()->create();
    $old = $u->password;
    $u->update(['password' => 'Changed-secure-password-2026!']);
    $this->actingAs($u->fresh())->withSession(['password_hash_web' => $old])->get('/admin')->assertRedirect(route('admin.login'));
    $this->assertGuest();
});
test('sitemap permits missing historical modification dates', function () {
    $p = SocialWork::factory()->create();
    DB::table('social_works')->where('id', $p->id)->update(['updated_at' => null]);
    $xml = $this->get('/sitemap.xml')->assertOk()->streamedContent();
    expect($xml)->toContain('/social-works/'.$p->slug);
    expect(simplexml_load_string($xml))->not->toBeFalse();
});
test('production preflight reports local blockers without exposing keys', function () {
    config(['app.debug' => true, 'app.url' => 'http://localhost', 'app.key' => 'base64:'.base64_encode(str_repeat('x', 32))]);
    $this->artisan('site:preflight')->expectsOutputToContain('FAIL Production environment')->expectsOutputToContain('FAIL Debug disabled')->expectsOutputToContain('FAIL Production CSS supplied')->doesntExpectOutputToContain(config('app.key'))->assertFailed();
});
