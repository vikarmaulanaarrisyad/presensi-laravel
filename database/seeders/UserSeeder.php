<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun user
        User::query()->updateOrCreate([
            'email' => 'user@gmail.com',
        ],
        [
            'name' => 'User',
            'username' => 'user',
            'password' => bcrypt('password'), // Ganti dengan password yang diinginkan
        ]  
        );
    }
}
