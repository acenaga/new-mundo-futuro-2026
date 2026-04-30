<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public const array DEFAULT_ROLES = [
        'super_admin',
        'admin',
        'tutor',
        'editor',
        'student',
    ];

    public function run(): void
    {
        foreach (self::DEFAULT_ROLES as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
