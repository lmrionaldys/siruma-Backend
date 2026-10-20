<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'nama_role' => 'User',
        ]);

        Role::create([
            'nama_role' => 'Approval',
        ]);

        Role::create([
            'nama_role' => 'Super Admin',
        ]);
    }
}