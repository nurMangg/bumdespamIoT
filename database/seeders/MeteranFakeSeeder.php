<?php

namespace Database\Seeders;

use App\Models\ReadMeteran;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeteranFakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('readMeteran')->truncate();

        // $pelanggan = DB::table('mspelanggan')->pluck('pelangganKode'); 
        $pelanggan = collect(['PAM0001', 'PAM0002', 'PAM0003']);


        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        while ($startDate->lessThan($endDate)) {
            foreach ($pelanggan as $kode) {
                ReadMeteran::create([
                    'readMeteranPelangganKode' => $kode,
                    'readMeteranReadingDate' => $startDate->format('Y-m-d H:i:s'),
                    'readMeteranWaterUsage' => rand(0, 10) // Setiap periode bisa 0 jika tidak digunakan
                ]);
            }
            $startDate->addMinutes(rand(1, 1440)); // Random interval (1 menit - 1 hari)
        }
    }
}
