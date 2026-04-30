<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSee('Mundo Futuro')
        ->assertSee('logo.svg', false)
        ->assertSee('logo-dark.svg', false)
        ->assertSee('data-test="auth-back-button"', false)
        ->assertSee('Volver')
        ->assertSee('Crea una cuenta')
        ->assertSee('Iniciar sesión')
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

    $user = User::query()->where('email', 'test@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('student'))->toBeTrue()
        ->and($user->getRoleNames()->all())->toBe(['student']);
});

test('registration validation errors are shown in spanish', function () {
    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertRedirect(route('register'))
        ->assertSessionHasErrors([
            'name' => 'El campo nombre es obligatorio.',
            'email' => 'El campo correo electrónico es obligatorio.',
            'password' => 'El campo contraseña es obligatorio.',
        ]);
});
