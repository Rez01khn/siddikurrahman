<?php

use App\Models\CoachingTip;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
function trainingData(array $data = []): array
{
    return array_replace(['title' => 'Synthetic test content', 'slug' => 'synthetic-test-content', 'description' => 'Synthetic fixture description', 'excerpt' => 'Fixture excerpt', 'is_published' => true, 'is_featured' => false, 'sort_order' => 0], $data);
}
dataset('training modules', ['equipment' => ['equipment', Equipment::class, '/in-the-bag'], 'coaching' => ['coaching', CoachingTip::class, '/coaching-tips']]);
test('training admin access is restricted', function (string $type, string $model, string $public, string $method, string $suffix, bool $ordinary) {
    $r = $model::create(trainingData());
    $url = '/admin/'.$type.str_replace('{id}', (string) $r->id, $suffix);
    if ($ordinary) {
        $this->actingAs(User::factory()->create())->$method($url)->assertForbidden();
    } else {
        $this->$method($url)->assertRedirect(route('admin.login'));
    }
})->with('training modules')->with([['get', ''], ['get', '/create'], ['post', ''], ['get', '/{id}/edit'], ['put', '/{id}'], ['delete', '/{id}']])->with([false, true]);
test('training CRUD renders forms and safely handles images', function (string $type, string $model, string $public) {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create());
    $this->get('/admin/'.$type.'/create')->assertOk();
    $this->post('/admin/'.$type, trainingData(['image' => UploadedFile::fake()->image('first.jpg', 200, 200), 'image_alt' => 'Fixture image']))->assertSessionHasNoErrors()->assertRedirect(route('admin.'.$type.'.index'));
    $r = $model::firstOrFail();
    $old = $r->image;
    $this->get('/admin/'.$type)->assertOk();
    $this->get(route('admin.'.$type.'.edit', $r->id))->assertOk();
    $this->put(route('admin.'.$type.'.update', $r->id), trainingData(['title' => 'Updated fixture', 'sort_order' => 9, 'image' => UploadedFile::fake()->image('next.jpg', 200, 200), 'image_alt' => 'Replacement']))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($old);
    $new = $r->fresh()->image;
    Storage::disk('public')->assertExists($new);
    expect($r->fresh()->sort_order)->toBe(9);
    $this->put(route('admin.'.$type.'.update', $r->id), trainingData(['remove_image' => true]))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($new);
    $this->delete(route('admin.'.$type.'.destroy', $r->id))->assertRedirect();
    $this->assertModelMissing($r);
    $this->get('/admin/modules/'.$type)->assertRedirect(route('admin.'.$type.'.index'));
})->with('training modules');
test('training validation prevents unsafe content and incomplete publication', function (string $type, string $model, string $public, array $data, string $field) {
    $model::create(trainingData(['slug' => 'duplicate']));
    $this->actingAs(User::factory()->admin()->create())->post('/admin/'.$type, trainingData($data))->assertSessionHasErrors($field);
})->with('training modules')->with([[['title' => ''], 'title'], [['slug' => 'duplicate'], 'slug'], [['slug' => '../escape'], 'slug'], [['source_url' => 'javascript:alert(1)'], 'source_url'], [['description' => null], 'description'], [['sort_order' => -1], 'sort_order']]);
test('training publication search ordering and homepage integration', function (string $type, string $model, string $public) {
    $featured = $model::create(trainingData(['title' => 'Visible featured fixture', 'is_featured' => true]));
    $model::create(trainingData(['slug' => 'private', 'title' => 'Private fixture', 'is_published' => false, 'is_featured' => true]));
    $model::create(trainingData(['slug' => 'unfeatured', 'title' => 'Unfeatured fixture', 'sort_order' => 9]));
    $this->get($public)->assertOk()->assertSee('Visible featured fixture')->assertDontSee('Private fixture');
    $this->get($public.'?q=Visible')->assertViewHas('records', fn ($r) => $r->total() === 1);
    $this->get('/')->assertOk()->assertSee('Visible featured fixture')->assertDontSee('Private fixture')->assertDontSee('Unfeatured fixture');
    if ($type === 'coaching') {
        $this->get('/coaching-tips/'.$featured->slug)->assertOk();
        $this->get('/coaching-tips/private')->assertNotFound();
    }
    $this->actingAs(User::factory()->admin()->create())->get('/admin/'.$type.'?publication=draft&featured=yes')->assertViewHas('records', fn ($r) => $r->total() === 1);
})->with('training modules');
test('training shared images are preserved until final reference', function () {
    Storage::fake('public');
    $path = 'training/'.str_repeat('a', 40).'.jpg';
    Storage::disk('public')->put($path, 'fixture');
    $a = Equipment::create(trainingData(['image' => $path]));
    $b = CoachingTip::create(trainingData(['image' => $path]));
    $this->actingAs(User::factory()->admin()->create())->delete('/admin/equipment/'.$a->id)->assertRedirect();
    Storage::disk('public')->assertExists($path);
    $this->delete('/admin/coaching/'.$b->id)->assertRedirect();
    Storage::disk('public')->assertMissing($path);
});
test('training uploads reject executable files and require image descriptions', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create())->post('/admin/equipment', trainingData(['image' => UploadedFile::fake()->create('file.php', 1, 'application/x-php')]))->assertSessionHasErrors(['image', 'image_alt']);
    $this->post('/admin/coaching', trainingData(['image' => UploadedFile::fake()->image('valid.jpg', 200, 200)]))->assertSessionHasErrors('image_alt');
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});
test('training text is escaped and empty states render', function () {
    $this->get('/in-the-bag')->assertOk()->assertSee('No approved equipment');
    $this->get('/coaching-tips')->assertOk()->assertSee('No approved coaching');
    $tip = CoachingTip::create(trainingData(['description' => '<script>alert(1)</script>']));
    $this->get('/coaching-tips/'.$tip->slug)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
});
test('training writes require CSRF', function () {
    $this->app->instance('env','local');
    $this->actingAs(User::factory()->admin()->create())->post('/admin/equipment',trainingData())->assertStatus(419);
});
