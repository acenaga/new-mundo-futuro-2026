<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['super_admin', 'admin', 'tutor', 'editor', 'student'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $super_admin = User::firstOrCreate(
            ['email' => 'mundofuturoca@gmail.com'],
            [
                'name' => 'Carlos Ferrer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $super_admin->assignRole('super_admin');
    }
}
