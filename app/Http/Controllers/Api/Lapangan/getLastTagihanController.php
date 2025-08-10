<?php

namespace App\Http\Controllers\Api\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class getLastTagihanController extends Controller
{
    public function index($id)
    {
        $pelanggan = Pelanggan::where('pelangganKode', $id)->first();
        $tagihan = Tagihan::select('tagihanTahun', 'tagihanBulan', 'tagihanMAwal', 'tagihanMAkhir')->where('tagihanPelangganId', $pelanggan->pelangganId)
                          ->orderBy('tagihanTahun', 'desc')
                          ->orderBy('tagihanBulan', 'desc')
                          ->first();
        
        if ($tagihan) {
            //$tagihan->tagihanMAkhir += 1;
            return response()->json($tagihan);
        } else {
            return response()->json(['message' => 'Tagihan tidak ditemukan'], 404);
        }
    }
}
