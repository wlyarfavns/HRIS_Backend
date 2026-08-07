<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        
        $this->call([
            RoleSeeder::class,
        ]);

$company = Company::create([
            'name'      => 'PT Testing Indonesia',
            'email'     => 'admin@testing.com',
            'is_active' => true,
        ]);

$user = User::create([
            'company_id' => $company->id, 
            'name'       => 'Test User',
            'email'      => 'test@example.com',
            'password'   => Hash::make('password'),
        ]);

$user->assignRole('company');
    }
}