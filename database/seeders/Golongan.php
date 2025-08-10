<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Golongan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Golongan::insert([
            [
                'golonganNama' => 'Tarif 1',
                'golonganTarif' => 2000,
                'golonganAbonemen' => 3000,
                'golonganStatus' => 'Aktif',
            ],
        ]);
    }
}
