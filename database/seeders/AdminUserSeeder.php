<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('username','admin')->exists()) {
            User::create([
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }
    }
}
