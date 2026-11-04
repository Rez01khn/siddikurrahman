<?php

use App\Models\ContactMessage;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
function contactData(array $data = []): array
{
    return array_replace(['name' => 'Fixture sender', 'email' => 'sender@example.com', 'subject' => 'Fixture subject', 'message' => 'Synthetic contact test message.'], $data);
}
test('contact stores messages without trusting read status', function () {
    $this->get('/contact')->assertOk();
    $this->post('/contact', contactData(['read_at' => now()]))->assertRedirect(route('contact'))->assertSessionHas('status');
    expect(ContactMessage::firstOrFail()->read_at)->toBeNull();
});
test('contact validates fields and rejects honeypot submissions', function (array $override, string $field) {
    $this->post('/contact', contactData($override))->assertSessionHasErrors($field);
    expect(ContactMessage::count())->toBe(0);
})->with([[['name' => ''], 'name'], [['email' => 'not-email'], 'email'], [['message' => 'short'], 'message'], [['website' => 'https://spam.example'], 'website']]);
test('contact rate limits submission bursts', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->post('/contact', contactData())->assertRedirect();
    }$this->post('/contact', contactData())->assertTooManyRequests();
    expect(ContactMessage::count())->toBe(5);
});
test('contact requires csrf', function () {
    $this->app->instance('env', 'local');
    $this->post('/contact', contactData())->assertStatus(419);
});
test('inbox supports reading status search deletion and escaped text', function () {
    $m = ContactMessage::create(contactData(['message' => '<script>alert(1)</script>']));
    $this->actingAs(User::factory()->admin()->create());
    $this->get('/admin/messages?q=Fixture&status=unread')->assertOk()->assertSee('Fixture subject');
    $this->get('/admin/messages/'.$m->id)->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    expect($m->fresh()->read_at)->toBeNull();
    $this->patch('/admin/messages/'.$m->id, ['is_read' => true])->assertSessionHasNoErrors();
    expect($m->fresh()->read_at)->not->toBeNull();
    $this->patch('/admin/messages/'.$m->id, ['is_read' => false])->assertSessionHasNoErrors();
    expect($m->fresh()->read_at)->toBeNull();
    $this->delete('/admin/messages/'.$m->id)->assertRedirect(route('admin.messages.index'));
    $this->assertModelMissing($m);
});
test('inbox and settings require administrator access', function (string $method, string $url, bool $ordinary) {
    $m = ContactMessage::create(contactData());
    $url = str_replace('{id}', (string) $m->id, $url);
    if ($ordinary) {
        $this->actingAs(User::factory()->create())->$method($url)->assertForbidden();
    } else {
        $this->$method($url)->assertRedirect(route('admin.login'));
    }
})->with([['get', '/admin/messages'], ['get', '/admin/messages/{id}'], ['patch', '/admin/messages/{id}'], ['delete', '/admin/messages/{id}'], ['get', '/admin/settings'], ['put', '/admin/settings']])->with([false, true]);
test('settings edit saves data and replaces images in public layout', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create())->get('/admin/settings')->assertOk();
    $this->put('/admin/settings', ['site_name' => 'Fixture website', 'footer_text' => 'Fixture footer', 'seo_title' => 'Fixture default SEO', 'seo_description' => 'Fixture default description', 'contact_email' => 'contact@example.com', 'social_links' => ['facebook' => 'https://example.com/profile'], 'logo' => UploadedFile::fake()->image('logo.png', 200, 200), 'favicon' => UploadedFile::fake()->image('icon.png', 32, 32)])->assertSessionHasNoErrors();
    $s = WebsiteSetting::findOrFail(1);
    $old = $s->logo;
    Storage::disk('public')->assertExists($old);
    $this->get('/')->assertOk()->assertSee('Fixture default SEO')->assertSee('Fixture default description')->assertSee('Fixture footer')->assertSee($s->imageUrl('favicon'), false);
    $this->get('/contact')->assertSee('contact@example.com');
    $this->put('/admin/settings', ['logo' => UploadedFile::fake()->image('new.png', 200, 200)])->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($old);
    $path = $s->fresh()->logo;
    $this->put('/admin/settings', ['remove_logo' => 1, 'remove_favicon' => 1])->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($path);
    expect($s->fresh()->logo)->toBeNull();
});
test('settings validate urls and favicon restrictions', function () {
    $this->actingAs(User::factory()->admin()->create())->put('/admin/settings', ['social_links' => ['facebook' => 'javascript:alert(1)'], 'favicon' => UploadedFile::fake()->image('bad.jpg', 100, 60)])->assertSessionHasErrors(['social_links.facebook', 'favicon']);
});
test('settings and message legacy links redirect', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/modules/settings')->assertRedirect(route('admin.settings.edit'));
    $this->get('/admin/modules/messages')->assertRedirect(route('admin.messages.index'));
});
