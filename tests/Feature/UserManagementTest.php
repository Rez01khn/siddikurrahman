<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);
function accountData(array $data = []): array
{
    return array_replace(['name' => 'Fixture account', 'email' => 'fixture@example.com', 'password' => 'Fixture-strong-pass-2026!', 'password_confirmation' => 'Fixture-strong-pass-2026!', 'is_admin' => false, 'is_active' => true], $data);
}
test('user management is admin only', function (string $method, string $suffix, bool $ordinary) {
    $u = User::factory()->create();
    $url = '/admin/users'.str_replace('{id}', (string) $u->id, $suffix);
    if ($ordinary) {
        $this->actingAs(User::factory()->create())->$method($url)->assertForbidden();
    } else {
        $this->$method($url)->assertRedirect(route('admin.login'));
    }
})->with([['get', ''], ['get', '/create'], ['post', ''], ['get', '/{id}/edit'], ['put', '/{id}'], ['delete', '/{id}']])->with([false, true]);
test('account CRUD hashes passwords and preserves password on blank edits', function () {
    $this->actingAs(User::factory()->admin()->create());
    $this->get('/admin/users')->assertOk();
    $this->get('/admin/users/create')->assertOk();
    $this->post('/admin/users', accountData())->assertSessionHasNoErrors();
    $u = User::where('email', 'fixture@example.com')->firstOrFail();
    expect(Hash::check('Fixture-strong-pass-2026!', $u->password))->toBeTrue();
    $hash = $u->password;
    $this->get(route('admin.users.edit', $u))->assertOk()->assertDontSee($hash);
    $this->put(route('admin.users.update', $u), accountData(['name' => 'Updated account', 'password' => null, 'password_confirmation' => null, 'is_admin' => true]))->assertSessionHasNoErrors();
    expect($u->fresh()->password)->toBe($hash);
    expect($u->fresh()->is_admin)->toBeTrue();
    $this->delete(route('admin.users.destroy', $u))->assertRedirect();
    $this->assertModelMissing($u);
});
test('last active administrator cannot be removed', function (string $action) {
    $u = User::factory()->admin()->create();
    User::factory()->admin()->create(['is_active' => false]);
    $this->actingAs($u);
    if ($action === 'delete') {
        $this->delete(route('admin.users.destroy', $u))->assertSessionHasErrors('account');
    } else {
        $data = accountData(['email' => $u->email, 'password' => null, 'password_confirmation' => null, 'is_admin' => $action !== 'demote', 'is_active' => $action !== 'deactivate']);
        $this->put(route('admin.users.update', $u), $data)->assertSessionHasErrors('account');
    }expect($u->fresh()->is_admin)->toBeTrue();
    expect($u->fresh()->is_active)->toBeTrue();
})->with(['delete', 'demote', 'deactivate']);
test('inactive administrators cannot login or continue authenticated requests', function () {
    $u = User::factory()->admin()->create(['is_active' => false]);
    $this->post('/admin/login', ['email' => $u->email, 'password' => 'password'])->assertSessionHasErrors('email');
    $this->actingAs($u)->get('/admin')->assertRedirect(route('admin.login'));
    $this->assertGuest();
});
test('self password change requires sign in again', function () {
    $u = User::factory()->admin()->create();
    $this->actingAs($u)->put(route('admin.users.update', $u), accountData(['email' => $u->email, 'is_admin' => true]))->assertRedirect(route('admin.login'));
    $this->assertGuest();
    expect(Hash::check('Fixture-strong-pass-2026!', $u->fresh()->password))->toBeTrue();
});
test('account validation rejects weak unconfirmed and duplicate credentials', function (array $data, string $field) {
    User::factory()->create(['email' => 'duplicate@example.com']);
    $this->actingAs(User::factory()->admin()->create())->post('/admin/users', accountData($data))->assertSessionHasErrors($field);
})->with([[['password' => 'weak', 'password_confirmation' => 'weak'], 'password'], [['password_confirmation' => 'wrong'], 'password'], [['email' => 'duplicate@example.com'], 'email'], [['email' => 'not-email'], 'email'], [['is_admin' => 'yes'], 'is_admin']]);
test('account filters and legacy redirect work', function () {
    User::factory()->create(['name' => 'Find inactive fixture', 'is_active' => false]);
    $this->actingAs(User::factory()->admin()->create())->get('/admin/users?q=Find&status=inactive&role=user')->assertOk()->assertViewHas('users', fn ($u) => $u->total() === 1);
    $this->get('/admin/modules/users')->assertRedirect(route('admin.users.index'));
});
test('account creation requires csrf', function () {
    $this->app->instance('env', 'local');
    $this->actingAs(User::factory()->admin()->create())->post('/admin/users',accountData())->assertStatus(419);
});
