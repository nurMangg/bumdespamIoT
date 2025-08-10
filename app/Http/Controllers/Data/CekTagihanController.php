<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;


class CekTagihanController extends Controller
{

public function getTagihanByKodePelanggan(Request $request)
{
    $kodePelanggan = $request->input('kodePelanggan');

    if (!$kodePelanggan) {
        return response()->json(['error' => 'Kode pelanggan tidak ditemukan'], 422);
    }

    $pelanggan = Pelanggan::where('pelangganKode', $kodePelanggan)->first();

    $tagihan = DB::table('tagihans')
        ->join('msbulan', 'tagihans.tagihanBulan', '=', 'msbulan.bulanId')
        ->join('mspelanggan', 'tagihans.tagihanPelangganId', '=', 'mspelanggan.pelangganId')
        ->select(
            'tagihans.tagihanId',
            'tagihans.tagihanKode',
            'mspelanggan.pelangganKode',
            'mspelanggan.pelangganNama',
            'msbulan.bulanNama',
            'tagihans.tagihanTahun',
            'tagihans.tagihanMAwal',
            'tagihans.tagihanMAkhir',
            'tagihans.tagihanInfoTarif',
            'tagihans.tagihanInfoAbonemen',
            DB::raw('((tagihans.tagihanMAkhir - tagihans.tagihanMAwal) * tagihans.tagihanInfoTarif + tagihans.tagihanInfoAbonemen) as tagihanTotal'),
            'tagihans.tagihanStatus'
        )
        ->where('mspelanggan.pelangganKode', $kodePelanggan)
        ->whereNull('tagihans.deleted_at')
        ->orderBy('tagihans.tagihanTahun', 'desc')
        ->orderBy('tagihans.tagihanBulan', 'desc')
        ->get()
        ->map(function ($item) {
            $item->tagihanEncrypted = Crypt::encryptString($item->tagihanId); // Laravel built-in encrypt
            return $item;
        });

    return response()->json(['data' => $tagihan]);
}

    

    public function index()
    {
        return view('cek-tagihan.index');
    }
}
