<?php

namespace Database\Seeders;

use App\Models\Bulan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BulanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bulan::insert([
            [
                'bulanNama' => 'Januari'
            ],
            [
                'bulanNama' => 'Februari'
            ],
            [
                'bulanNama' => 'Maret'
            ],
            [
                'bulanNama' => 'April'
            ],
            [
                'bulanNama' => 'Mei'
            ],
            [
                'bulanNama' => 'Juni'
            ],
            [
                'bulanNama' => 'Juli'
            ],
            [
                'bulanNama' => 'Agustus'
            ],
            [
                'bulanNama' => 'September'
            ],
            [
                'bulanNama' => 'Oktober'
            ],
            [
                'bulanNama' => 'November'
            ],
            [
                'bulanNama' => 'Desember'
            ]
            ]);
    }
}
