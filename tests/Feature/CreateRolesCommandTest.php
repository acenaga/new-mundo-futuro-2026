<?php

use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

test('it creates the base application roles', function () {
    $this->artisan('access:create-roles')
        ->expectsOutput('Roles ready: ' . implode(', ', RoleSeeder::DEFAULT_ROLES) . '.')
        ->assertExitCode(0);

    foreach (RoleSeeder::DEFAULT_ROLES as $roleName) {
        expect(Role::query()->where('name', $roleName)->where('guard_name', 'web')->exists())->toBeTrue();
    }
});

test('it is idempotent when roles already exist', function () {
    foreach (RoleSeeder::DEFAULT_ROLES as $roleName) {
        Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);
    }

    $this->artisan('access:create-roles')
        ->expectsOutput('Roles ready: ' . implode(', ', RoleSeeder::DEFAULT_ROLES) . '.')
        ->assertExitCode(0);

    expect(Role::query()->count())->toBe(count(RoleSeeder::DEFAULT_ROLES));
});
