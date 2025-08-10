<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {  
        $currentUser = $request->user()->pelanggan;
        // dd($currentUser);
        $tagihan = Tagihan::where('tagihanPelangganId', $currentUser->pelangganId)
            ->whereNull('deleted_at')
            ->with('pembayaranInfo')
            ->get();
        
        $tagihan->transform(function($item) {
            $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
            $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;
            $item->tagihanTotalPemakaianSetiapBulan = $item->tagihanMAkhir - $item->tagihanMAwal;
            return $item;
        });

        $totalTagihan = $tagihan->whereIn('tagihanStatus', ['Belum Lunas', 'Pending'])->sum('tagihanJumlahTotal');

        $lastTagihan = $tagihan->sortByDesc('tagihanMAkhir')->first()->tagihanMAkhir;

        
        $pemakaianPerBulan = $tagihan
            ->sortBy('tagihanTahun')
            ->sortBy('tagihanBulan')

            ->take(12)
            ->map(function($item) {
                return [
                    'tagihanBulan' => $item->tagihanBulan . ' - ' . $item->tagihanTahun,
                    'tagihanTotalPemakaian' => $item->tagihanTotalPemakaianSetiapBulan
                ];
            });

        return response()->json([
            'pemakaianPerBulan' => $pemakaianPerBulan,
            'totalTagihanBelumLunas' => $totalTagihan ?? 0,
            'meterAkhirTagihanKubik' => $lastTagihan ?? 0,
            'meterAkhirTagihanLiter' => $lastTagihan * 1000 ?? 0
        ]);
    }
}
