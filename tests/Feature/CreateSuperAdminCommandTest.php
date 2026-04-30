<?php

use App\Models\User;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

test('it creates a verified super admin user', function () {
    $this->artisan('access:create-super-admin', [
        '--name' => 'Carlos Ferrer',
        '--email' => 'mundofuturoca@gmail.com',
        '--password' => 'super-secret-password',
    ])
        ->expectsOutput('Super admin ready for mundofuturoca@gmail.com.')
        ->assertExitCode(0);

    $user = User::query()->where('email', 'mundofuturoca@gmail.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Carlos Ferrer');
    expect($user->hasRole('super_admin'))->toBeTrue();
    expect($user->hasVerifiedEmail())->toBeTrue();
    expect(Hash::check('super-secret-password', $user->password))->toBeTrue();
});

test('it can create a super admin interactively with password confirmation', function () {
    $this->artisan('access:create-super-admin')
        ->expectsQuestion('Super admin name', 'Carlos Ferrer')
        ->expectsQuestion('Super admin email', 'mundofuturoca@gmail.com')
        ->expectsQuestion('Super admin password', 'interactive-super-secret-password')
        ->expectsQuestion('Confirm super admin password', 'interactive-super-secret-password')
        ->expectsOutput('Super admin ready for mundofuturoca@gmail.com.')
        ->assertExitCode(0);

    $user = User::query()->where('email', 'mundofuturoca@gmail.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Carlos Ferrer');
    expect($user->hasRole('super_admin'))->toBeTrue();
    expect(Hash::check('interactive-super-secret-password', $user->password))->toBeTrue();
});

test('it fails when interactive password confirmation does not match', function () {
    $this->artisan('access:create-super-admin')
        ->expectsQuestion('Super admin name', 'Carlos Ferrer')
        ->expectsQuestion('Super admin email', 'mundofuturoca@gmail.com')
        ->expectsQuestion('Super admin password', 'interactive-super-secret-password')
        ->expectsQuestion('Confirm super admin password', 'different-password')
        ->expectsOutput('The super admin passwords do not match.')
        ->expectsOutput('The super admin password is required.')
        ->assertExitCode(1);

    expect(User::query()->where('email', 'mundofuturoca@gmail.com')->exists())->toBeFalse();
});

test('it updates an existing user without creating duplicates', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'mundofuturoca@gmail.com',
        'email_verified_at' => null,
    ]);

    $this->artisan('access:create-super-admin', [
        '--name' => 'Carlos Ferrer',
        '--email' => 'mundofuturoca@gmail.com',
        '--password' => 'updated-super-secret-password',
    ])
        ->expectsOutput('Super admin ready for mundofuturoca@gmail.com.')
        ->assertExitCode(0);

    expect(User::query()->where('email', 'mundofuturoca@gmail.com')->count())->toBe(1);

    $user->refresh();

    expect($user->name)->toBe('Carlos Ferrer');
    expect($user->hasRole('super_admin'))->toBeTrue();
    expect($user->hasVerifiedEmail())->toBeTrue();
    expect(Hash::check('updated-super-secret-password', $user->password))->toBeTrue();
});

test('super admins can access the filament panel', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Role::firstOrCreate([
        'name' => 'super_admin',
        'guard_name' => 'web',
    ]);

    $user->assignRole('super_admin');

    expect($user)->toBeInstanceOf(FilamentUser::class);
    expect($user->canAccessPanel(Mockery::mock(Panel::class)))->toBeTrue();
});
