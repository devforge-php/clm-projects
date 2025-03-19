<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'username' => 'admin',
            'city' => 'Tashkent',
            'phone' => '998901234567',
            'email' => 'Javohir@gmail.com',
            'password' => Hash::make('Azizbek98701'), // Parolni hash qilish
            'role' => 'admin', // Role: admin
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
