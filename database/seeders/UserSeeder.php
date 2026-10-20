<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'role_id' => 1,
            'nama' => 'User SIRUMA',
            'name' => 'User SIRUMA',
            'username' => 'user',
            'email' => 'user@siruma.test',
            'password' => 'password123',
            'status' => 'aktif',
        ]);

        User::create([
            'role_id' => 2,
            'nama' => 'Approval SIRUMA',
            'name' => 'Approval SIRUMA',
            'username' => 'approval',
            'email' => 'approval@siruma.test',
            'password' => 'password123',
            'status' => 'aktif',
        ]);

        User::create([
            'role_id' => 3,
            'nama' => 'Super Admin SIRUMA',
            'name' => 'Super Admin SIRUMA',
            'username' => 'admin',
            'email' => 'admin@siruma.test',
            'password' => 'password123',
            'status' => 'aktif',
        ]);
    }
}