<?php

use App\Models\User;

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Log in')
        ->assertSee('Sign up')
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false);
});

test('authenticated users see dashboard access instead of guest auth links on home', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk()
        ->assertSee('Hola, '.$user->name)
        ->assertSee('Ir al panel')
        ->assertSee(route('dashboard'), false)
        ->assertDontSee('Log in')
        ->assertDontSee('Sign up')
        ->assertDontSee(route('login'), false)
        ->assertDontSee(route('register'), false);
});
