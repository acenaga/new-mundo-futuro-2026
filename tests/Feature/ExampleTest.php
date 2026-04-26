<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Log in')
        ->assertSee('Sign up')
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false);
});
