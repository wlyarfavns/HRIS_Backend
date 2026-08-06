<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['company', 'hr', 'finance', 'supervisor', 'employee'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
