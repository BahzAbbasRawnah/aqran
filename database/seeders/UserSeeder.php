<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'username' => 'admin',
            'first_name' => 'مدير',
            'last_name' => 'النظام',
            'email' => 'admin@aqran.sa',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
