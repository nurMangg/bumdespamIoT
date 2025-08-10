<?php

namespace Database\Seeders;

use App\Models\SettingWeb;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingWebSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SettingWeb::insert([
            [
                'settingWebNama' => 'PDAM Desa Pagerbarang',
                'settingWebLogo' => 'logo.png',
                'settingWebLogoLandscape' => 'logo-landscape.png',
                'settingWebAlamat' => 'Jl. Raya Pagerbarang, Pagerbarang',
                'settingWebEmail' => 'mangg@example.com',
                'settingWebPhone' => '08123456789',
            ]
            ]);
    }
}
