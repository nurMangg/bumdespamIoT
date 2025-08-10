<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class TFManualController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'uploadFile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        $decodeCryptId = Crypt::decryptString($request->tagihanId);
        $tagihan = Tagihan::where('tagihanId', $decodeCryptId)->first();

        if (!$tagihan) {
            return response()->json(['error' => 'Tagihan tidak ditemukan'], 404);
        }

        $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihan->tagihanId)->first();
        if (!$pembayaran) {
            return response()->json(['error' => 'Pembayaran tidak ditemukan'], 404);
        }

        if ($request->hasFile('uploadFile')) {
            try {        
                // Ambil file dari request
                $file = $request->file('uploadFile');
        
                // Buka file sebagai stream dan konversi ke base64
                $base64Image = BuktiPembayaran::compressImageToBase64($file, 75);
        
                if (!$base64Image) {
                    return response()->json(['error' => 'Gagal mengompres gambar.'], 500);
                }

                // Simpan base64 image ke database
                $buktiPembayaran = new BuktiPembayaran();
                $buktiPembayaran->buktiPembayaranPembayaranId = $pembayaran->pembayaranId;
                $buktiPembayaran->buktiPembayaranFoto = $base64Image;
                $buktiPembayaran->save();

                $pembayaran->pembayaranMetode = $request->metodePembayaran;
                $pembayaran->pembayaranJumlah = (($tagihan->tagihanMAkhir - $tagihan->tagihanMAwal) * $tagihan->tagihanInfoTarif);
                $pembayaran->pembayaranAbonemen = $tagihan->tagihanInfoAbonemen;
                $pembayaran->save();

                $tagihan->tagihanStatus = 'Pending';
                $tagihan->save();
        
                
        
                return response()->json(['success' => 'Bukti pembayaran berhasil disimpan.']);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
        }
    }

    public function cekPayManual(Request $request) {
        $validator = Validator::make($request->all(), [
            'tagihanId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        $decodeCryptId = Crypt::decryptString($request->tagihanId);
        $tagihan = Tagihan::where('tagihanId', $decodeCryptId)->first();

        if (!$tagihan) {
            return response()->json(['error' => 'Tagihan tidak ditemukan'], 404);
        }

        if ($tagihan->tagihanStatus == 'Pending') {
            return response()->json(['message' => 'Tagihan sedang dalam proses konfirmasi admin, silahkan tunggu.', 'status' => 'info']);
        } else {
            return response()->json(['message' => 'Tagihan sudah dikonfirmasi', 'status' => 'error']);
        }
    }
}
