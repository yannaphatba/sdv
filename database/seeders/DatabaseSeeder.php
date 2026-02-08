<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. สร้าง Admin (Username: admin / Pass: 1111)
        User::create([
            'username' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('1111'),
        ]);

        // 2. สร้าง Student (Username: student / Pass: 1111)
        User::create([
            'username' => 'student',
            'role' => 'student',
            'password' => Hash::make('1111'),
        ]);

        User::create([
            'username' => 'security',
            'role' => 'security', // 👈 กำหนด role เป็น security
            'password' => Hash::make('1111'),
        ]);
    }
}