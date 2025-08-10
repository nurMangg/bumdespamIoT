<?php

namespace Database\Seeders;

use App\Models\SettingWeb;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BulanSeeder::class,
            MenuSeeder::class,
            RolesSeeder::class,
            UserSeeder::class,

            Golongan::class,
            DesaSeeder::class,
            SettingWebSeeder::class
        ]);
    }
}
