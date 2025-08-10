<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class TagihanApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $currentUser = $request->user()->pelanggan;
            if (!$currentUser) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $tagihan = Tagihan::where('tagihanPelangganId', $currentUser->pelangganId)
                ->whereNull('deleted_at')
                ->with('pembayaranInfo')
                ->orderBy('tagihanTahun', 'desc')
                ->orderBy('tagihanBulan', 'desc')
                ->get();

            return response()->json($tagihan);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    
}
