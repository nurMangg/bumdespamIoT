<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApexChartController extends Controller
{
    public function tagihanApexChart(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $tagihan = DB::table('tagihans')
            ->join('mspelanggan', 'tagihans.tagihanPelangganId', '=', 'mspelanggan.pelangganId')
            ->selectRaw("
                CONCAT(mspelanggan.pelangganDesa, ' RW ', mspelanggan.pelangganRw) as rw, 
                SUM(CASE WHEN tagihans.tagihanStatus = 'Lunas' THEN 1 ELSE 0 END) as lunas,
                SUM(CASE WHEN tagihans.tagihanStatus = 'Belum Lunas' THEN 1 ELSE 0 END) as belum_lunas
            ")
            ->where('tagihans.tagihanBulan', $bulan)
            ->where('tagihans.tagihanTahun', $tahun)
            ->groupBy('rw')
            ->get();

        return response()->json($tagihan);
    }
    
}
