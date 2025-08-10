<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\TransaksiExport;
use App\Http\Controllers\Controller;
use App\Models\Bulan;
use App\Models\Pelanggan;
use App\Models\Roles;
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

class LaporanTransaksiController extends Controller
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
        $this->route = 'laporan-transaksi';

        $this->form = array(
            array(
                'label' => 'Dari Tahun',
                'field' => 'tagihanDariTahun',
                'type' => 'select',
                'options' => Tahun::all()->pluck('tahun', 'tahun')->sort()->toArray(), // Add option values here
                'placeholder' => 'Semua Tahun',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'Dari Bulan',
                'field' => 'tagihanDariBulan',
                'type' => 'select',
                'options' => Bulan::all()->pluck('bulanNama', 'bulanId')->toArray(),
                'placeholder' => 'Semua Bulan',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'Sampai Tahun',
                'field' => 'tagihanSampaiTahun',
                'type' => 'select',
                'options' => Tahun::all()->pluck('tahun', 'tahun')->sort()->toArray(), // Add option values here
                'placeholder' => 'Semua Tahun',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'Sampai Bulan',
                'field' => 'tagihanSampaiBulan',
                'type' => 'select',
                'options' => Bulan::all()->pluck('bulanNama', 'bulanId')->toArray(),

                'placeholder' => 'Semua Bulan',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'Desa',
                'field' => 'pelangganDesa',
                'type' => 'select',
                'options' => Pelanggan::all()->pluck('pelangganDesa', 'pelangganDesa')->unique()->sort()->toArray(), 
                'placeholder' => 'Semua Desa',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'RT',
                'field' => 'pelangganRt',
                'type' => 'select',
                'options' => Pelanggan::all()->pluck('pelangganRt', 'pelangganRt')->unique()->sort()->toArray(), 
                'placeholder' => 'Semua RT',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'RW',
                'field' => 'pelangganRw',
                'type' => 'select',
                'options' => Pelanggan::all()->pluck('pelangganRw', 'pelangganRw')->unique()->sort()->toArray(), 
                'placeholder' => 'Semua RW',
                'width' => 3,
                'required' => true
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
                'label' => 'Metode Pembayaran',
                'field' => 'metodePembayaran'
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
            if (Auth::user()->userRoleId != Roles::where('roleName', 'pelanggan')->first()->roleId) {
                $query = Tagihan::with('pelanggan')->select('*')->where('tagihanStatus', 'Lunas')
                ->orderBy('tagihanDibayarPadaWaktu', 'desc');
            } else {
                $pelanggan = Pelanggan::where('pelangganUserId', Auth::user()->id)->first();
                $query = Tagihan::with('pelanggan')->select('*')->where('tagihanPelangganId', $pelanggan->pelangganId)
                ->where('tagihanStatus', 'Lunas')
                ->orderBy('tagihanDibayarPadaWaktu', 'desc');
            }
            

                if ($request->has('filter')) {
                    // Apply filters specified in the request
                    $filters = $request->input('filter');
                    foreach ($filters as $field => $value) {
                        if (!empty($value)) {
                            if ($field === 'tagihanDariTahun') {
                                // Filter berdasarkan dariTahun tagihan
                                $query->where('tagihanTahun', '>=', $value);
                            } elseif ($field === 'tagihanDariBulan') {
                                // Filter berdasarkan daribulan tagihan
                                $query->where('tagihanBulan', '>=', $value);
                            } elseif ($field === 'tagihanSampaiTahun') {
                                // Filter berdasarkan sampaiTahun tagihan
                                $query->where('tagihanTahun', '<=', $value);
                            } elseif ($field === 'tagihanSampaiBulan') {
                                // Filter berdasarkan sampai bulan tagihan
                                $query->where('tagihanBulan', '<=', $value);
                            } elseif ($field === 'pelangganDesa') {
                                // Filter berdasarkan rt pelanggan
                                $query->whereHas('pelanggan', function ($q) use ($value) {
                                    $q->where('pelangganDesa', $value);
                                });
                            } elseif ($field === 'pelangganRt') {
                                // Filter berdasarkan rt pelanggan
                                $query->whereHas('pelanggan', function ($q) use ($value) {
                                    $q->where('pelangganRt', $value);
                                });
                            } elseif ($field === 'pelangganRw') {
                                // Filter berdasarkan rw pelanggan
                                $query->whereHas('pelanggan', function ($q) use ($value) {
                                    $q->where('pelangganRw', $value);
                                });
                            } else {
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
                    ->addColumn('metodePembayaran', function($row){
                        return $row->pembayaranInfo->pembayaranMetode;
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

                if ($field === 'tagihanDariTahun') {
                    // Filter berdasarkan dariTahun tagihan
                    $query->where('tagihanTahun', '>=', $value);

                } elseif ($field === 'tagihanDariBulan') {
                    // Filter berdasarkan daribulan tagihan
                    $query->where('tagihanBulan', '>=', $value);
                } elseif ($field === 'tagihanSampaiTahun') {
                    // Filter berdasarkan sampaiTahun tagihan
                    $query->where('tagihanTahun', '<=', $value);
                } elseif ($field === 'tagihanSampaiBulan') {
                    // Filter berdasarkan sampai bulan tagihan
                    $query->where('tagihanBulan', '<=', $value);
                } elseif ($field === 'pelangganDesa') {
                    // Filter berdasarkan rt pelanggan
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganDesa', $value);
                    });
                } elseif ($field === 'pelangganRt') {
                    // Filter berdasarkan rt pelanggan
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganRt', $value);
                    });
                } elseif ($field === 'pelangganRw') {
                    // Filter berdasarkan rw pelanggan
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganRw', $value);
                    });
                } else {
                    // Filter untuk kolom lain di tabel tagihans
                    $query->where($field, 'like', '%' . $value . '%');
                }
            }
        }

        $filterTanggal = isset($appliedFilters->tagihanDariBulan) ? Bulan::where('bulanId', $appliedFilters->tagihanDariBulan)->first()->bulanNama . ' ' . $appliedFilters->tagihanDariTahun : '';
        $filterTanggal .= isset($appliedFilters->tagihanSampaiBulan) ? ' s/d ' . Bulan::where('bulanId', $appliedFilters->tagihanSampaiBulan)->first()->bulanNama . ' ' . $appliedFilters->tagihanSampaiTahun : '';

        if(!$filterTanggal){
            $filterTanggal = "Semua Transaksi Lunas";
        }

        $filterPelanggan = isset($appliedFilters->pelangganRt) ? ' RT ' . $appliedFilters->pelangganRt : '';
        $filterPelanggan .= isset($appliedFilters->pelangganRw) ? ' RW ' . $appliedFilters->pelangganRw : '';
        if(!$filterPelanggan){
            $filterPelanggan = "Semua RT RW";
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

        $dataJumlah = [
            'totalSemuaTagihanLunas' => $data->where('tagihanStatus', 'Lunas')->sum('tagihanJumlahTotal')
        ];

    
        // Generate PDF
        $pdf = Pdf::loadView('laporans.pdf.transaksi', ['data' => $data, 'title' => $this->title, 'grid' => $this->grid, 'dataJumlah' => $dataJumlah, 'filterTanggal' => $filterTanggal, 'filterPelanggan' => '-'])
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

        return Excel::download(new TransaksiExport($filters), 'transaksi-' . Carbon::now()->format('d-m-Y_H-i-s') . '.xlsx');
    }
}
