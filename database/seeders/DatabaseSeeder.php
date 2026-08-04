<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Panggil RoleSeeder agar peran terdaftar di database
        $this->call([
            RoleSeeder::class,
        ]);

        // 2. Buat satu data Perusahaan (Company) dummy
        $company = Company::create([
            'name'      => 'PT Testing Indonesia',
            'email'     => 'admin@testing.com',
            'is_active' => true,
        ]);

        // 3. Buat User utama yang terikat dengan perusahaan di atas
        $user = User::create([
            'company_id' => $company->id, // <- Ini yang menyelesaikan error Anda
            'name'       => 'Test User',
            'email'      => 'test@example.com',
            'password'   => Hash::make('password'),
        ]);

        // 4. Berikan role 'company' ke user tersebut
        $user->assignRole('company');
    }
}