<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 admin users
        User::factory()->create([
            'name' => 'Admin One',
            'email' => 'admin1@reborrn.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Admin Two',
            'email' => 'admin2@reborrn.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Create 10 regular users
        User::factory()->count(10)->create([
            'role' => 'user',
        ]);
    }
}
