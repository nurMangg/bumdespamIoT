<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\TagihanExport;
use App\Http\Controllers\Controller;
use App\Models\Bulan;
use App\Models\Golongan;
use App\Models\Pelanggan;
use App\Models\Roles;
use App\Models\Tagihan;
use App\Models\Tahun;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class LaporanTagihanController extends Controller
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
        $this->title = 'Laporan Tagihan';
        $this->breadcrumb = 'Laporan';
        $this->route = 'laporan-tagihan';

        $this->form = array(
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
                'label' => 'Dari Tahun',
                'field' => 'tagihanDariTahun',
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
                'label' => 'Sampai Tahun',
                'field' => 'tagihanSampaiTahun',
                'type' => 'select',
                'options' => Tahun::all()->pluck('tahun', 'tahun')->sort()->toArray(), // Add option values here
                'placeholder' => 'Semua Tahun',
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
                'label' => 'Meteran Awal (m3)',
                'field' => 'tagihanMAwal',
            ),
            array(
                'label' => 'Meteran Awal (m3)',
                'field' => 'tagihanMAkhir',
            ),
            array(
                'label' => 'Penggunaan Air (m3)',
                'field' => 'tagihanPenggunaan',
            ),
            array(
                'label' => 'Biaya Tagihan',
                'field' => 'formattedTagihanTotal',
            ),
            array(
                'label' => 'Biaya Abonemen',
                'field' => 'formattedAbonemen',
            ),
            array(
                'label' => 'Total Tagihan',
                'field' => 'formattedTagihanJumlahTotal',
            ),
            array(
                'label' => 'tagihan Status',
                'field' => 'tagihanStatus',
            ),
            
            
        );
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (Auth::user()->userRoleId != Roles::where('roleName', 'pelanggan')->first()->roleId) {
                $query = Tagihan::with('pelanggan')->select('*')
                ->orderBy('created_at', 'desc');
            } else {
                $pelanggan = Pelanggan::where('pelangganUserId', Auth::user()->id)->first();
                $query = Tagihan::with('pelanggan')->select('*')->where('tagihanPelangganId', $pelanggan->pelangganId)
                ->orderBy('created_at', 'desc');
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
                    ->addColumn('tagihanPenggunaan', function($row){
                        return $row->tagihanMAkhir - $row->tagihanMAwal . ' m3';
                    })
                    ->editColumn('formattedTagihanTotal', function($row){
                        return 'Rp. ' . number_format($row->tagihanTotal, 0, ',', '.');
                    })
                    ->editColumn('formattedAbonemen', function($row){
                        return 'Rp. ' . number_format($row->tagihanInfoAbonemen, 0, ',', '.');
                    })
                    ->addColumn('formattedTagihanJumlahTotal', function($row){
                        return 'Rp. ' . number_format($row->tagihanJumlahTotal, 0, ',', '.');
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
        if (Auth::user()->userRoleId != Roles::where('roleName', 'pelanggan')->first()->roleId) {
            $query = Tagihan::with('pelanggan')->select('*')
            ->orderBy('tagihanKode', 'asc');
        } else {
            $pelanggan = Pelanggan::where('pelangganUserId', Auth::user()->id)->first();
            $query = Tagihan::with('pelanggan')->select('*')->where('tagihanPelangganId', $pelanggan->pelangganId)
            ->orderBy('tagihanKode', 'asc');
        }

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
            $filterTanggal = "Semua Transaksi";
        }

        $filterPelanggan = isset($appliedFilters->pelangganRt) ? ' RT ' . $appliedFilters->pelangganRt : '';
        $filterPelanggan .= isset($appliedFilters->pelangganRw) ? ' RW ' . $appliedFilters->pelangganRw : '';
        if(!$filterPelanggan){
            $filterPelanggan = "Semua RT RW";
        }

        $data = $query->get();
        $jumlahBelumLunas = $data->where('tagihanStatus', 'Belum Lunas')->count();
        $jumlahLunas = $data->where('tagihanStatus', 'Lunas')->count();
        $data->transform(function($item) {
            $pelanggan = $item->pelanggan;
            $item->pelangganNama = $pelanggan->pelangganNama;
            $item->pelangganDesa = $pelanggan->pelangganDesa;
            $item->pelangganRTRW = $pelanggan->pelangganRt . ' / ' . $pelanggan->pelangganRw;
            $item->tagihanPenggunaan = $item->tagihanMAkhir - $item->tagihanMAwal;
            $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
            $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;

            $item->formattedAbonemen = 'Rp. ' . number_format($item->tagihanInfoAbonemen, 0, ',', '.');
            $item->formattedTagihanTotal = 'Rp. ' . number_format($item->tagihanTotal, 0, ',', '.');
            $item->formattedTagihanJumlahTotal = 'Rp. ' . number_format($item->tagihanJumlahTotal, 0, ',', '.');

            $item->tagihanTerbit = Bulan::where('bulanId', $item->tagihanBulan)->first()->bulanNama . ' - ' . $item->tagihanTahun;
            return $item;
        });

        $dataJumlah = [
            'jumlahBelumLunas' => $jumlahBelumLunas,
            'jumlahLunas' => $jumlahLunas,
            'totalSemuaTagihanBelumLunas' => $data->whereIn('tagihanStatus', ['Belum Lunas', 'Pending'])->sum('tagihanJumlahTotal'),
            'totalSemuaTagihanLunas' => $data->where('tagihanStatus', 'Lunas')->sum('tagihanJumlahTotal')
        ];

        // Generate PDF
        $pdf = Pdf::loadView('laporans.pdf.index', ['data' => $data, 'title' => $this->title, 'grid' => $this->grid, 'dataJumlah' => $dataJumlah, 'filterTanggal' => $filterTanggal, 'filterPelanggan' => $filterPelanggan])
            ->setPaper('a4', 'landscape');

        // Simpan PDF ke folder storage
        $fileName = 'laporan-tagihan-' . time() . '.pdf';
        Storage::disk('public')->put('exports/tagihan/' . $fileName, $pdf->output());

        $fileUrl = asset('storage/exports/tagihan/' . $fileName);


        // Return URL untuk file yang diunduh
        return response()->json([
            'status' => 'success',
            'url' => $fileUrl,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->query('filter', []);

        return Excel::download(new TagihanExport($filters), 'tagihan-' . Carbon::now()->format('d-m-Y_H-i-s') . '.xlsx');
    }

}
