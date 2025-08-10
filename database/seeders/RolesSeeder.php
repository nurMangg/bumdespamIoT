<?php

namespace Database\Seeders;

use App\Models\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Roles::insert(['roleName' => 'admin', 'roleMenuId' => '["3","4","6","7","9","10","11","13","15","16","17","19","20","21","22"]']);
        Roles::insert(['roleName' => 'kasir']);
        Roles::insert(['roleName' => 'lapangan']);
        Roles::insert(['roleName' => 'pelanggan']);


    }
}
