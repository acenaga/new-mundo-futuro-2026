<?php

namespace App\Console\Commands;

use Database\Seeders\RoleSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Signature('access:create-roles')]
#[Description('Create the base application roles if they do not already exist')]
class CreateRolesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (RoleSeeder::DEFAULT_ROLES as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Roles ready: ' . implode(', ', RoleSeeder::DEFAULT_ROLES) . '.');

        return self::SUCCESS;
    }
}
