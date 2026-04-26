<?php

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSee('Mundo Futuro')
        ->assertSee('logo.svg', false)
        ->assertSee('logo-dark.svg', false)
        ->assertSee('data-test="auth-back-button"', false)
        ->assertSee('Volver')
        ->assertSee('Create an account')
        ->assertSee('Log in')
        ->assertSee('prefers-color-scheme: dark', false);
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
