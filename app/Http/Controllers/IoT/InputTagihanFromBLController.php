<?php

namespace App\Http\Controllers\IoT;

use App\Http\Controllers\Controller;
use App\Models\BluetoothLog;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\HistoriInputTagihan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class InputTagihanFromBLController extends Controller
{
    protected $model = BluetoothLog::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelanggan_id';

    public function __construct()
    {
        $this->title = 'Input Tagihan dari IoT';
        $this->breadcrumb = 'IoT';
        $this->route = 'input-tagihan-iot';

        $this->form = array(
            array(
                'label' => 'Nama Pelanggan',
                'field' => 'pelangganNama',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Sinkron Terakhir',
                'field' => 'created_at',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Volume Bulan Lalu',
                'field' => 'volume_bulan_lalu',
                'type' => 'text',
                'placeholder' => '',
                'width' => 2,
            ),
            array(
                'label' => 'Volume Bulan Ini',
                'field' => 'volume',
                'type' => 'text',
                'placeholder' => '',
                'width' => 2,
            ),
            array(
                'label' => 'Status Tagihan',
                'field' => 'status_tagihan',
                'type' => 'text',
                'placeholder' => '',
                'width' => 2,
            ),

        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BluetoothLog::select('pelanggan_id')
                ->selectRaw('MAX(created_at) as latest_sync, MAX(volume_m3) as volume')
                ->groupBy('pelanggan_id');

            // Apply month and year filtering
            if ($request->filled('filter_month') && $request->filled('filter_year')) {
                $month = $request->filter_month;
                $year = $request->filter_year;
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
            } elseif ($request->filled('filter_year')) {
                $year = $request->filter_year;
                $query->whereYear('created_at', $year);
            }

            $data = $query->get()
                ->map(function($item){
                    $item->pelanggan_id_encrypted = Crypt::encryptString($item->pelanggan_id);
                    return $item;
                });
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function($row){
                        return '<input type="checkbox" class="bulk-checkbox" value="'.$row->pelanggan_id_encrypted.'">';
                    })
                    ->addColumn('action', function($row){
                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->pelanggan_id_encrypted.'" data-original-title="Lihat" class="lihat btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                </div>';
                    })
                    ->addColumn('pelangganNama', function($row){
                        $pelanggan = Pelanggan::where('pelangganKode', $row->pelanggan_id)->first();
                        return $pelanggan->pelangganNama ?? '-';
                    })
                    ->addColumn('volume_bulan_lalu', function($row){
                        $pelanggan = Pelanggan::where('pelangganKode', $row->pelanggan_id)->first();
                        if (!$pelanggan) return '-';

                        // Get filter month and year from request
                        $filterMonth = request('filter_month');
                        $filterYear = request('filter_year');

                        // Calculate previous month and year
                        if ($filterMonth && $filterYear) {
                            if ($filterMonth == 1) {
                                $prevMonth = 12;
                                $prevYear = $filterYear - 1;
                            } else {
                                $prevMonth = $filterMonth - 1;
                                $prevYear = $filterYear;
                            }
                        } else {
                            // If no filter, use current month - 1
                            $currentMonth = date('n');
                            $currentYear = date('Y');
                            if ($currentMonth == 1) {
                                $prevMonth = 12;
                                $prevYear = $currentYear - 1;
                            } else {
                                $prevMonth = $currentMonth - 1;
                                $prevYear = $currentYear;
                            }
                        }

                        $volume_bulan_lalu = Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId)
                            ->where('tagihanBulan', $prevMonth)
                            ->where('tagihanTahun', $prevYear)
                            ->first();
                        return $volume_bulan_lalu ? $volume_bulan_lalu->tagihanMAkhir : '-';
                    })
                    ->addColumn('volume', function($row){
                        // Tampilkan volume sekarang dan volume bulan lalu, digenapkan
                        $volumeSekarang = $row->volume ?? '-';
                        if ($volumeSekarang !== '-') {
                            $volumeSekarang = round($volumeSekarang);
                        }
                        // Ambil volume bulan lalu dari Tagihan
                        $pelanggan = Pelanggan::where('pelangganKode', $row->pelanggan_id)->first();
                        $volumeBulanLalu = '-';
                        if ($pelanggan) {
                            // Get filter month and year from request
                            $filterMonth = request('filter_month');
                            $filterYear = request('filter_year');

                            // Calculate previous month and year
                            if ($filterMonth && $filterYear) {
                                if ($filterMonth == 1) {
                                    $prevMonth = 12;
                                    $prevYear = $filterYear - 1;
                                } else {
                                    $prevMonth = $filterMonth - 1;
                                    $prevYear = $filterYear;
                                }
                            } else {
                                // If no filter, use current month - 1
                                $currentMonth = date('n');
                                $currentYear = date('Y');
                                if ($currentMonth == 1) {
                                    $prevMonth = 12;
                                    $prevYear = $currentYear - 1;
                                } else {
                                    $prevMonth = $currentMonth - 1;
                                    $prevYear = $currentYear;
                                }
                            }

                            $tagihanBulanLalu = Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId)
                                ->where('tagihanBulan', $prevMonth)
                                ->where('tagihanTahun', $prevYear)
                                ->first();
                            $volumeBulanLalu = $tagihanBulanLalu ? round($tagihanBulanLalu->tagihanMAkhir) : '-';
                        }
                        return $volumeSekarang + $volumeBulanLalu;
                    })
                    ->addColumn('created_at', function($row){
                        return $row->latest_sync ? \Carbon\Carbon::parse($row->latest_sync)->format('d-m-Y H:i:s') : '-';
                    })
                    ->addColumn('status_tagihan', function($row){
                        // Get filter month and year from request
                        $filterMonth = request('filter_month');
                        $filterYear = request('filter_year');

                        if (!$filterMonth || !$filterYear) {
                            return '-';
                        }

                        // Get pelanggan info
                        $pelanggan = Pelanggan::where('pelangganKode', $row->pelanggan_id)->first();
                        if (!$pelanggan) {
                            return '-';
                        }

                        // Cari tagihan dengan pelanggan, bulan, dan tahun yang sama
                        $tagihan = \App\Models\Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId)
                            ->where('tagihanBulan', $filterMonth)
                            ->where('tagihanTahun', $filterYear)
                            ->first();

                        if ($tagihan) {
                            return '<span class="badge badge-success">Sudah Diinput</span>';
                        } else {
                            return '<span class="badge badge-warning">Belum Diinput</span>';
                        }
                    })
                    ->rawColumns(['action', 'checkbox', 'status_tagihan'])
                    ->make(true);
        }

        return view('masters.bluetooth',
            [
                'form' => $this->form,
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        try {
            $selectedIds = $request->input('selected_ids', []);

            if (empty($selectedIds)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
            }

            $insertedCount = 0;
            $errors = [];

            foreach ($selectedIds as $encryptedId) {
                try {
                    $pelangganId = Crypt::decryptString($encryptedId);

                    // Get the latest BluetoothLog for this pelanggan
                    $bluetoothLog = BluetoothLog::where('pelanggan_id', $pelangganId)
                        ->latest('created_at')
                        ->first();

                    if (!$bluetoothLog) {
                        $errors[] = "Data BluetoothLog tidak ditemukan untuk pelanggan ID: {$pelangganId}";
                        continue;
                    }

                    // Get pelanggan info
                    $pelanggan = Pelanggan::where('pelangganKode', $pelangganId)->first();
                    if (!$pelanggan) {
                        $errors[] = "Data pelanggan tidak ditemukan untuk ID: {$pelangganId}";
                        continue;
                    }

                    // Get the latest tagihan for this pelanggan to get previous meter reading
                    $latestTagihan = Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId)
                        ->latest('created_at')
                        ->first();

                    $meterAwal = $latestTagihan ? (int) ceil($latestTagihan->tagihanMAkhir) : 0;
                    $meterAkhir = (int) ceil($bluetoothLog->volume_m3 + ($latestTagihan->tagihanMAkhir ?? 0));
                    $pemakaian = $meterAkhir - $meterAwal;

                    // Check if tagihan already exists for this month and year
                    $existingTagihan = Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId)
                        ->where('tagihanBulan', $bluetoothLog->created_at->format('m'))
                        ->where('tagihanTahun', $bluetoothLog->created_at->format('Y'))
                        ->first();

                    if ($existingTagihan) {
                        $errors[] = "Tagihan untuk {$pelanggan->pelangganNama} bulan {$bluetoothLog->created_at->format('m/Y')} sudah ada";
                        continue;
                    }

                    // Determine next bulan and tahun
                    if ($latestTagihan) {
                        if ((int)$latestTagihan->tagihanBulan == 12) {
                            $nextBulan = 1;
                            $nextTahun = (int)$latestTagihan->tagihanTahun + 1;
                        } else {
                            $nextBulan = (int)$latestTagihan->tagihanBulan + 1;
                            $nextTahun = (int)$latestTagihan->tagihanTahun;
                        }
                    } else {
                        $nextBulan = (int)$bluetoothLog->created_at->format('m');
                        $nextTahun = (int)$bluetoothLog->created_at->format('Y');
                    }

                    // Insert into tagihan table
                    $tagihan = new Tagihan();
                    $tagihan->tagihanKode = $this->generateUniqueCode();
                    $tagihan->tagihanPelangganId = $pelanggan->pelangganId;
                    $tagihan->tagihanInfoTarif = $pelanggan->golongan->golonganTarif ?? 0;
                    $tagihan->tagihanInfoAbonemen = $pelanggan->golongan->golonganAbonemen ?? 0;
                    $tagihan->tagihanBulan = $nextBulan;
                    $tagihan->tagihanTahun = $nextTahun;
                    $tagihan->tagihanMAwal = $meterAwal;
                    $tagihan->tagihanMAkhir = $meterAkhir;
                    $tagihan->tagihanUserId = Auth::user()->id;
                    $tagihan->tagihanTanggal = now()->format('Y-m-d');
                    $tagihan->tagihanStatus = 'Belum Lunas';
                    $tagihan->save();

                    Pembayaran::create([
                        'pembayaranTagihanId' => $tagihan->tagihanId,
                        'pembayaranJumlah' => ($tagihan->tagihanMAkhir - $tagihan->tagihanMAwal) * $tagihan->tagihanInfoTarif,
                        'pembayaranStatus' => 'Belum Lunas'
                    ]);

                    HistoriInputTagihan::create([
                        'tagihan_id' => $tagihan->tagihanId,
                        'lapangan_id' => Auth::user()->id,
                    ]);

                    $insertedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Error untuk pelanggan ID {$pelangganId}: " . $e->getMessage();
                }
            }

            $message = "Berhasil menginput {$insertedCount} tagihan";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'inserted_count' => $insertedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateUniqueCode(): string
    {
        $date = date('ym');
        $tagihanCount = Tagihan::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $tagihanPart = str_pad($tagihanCount + 1, 4, '0', STR_PAD_LEFT);

        return "TPAM-{$date}{$tagihanPart}";
    }

    public function edit($id)
    {
        $pelangganId = Crypt::decryptString($id);

        if (request()->ajax()) {
            $query = BluetoothLog::where('pelanggan_id', $pelangganId)
                ->select('created_at','datetime','volume_m3');

            // Apply month and year filtering
            if (request()->filled('filter_month') && request()->filled('filter_year')) {
                $month = request('filter_month');
                $year = request('filter_year');
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
            } elseif (request()->filled('filter_year')) {
                $year = request('filter_year');
                $query->whereYear('created_at', $year);
            } else {
                // If no filter, show current month data
                $query->whereYear('created_at', date('Y'))
                      ->whereMonth('created_at', date('n'));
            }

            $data = $query->orderBy('datetime', 'desc')->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('datetime', function($row) {
                    return $row->datetime ?? '-';
                })
                ->addColumn('volume', function($row) {
                    return $row->volume_m3 ?? '-';
                })
                ->rawColumns(['datetime', 'volume'])
                ->make(true);
        }

        // Get pelanggan info for modal header
        $pelanggan = Pelanggan::where('pelangganKode', $pelangganId)->first();
        $pelangganInfo = [
            'nama' => $pelanggan->pelangganNama ?? 'Tidak Diketahui',
            'kode' => $pelangganId
        ];

        return response()->json(['success' => true, 'pelanggan' => $pelangganInfo]);
    }
}
