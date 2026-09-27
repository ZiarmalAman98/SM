<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher@school.com'],
            [
                'name' => 'Teacher',
                'password' => Hash::make('Teacher@12345'),
                'role' => 'teacher',
            ]
        );

        User::updateOrCreate(
            ['email' => 'student@school.com'],
            [
                'name' => 'Student',
                'password' => Hash::make('Student@12345'),
                'role' => 'student',
            ]
        );
    }
}