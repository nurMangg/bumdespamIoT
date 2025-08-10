<?php

namespace App\Http\Controllers\Api\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class getDataPelangganController extends Controller
{
    public function index($id)
    {
        $pelanggan  = Pelanggan::where('pelangganKode', $id)->first();
        if ($pelanggan) {
            return response()->json($pelanggan);
        } else {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }
    }
}
