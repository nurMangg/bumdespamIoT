<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\PelangganExport;
use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\Pelanggan;
use App\Models\SettingWeb;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenggunaController extends Controller
{
    protected $model = Pelanggan::class;
    protected $grid;
    protected $form;

    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelangganKode';

    public function __construct()
    {
        $this->title = 'Laporan Pelanggan';
        $this->breadcrumb = 'Laporan';
        $this->route = 'laporan-pelanggan';

        $this->form = array(
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
                'options' => Pelanggan::all()->pluck('pelangganRt', 'pelangganRt')->unique()->sort()->toArray(), // Add option values here
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
            array(
                'label' => 'Golongan',
                'field' => 'pelangganGolonganId',
                'type' => 'select',
                'width' => 6,
                'placeholder' => 'Semua Golongan',
                'required' => true,
                'options' => Golongan::pluck('golonganNama', 'golonganId')->toArray()

            ),
            array(
                'label' => 'Status',
                'field' => 'pelangganStatus',
                'type' => 'select',
                'width' => 6,
                'placeholder' => 'Semua Status',
                'required' => true,
                'options' => [
                    'Aktif' => 'Aktif',
                    'Tidak Aktif' => 'Tidak Aktif'
                ]

            ),
        );

        $this->grid = array(
            array(
                'label' => 'Kode Pelanggan',
                'field' => 'pelangganKode',
            ),
            array(
                'label' => 'Nama',
                'field' => 'pelangganNama',
            ),
            [
                'label' => 'Nomor HP',
                'field' => 'pelangganPhone',
            ],
            array(
                'label' => 'Desa',
                'field' => 'pelangganDesa',
            ),
            array(
                'label' => 'RT',
                'field' => 'pelangganRt',
            ),
            array(
                'label' => 'RW',
                'field' => 'pelangganRw',
            ),
            array(
                'label' => 'Golongan',
                'field' => 'pelangganGolonganId',
            ),
            array(
                'label' => 'Bergabung',
                'field' => 'pelangganBergabung',
            ),
            array(
                'label' => 'Status',
                'field' => 'pelangganStatus',
            ), 
        );
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Pelanggan::select('pelangganId', 'pelangganKode', 'pelangganPhone', 'pelangganNama', 'pelangganDesa', 'pelangganRt', 'pelangganRw', 'pelangganStatus', 'pelangganGolonganId', 'created_at')
                ->orderBy('created_at', 'desc');

            if ($request->has('filter')) {
                // Apply any filters specified in the request
                $filters = $request->input('filter');
                foreach ($filters as $field => $value) {
                    if (!empty($value)) {
                        $query->where($field, 'like', '%' . $value . '%');
                    }
                }
            }

            $data = $query->get();

            return datatables()::of($data)
                    ->addIndexColumn()
                    ->editColumn('pelangganGolonganId', function($row){
                        return Golongan::find($row->pelangganGolonganId)->golonganNama;
                    })
                    ->addColumn('pelangganBergabung', function($row){
                        return \Carbon\Carbon::parse($row->created_at)->locale('id_ID')->translatedFormat('d F Y');
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
        $query = Pelanggan::query();

        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                $query->where($field, 'like', '%' . $value . '%');
            }
        }

        $data = $query->orderBy('pelangganKode', 'asc')->get();
        $data->transform(function($item) {
            $item->pelangganGolonganId = Golongan::find($item->pelangganGolonganId)->golonganNama;
            return $item;
        });

        // Setting Web
        $web = SettingWeb::first();

        // Generate PDF
        $pdf = Pdf::loadView('laporans.pdf.pelanggan', ['data' => $data, 'title' => $this->title, 'grid' => $this->grid, 'web' => $web])->setPaper('a4', 'landscape');

        // Simpan PDF ke folder storage
        $fileName = 'laporan-pelanggan-' . time() . '.pdf';
        Storage::disk('public')->put('exports/pelanggan/' . $fileName, $pdf->output());

        $fileUrl = asset('storage/exports/pelanggan/' . $fileName);


        // Return URL untuk file yang diunduh
        return response()->json([
            'status' => 'success',
            'url' => $fileUrl,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->query('filter', []);

        return Excel::download(new PelangganExport($filters), 'pelanggan-' . Carbon::now()->format('d-m-Y_H-i-s') . '.xlsx');
    }
}
