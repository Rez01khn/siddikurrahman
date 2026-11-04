<?php

use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

test('guests can see the admin login with csrf protection', function () {
    $this->get('/admin/login')->assertOk()->assertSee('name="_token"', false);
});

test('guests are redirected from protected admin pages', function (string $url) {
    $this->get($url)->assertRedirect(route('admin.login'));
})->with(['/admin', '/admin/modules/settings']);

test('ordinary users cannot access admin endpoints', function (string $method, string $url) {
    $user = User::factory()->create();

    $this->actingAs($user)->$method($url)->assertForbidden();
})->with([
    ['get', '/admin'],
    ['get', '/admin/modules/users'],
    ['post', '/admin/logout'],
]);

test('ordinary users cannot log in as administrators', function () {
    $user = User::factory()->create();

    $this->post('/admin/login', ['email' => $user->email, 'password' => 'password', 'is_admin' => true])
        ->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('incorrect passwords are rejected without exposing the password', function () {
    $user = User::factory()->admin()->create();

    $this->post('/admin/login', ['email' => $user->email, 'password' => 'incorrect'])
        ->assertSessionHasErrors('email')
        ->assertSessionMissing('_old_input.password');
    $this->assertGuest();
});

test('login validates required fields', function () {
    $this->post('/admin/login')->assertSessionHasErrors(['email', 'password']);
    $this->assertGuest();
});

test('login rejects malformed input', function (array $input, string $field) {
    $this->post('/admin/login', $input)->assertSessionHasErrors($field);
    $this->assertGuest();
})->with([
    [['email' => 'not-an-email', 'password' => 'test'], 'email'],
    [['email' => ['injected'], 'password' => 'test'], 'email'],
    [['email' => 'admin@example.com', 'password' => ['injected']], 'password'],
]);

test('administrators sign in and receive a regenerated session', function () {
    $user = User::factory()->admin()->create();
    $this->withSession(['_token' => 'before-login']);
    $oldId = session()->getId();

    $this->post('/admin/login', ['email' => '  '.strtoupper($user->email).'  ', 'password' => 'password'])
        ->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($user, 'web');
    expect(session()->getId())->not->toBe($oldId);
});

test('authenticated administrators are redirected away from login', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/login')->assertRedirect(route('admin.dashboard'));
});

test('authenticated ordinary users are redirected to the public homepage from login', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/login')->assertRedirect(route('home'));
});

test('the sixth account login attempt is rate limited', function () {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post('/admin/login', ['email' => 'unknown@example.com', 'password' => 'incorrect'])
            ->assertSessionHasErrors('email');
    }

    $this->post('/admin/login', ['email' => ' UNKNOWN@example.com ', 'password' => 'incorrect'])
        ->assertTooManyRequests()->assertSee('Take a short pause.');
});

test('rotating email addresses cannot bypass the ip login limit', function () {
    for ($attempt = 0; $attempt < 20; $attempt++) {
        $this->post('/admin/login', ['email' => "unknown{$attempt}@example.com", 'password' => 'incorrect']);
    }

    $this->post('/admin/login', ['email' => 'another@example.com', 'password' => 'incorrect'])
        ->assertTooManyRequests();
});

test('the dashboard reports unconfigured content and escapes the user name', function () {
    Schema::drop('contact_messages');
    $user = User::factory()->admin()->create(['name' => '<script>alert(1)</script>']);

    $this->actingAs($user)->get('/admin')
        ->assertOk()->assertSee('Not configured')
        ->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertHeader('Cache-Control', 'no-store, private');
});

test('configured content totals reflect the actual database', function () {
    Article::factory()->count(2)->create();
    foreach ([null, now()] as $read) {
        ContactMessage::create(['name' => 'Fixture', 'email' => 'fixture@example.com', 'subject' => 'Fixture subject', 'message' => 'Fixture message', 'read_at' => $read]);
    }

    $response = $this->actingAs(User::factory()->admin()->create())->get('/admin');

    $response->assertOk();
    expect(collect($response->viewData('metrics'))->keyBy('module')->get('news')['value'])->toBe(2);
    expect(collect($response->viewData('metrics'))->keyBy('module')->get('messages')['value'])->toBe(1);
});

test('homepage legacy module link redirects to its functional list', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin/modules/homepage')->assertRedirect(route('admin.homepage.index'));
});
test('unknown modules return not found', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/modules/unknown')->assertNotFound();
});

test('revoked admin permission prevents further access', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);
    $user->is_admin = false;
    $user->save();

    $this->get('/admin')->assertForbidden();
});

test('logout destroys the authenticated session and rotates the csrf token', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user)->withSession(['sensitive' => 'private', '_token' => 'old-token']);
    $oldId = session()->getId();

    $this->post('/admin/logout')->assertRedirect(route('admin.login'))->assertSessionMissing('sensitive');
    $this->assertGuest('web');
    expect(session()->getId())->not->toBe($oldId);
    expect(session()->token())->not->toBe('old-token');
});

test('logout is not available by get', function () {
    $this->get('/admin/logout')->assertMethodNotAllowed();
});

test('state changing admin requests reject missing csrf tokens', function (string $url) {
    $this->app->instance('env', 'local');
    $this->actingAs(User::factory()->admin()->create());

    $this->post($url)->assertStatus(419);
})->with(['/admin/login', '/admin/logout']);

test('there is no public admin registration', function () {
    $this->get('/admin/register')->assertNotFound();
});

test('users cannot mass assign admin access', function () {
    $user = new User(['name' => 'Ordinary', 'email' => 'ordinary@example.com', 'password' => 'password', 'is_admin' => true]);
    $user->save();

    expect($user->fresh()->is_admin)->toBeFalse();
});

test('the admin command creates a hashed administrator account', function () {
    $this->artisan('admin:create')
        ->expectsQuestion('Name', 'Website Owner')
        ->expectsQuestion('Email', 'owner@example.com')
        ->expectsQuestion('Password (12+ characters, mixed case, number and symbol)', 'A-strong-secret-2026!')
        ->expectsQuestion('Confirm password', 'A-strong-secret-2026!')
        ->assertSuccessful();

    $user = User::where('email', 'owner@example.com')->firstOrFail();
    expect($user->is_admin)->toBeTrue();
    expect(Hash::check('A-strong-secret-2026!', $user->password))->toBeTrue();
    expect($user->password)->not->toBe('A-strong-secret-2026!');
});

test('the admin command refuses to replace an existing user', function () {
    $user = User::factory()->create(['email' => 'existing@example.com']);
    $oldPassword = $user->password;

    $this->artisan('admin:create')
        ->expectsQuestion('Name', 'Replacement')
        ->expectsQuestion('Email', $user->email)
        ->expectsQuestion('Password (12+ characters, mixed case, number and symbol)', 'A-strong-secret-2026!')
        ->expectsQuestion('Confirm password', 'A-strong-secret-2026!')
        ->assertFailed();

    expect($user->fresh()->is_admin)->toBeFalse();
    expect($user->fresh()->password)->toBe($oldPassword);
});

test('the admin command rejects weak or mismatched passwords', function () {
    $this->artisan('admin:create')
        ->expectsQuestion('Name', 'Owner')
        ->expectsQuestion('Email', 'owner@example.com')
        ->expectsQuestion('Password (12+ characters, mixed case, number and symbol)', 'short')
        ->expectsQuestion('Confirm password', 'different')
        ->assertFailed();

    $this->assertDatabaseCount('users', 0);
});
