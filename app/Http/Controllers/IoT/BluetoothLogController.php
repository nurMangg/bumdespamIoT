<?php

namespace App\Http\Controllers\IoT;

use App\Http\Controllers\Controller;
use App\Models\BluetoothLog;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BluetoothLogController extends Controller
{
    /**
     * Simpan log data bluetooth
     */
    public function store(Request $request)
    {
        try {
            Log::info($request->all());
            $validator = Validator::make($request->all(), [
                'pelanggan_id' => 'required',
                'log_data' => 'required|array|min:1',
                'log_data.*.datetime' => 'required|string',
                'log_data.*.total' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pelangganId = $request->pelanggan_id;
            $logData = $request->log_data;

            // Cek apakah pelanggan ada
            $pelanggan = Pelanggan::where('pelangganKode', $pelangganId)->first();
            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan'
                ], 404);
            }



            // Proses dan simpan log data
            $logsToInsert = [];
            foreach ($logData as $log) {
                $total = floatval($log['total']);
                $volumeM3 = $total / 1000;

                $logsToInsert[] = [
                    'pelanggan_id' => $pelangganId,
                    'datetime' => $log['datetime'],
                    'total' => $total,
                    'volume_m3' => $volumeM3,
                ];
            }

            // Insert batch untuk performa
            BluetoothLog::insert($logsToInsert);

            return response()->json([
                'success' => true,
                'message' => 'Log data berhasil disimpan',
                'data' => [
                    'pelanggan_id' => $pelangganId,
                    'total_logs_saved' => count($logsToInsert),
                    'pelanggan_name' => $pelanggan->pelangganNama
                ]
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil log data berdasarkan pelanggan
     */
    public function getByPelanggan(Request $request, $pelangganId)
    {
        try {
            $validator = Validator::make(['pelanggan_id' => $pelangganId], [
                'pelanggan_id' => 'required|integer|exists:mspelanggan,pelangganId',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan'
                ], 404);
            }

            $logs = BluetoothLog::where('pelanggan_id', $pelangganId)
                ->orderBy('datetime', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $logs
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getByPelanggan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistik penggunaan air per pelanggan
     */
    public function getUsageStats($pelangganId)
    {
        try {
            $stats = BluetoothLog::where('pelanggan_id', $pelangganId)
                ->selectRaw('
                    DATE(datetime) as date,
                    SUM(total) as total_liter,
                    SUM(volume_m3) as total_m3,
                    COUNT(*) as reading_count
                ')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getUsageStats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}