<?php

namespace App\Http\Controllers\IoT;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\ReadMeteran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IoTController extends Controller
{
    public function index()
    {
        return view('dashboard-iot');
    }


    public function getWaterUsageChart(Request $request)
    {
        $pelangganKode = $request->query('pelangganKode');

        // Ambil data pemakaian dalam 30 hari terakhir
        $query = ReadMeteran::selectRaw("
            readMeteranReadingDate as datetime, 
            SUM(readMeteranWaterUsage) as total_usage
        ")
        ->where('readMeteranReadingDate', '>=', Carbon::now()->subDays(30))
        ->groupBy('datetime')
        ->orderBy('datetime', 'ASC');

        if ($pelangganKode) {
            $query->where('readMeteranPelangganKode', $pelangganKode);
        }

        $rawData = $query->get();

        // Buat data lengkap, jika tidak ada pemakaian set 0
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        $fullData = [];

        while ($startDate->lessThan($endDate)) {
            $datetime = $startDate->format('Y-m-d H:i:s'); // Format harus sesuai dengan database

            // Cari data yang paling dekat dengan datetime ini
            $closestData = $rawData->first(function ($item) use ($datetime) {
                return Carbon::parse($item->datetime)->format('Y-m-d H') === Carbon::parse($datetime)->format('Y-m-d H');
            });

            $fullData[] = [
                'datetime' => $datetime,
                'total_usage' => $closestData ? $closestData->total_usage : 0
            ];

            $startDate->addMinutes(30); // Setiap 1 jam
        }

        return response()->json($fullData);
    }


    
    public function getWaterUsageChartNotNull(Request $request)
    {
        $pelangganKode = $request->query('pelangganKode');
        
        // Ambil data pemakaian dalam 30 hari terakhir
        $query = ReadMeteran::selectRaw("
            readMeteranReadingDate as datetime, 
            SUM(readMeteranWaterUsage) as total_usage
        ")
        ->where('readMeteranReadingDate', '>=', Carbon::now()->subDays(30))
        ->groupBy('datetime')
        ->orderBy('datetime', 'ASC');
        
        if ($pelangganKode) {
            $query->where('readMeteranPelangganKode', $pelangganKode);
        }
        
        $rawData = $query->get();
        
        // Buat data chart
        $chartData = [];
        
        foreach ($rawData as $item) {
            if ($item->total_usage > 0) {
                $chartData[] = [
                    'datetime' => $item->datetime,
                    'total_usage' => $item->total_usage
                ];
            }
        }
        
        return response()->json($chartData);
    }



    public function getPelanggan()
    {
        return response()->json(Pelanggan::select('pelangganKode', 'pelangganNama')->get());
    }

    public function getWaterUsageSummary(Request $request)
    {
        $pelangganKode = $request->query('pelangganKode');
        $periode = $request->query('periode', 'daily'); // Default harian

        // Sesuaikan format grup berdasarkan periode
        $dateFormat = $periode === 'monthly' ? '%Y-%m' : '%Y-%m-%d';

        $query = ReadMeteran::selectRaw("
            DATE_FORMAT(readMeteranReadingDate, '$dateFormat') as period, 
            SUM(readMeteranWaterUsage) as total_usage
        ")
        ->where('readMeteranReadingDate', '>=', Carbon::now()->subMonths(6)) // Ambil data 6 bulan terakhir
        ->groupBy('period')
        ->orderBy('period', 'ASC');

        if ($pelangganKode) {
            $query->where('readMeteranPelangganKode', $pelangganKode);
        }

        $data = $query->get();

        return response()->json($data);
    }

}
