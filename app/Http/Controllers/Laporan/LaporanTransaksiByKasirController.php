<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\TransaksiKasirExport;
use App\Http\Controllers\Controller;
use App\Models\Bulan;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Tahun;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class LaporanTransaksiByKasirController extends Controller
{
    protected $model = Tagihan::class;
    protected $grid;
    protected $form;

    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelangganKode';

    public function __construct()
    {
        $this->title = 'Laporan Transaksi';
        $this->breadcrumb = 'Laporan';
        $this->route = 'laporan-transaksi-by-kasir';

        $this->form = array(
            array(
                'label' => 'Kasir',
                'field' => 'pelangganKasir',
                'type' => 'select',
                'options' => User::withAdminOrKasirRole()->pluck('name', 'id')->toArray(),
                'placeholder' => 'Semua Role',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => '',
                'field' => 'hariIni',
                'type' => 'checkbox',
                'options' => [
                    'true' => 'Tampilkan Hari ini?'
                ],
                'placeholder' => '',
                'width' => 3,
                'required' => false
            ),

            
        );

        $this->grid = array(
            array(
                'label' => 'Kode Tagihan',
                'field' => 'tagihanKode',
            ),
            array(
                'label' => 'Nama',
                'field' => 'pelangganNama',
            ),
            array(
                'label' => 'Desa',
                'field' => 'pelangganDesa',
            ),
            array(
                'label' => 'RT/RW',
                'field' => 'pelangganRTRW',
            ),
            array(
                'label' => 'Tagihan Terbit',
                'field' => 'tagihanTerbit',
            ),
            array(
                'label' => 'Total Tagihan',
                'field' => 'formattedTagihanJumlahTotal',
            ),
            array(
                'label' => 'tagihan Status',
                'field' => 'tagihanStatus',
            ),
            array(
                'label' => 'Terverifikasi',
                'field' => 'terverifikasi'
            ),

            array(
                'label' => 'Kasir',
                'field' => 'kasir'
            )

            
            
        );
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
                $query = Tagihan::with('pelanggan')->select('*')
                    ->where('tagihanStatus', 'Lunas')
                    ->orderBy('tagihanDibayarPadaWaktu', 'desc');
            
                if ($request->has('filter')) {
                    // Apply filters specified in the request
                    $filters = $request->input('filter');
                    foreach ($filters as $field => $value) {
                        if (!empty($value)) {
                            if ($field === 'pelangganKasir') {
                                // Filter berdasarkan rw pelanggan
                                $query->whereHas('pembayaranInfo', function ($q) use ($value) {
                                    $q->where('pembayaranKasirId', $value);
                                });
                            } elseif ($field === 'hariIni') {
                                // Filter berdasarkan hari ini
                                $query->whereDate('tagihanDibayarPadaWaktu', date('Y-m-d'));
                            } 
                            else {
                                // Filter untuk kolom lain di tabel tagihans
                                $query->where($field, 'like', '%' . $value . '%');
                            }
                        }
                    }
                }

            $data = $query->get();
            $data->transform(function($item) {
                $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
                $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;
                return $item;
            });

            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('pelangganNama', function($row){
                        return $row->pelanggan->pelangganNama;
                    })
                    ->addColumn('pelangganDesa', function($row){
                        return $row->pelanggan->pelangganDesa;
                    })
                    ->addColumn('pelangganRTRW', function($row){
                        return $row->pelanggan->pelangganRt . ' / ' . $row->pelanggan->pelangganRw;
                    })
                    ->addColumn('tagihanTerbit', function($row){
                        return Bulan::where('bulanId', $row->tagihanBulan)->first()->bulanNama . ' - ' . $row->tagihanTahun;
                    })
                    ->addColumn('formattedTagihanJumlahTotal', function($row){
                        return 'Rp. ' . number_format($row->tagihanJumlahTotal, 0, ',', '.');
                    })
                    ->addColumn('terverifikasi', function($row){
                        return $row->tagihanDibayarPadaWaktu;
                    })
                    ->addColumn('kasir', function($row){
                        return User::find($row->pembayaranInfo->pembayaranKasirId)->name ?? 'Aplikasi';
                    })
                    ->make(true);
        }

        return view('laporans.index', 
            [
                'form' => $this->form,
                'grid' => $this->grid, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->input('filter', []);
        $query = Tagihan::with('pelanggan')->select('*')->where('tagihanStatus', 'Lunas')
                ->orderBy('tagihanKode', 'asc');

        $appliedFilters = new stdClass();

        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                $appliedFilters->$field = $value;

                if ($field === 'pelangganKasir') {
                    // Filter berdasarkan rw pelanggan
                    $query->whereHas('pembayaranInfo', function ($q) use ($value) {
                        $q->where('pembayaranKasirId', $value);
                    });
                } elseif ($field === 'hariIni') {
                    // Filter berdasarkan hari ini
                    $query->whereDate('tagihanDibayarPadaWaktu', date('Y-m-d'));
                } else {
                    // Filter untuk kolom lain di tabel tagihans
                    $query->where($field, 'like', '%' . $value . '%');
                }
            }
        }

        $data = $query->get();
        $data->transform(function($item) {
            $pelanggan = $item->pelanggan;
            $item->pelangganNama = $pelanggan->pelangganNama;
            $item->pelangganDesa = $pelanggan->pelangganDesa;
            $item->pelangganRTRW = $pelanggan->pelangganRt . ' / ' . $pelanggan->pelangganRw;
            $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
            $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;
            $item->terverifikasi = $item->tagihanDibayarPadaWaktu;
            $item->metodePembayaran = $item->pembayaranInfo->pembayaranMetode;
            $item->kasir = User::find($item->pembayaranInfo->pembayaranKasirId)->name ?? 'Aplikasi';
            $item->formattedTagihanJumlahTotal = 'Rp. ' . number_format($item->tagihanJumlahTotal, 0, ',', '.');

            $item->tagihanTerbit = Bulan::where('bulanId', $item->tagihanBulan)->first()->bulanNama . ' - ' . $item->tagihanTahun;
            return $item;
        });

        $filterTanggal = isset($appliedFilters->hariIni) ? date('d-m-Y') : 'Semua Tanggal';
        // dd($filterTanggal);
        $filterKasir = isset($appliedFilters->pelangganKasir) ? User::find($appliedFilters->pelangganKasir)->name : 'Semua Kasir';


        $dataJumlah = [
            'totalSemuaTagihanLunas' => $data->where('tagihanStatus', 'Lunas')->sum('tagihanJumlahTotal')
        ];

    
        // Generate PDF
        $pdf = Pdf::loadView('laporans.pdf.transaksi-by-kasir', 
            ['data' => $data, 
            'title' => $this->title, 
            'grid' => $this->grid, 
            'dataJumlah' => $dataJumlah,
            'filterTanggal' => $filterTanggal,
            'filterKasir' => $filterKasir
            
            ])
            ->setPaper('a4', 'landscape');

        // Simpan PDF ke folder storage
        $fileName = 'laporan-transaksi-' . time() . '.pdf';
        Storage::disk('public')->put('exports/transaksi/' . $fileName, $pdf->output());

        $fileUrl = asset('storage/exports/transaksi/' . $fileName);


        // Return URL untuk file yang diunduh
        return response()->json([
            'status' => 'success',
            'url' => $fileUrl,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->query('filter', []);

        return Excel::download(new TransaksiKasirExport($filters), 'transaksi-kasir-' . Carbon::now()->format('d-m-Y_H-i-s') . '.xlsx');
    }
}
