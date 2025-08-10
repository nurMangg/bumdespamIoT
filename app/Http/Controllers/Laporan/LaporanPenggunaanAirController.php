<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class LaporanPenggunaanAirController extends Controller
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
                'label' => 'Kode Pelanggan',
                'field' => 'pelangganKode',
                'type' => 'select',
                'options' => Pelanggan::all()->pluck('pelangganDesa', 'pelangganDesa')->unique()->sort()->toArray(),
                'placeholder' => 'Semua Desa',
                'width' => 6,
                'required' => true
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
                'field' => 'pelangganRT',
            ),
            array(
                'label' => 'RW',
                'field' => 'pelangganRW',
            ),
            array(
                'label' => 'Golongan Tarif',
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
            $query = Pelanggan::select('pelangganId', 'pelangganKode', 'pelangganPhone', 'pelangganNama', 'pelangganDesa', 'pelangganRT', 'pelangganRW', 'pelangganStatus', 'pelangganGolonganId', 'created_at')
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
}
