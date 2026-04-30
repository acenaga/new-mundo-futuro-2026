<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Signature('access:create-super-admin
    {--name= : Name of the super admin}
    {--email= : Email address of the super admin}
    {--password= : Password for the super admin account}')]
#[Description('Create or update the super admin user and assign the super_admin role')]
class CreateSuperAdminCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $name = $this->option('name') ?: $this->ask('Super admin name');
        $email = $this->option('email') ?: $this->ask('Super admin email');
        $password = $this->resolvePassword();

        if (! is_string($name) || blank($name)) {
            $this->error('The super admin name is required.');

            return self::FAILURE;
        }

        if (! is_string($email) || blank($email)) {
            $this->error('The super admin email is required.');

            return self::FAILURE;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('The super admin email must be a valid email address.');

            return self::FAILURE;
        }

        if (! is_string($password) || blank($password)) {
            $this->error('The super admin password is required.');

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew([
            'email' => $email,
        ]);

        $user->name = $name;
        $user->password = $password;
        $user->email_verified_at ??= now();
        $user->save();

        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info("Super admin ready for {$user->email}.");

        return self::SUCCESS;
    }

    protected function resolvePassword(): ?string
    {
        $password = $this->option('password');

        if (filled($password)) {
            return $password;
        }

        $password = $this->secret('Super admin password');
        $passwordConfirmation = $this->secret('Confirm super admin password');

        if ($password !== $passwordConfirmation) {
            $this->error('The super admin passwords do not match.');

            return null;
        }

        return $password;
    }
}
