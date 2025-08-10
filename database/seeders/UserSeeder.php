<?php

namespace Database\Seeders;

use App\Models\Roles;
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
        User::insert([
            'name' => 'Admin Aplikasi',
            'username' => 'adminpdam',
            'password' => bcrypt('adminpdam'),
            'userRoleId' => Roles::where('roleName', 'admin')->first()->roleId
        ]);
    }
}
