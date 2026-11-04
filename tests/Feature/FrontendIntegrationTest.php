<?php

use App\Models\HomepageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
test('all completed public entry pages render and expose shared navigation', function (string $path) {
    $this->get($path)->assertOk()->assertSee(route('contact'), false)->assertSee(route('in-the-bag'), false)->assertSee(route('coaching-tips.index'), false)->assertSee(route('sponsors.index'), false);
})->with(['/', '/biography', '/tournaments', '/news', '/photos', '/videos', '/sponsors', '/social-works', '/in-the-bag', '/coaching-tips', '/contact']);
test('homepage contact label remains editable while using the functional contact route', function () {
    HomepageSection::factory()->create(array_replace(config('homepage.contact.defaults'), ['button_text' => 'Talk with the team', 'button_url' => '#main']));
    $this->get('/')->assertOk()->assertSee('Talk with the team')->assertSee('href="'.route('contact').'"', false);
});
