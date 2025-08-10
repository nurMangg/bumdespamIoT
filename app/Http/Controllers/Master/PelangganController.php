<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\HistoryWeb;
use App\Models\Pelanggan;
use App\Models\Roles;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PelangganController extends BaseController
{
    protected $model = Pelanggan::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelangganId';

    public function __construct()
    {
        $this->title = 'Pelanggan';
        $this->breadcrumb = 'Master Data';
        $this->route = 'pelanggan';

        $this->form = array(
            array(
                'label' => 'Kode Pelanggan',
                'field' => 'pelangganKode',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'disabled' => true,
            ),
            array(
                'label' => 'Nama Pelanggan',
                'field' => 'pelangganNama',
                'type' => 'text',
                'placeholder' => 'Masukkan Nama',
                'width' => 6,
                'required' => true

            ),
            array(
                'label' => 'No. Telepon (62)',
                'field' => 'pelangganPhone',
                'type' => 'number',
                'placeholder' => '62',
                'width' => 6,
            ),
            array(
                'label' => 'Desa',
                'field' => 'pelangganDesa',
                'type' => 'text',
                'placeholder' => 'Masukkan Alamat',
                'width' => 6,
            ),
            array(
                'label' => 'RT',
                'field' => 'pelangganRt',
                'type' => 'number',
                'placeholder' => 'Masukkan RT',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'RW',
                'field' => 'pelangganRw',
                'type' => 'number',
                'placeholder' => 'Masukkan RW',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'Golongan',
                'field' => 'pelangganGolonganId',
                'type' => 'select',
                'width' => 6,
                'placeholder' => 'Pilih Golongan',
                'required' => true,
                'options' => Golongan::pluck('golonganNama', 'golonganId')->toArray()

            ),
            array(
                'label' => 'Status',
                'field' => 'pelangganStatus',
                'type' => 'select',
                'width' => 6,
                'placeholder' => 'Pilih Status',
                'required' => true,
                'options' => [
                    'Aktif' => 'Aktif',
                    'Tidak Aktif' => 'Tidak Aktif'
                ],
                'default' => 'Aktif'

            ),
        );
    }

    // Generate Unique Code Pelanggan
    public function generateUniqueCode(): string
    {
        // Menghitung semua pelanggan termasuk yang di-soft delete
        $pelangganCount = Pelanggan::withTrashed()->count();
    
        $pelangganPart = str_pad($pelangganCount + 1, 4, '0', STR_PAD_LEFT);
    
        return "PAM{$pelangganPart}";
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pelanggan::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $pelangganId = Crypt::encryptString($row->pelangganId);

                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$pelangganId.'" data-original-title="View" class="view btn btn-warning btn-xs"><i class="fa-regular fa-eye"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$pelangganId.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$pelangganId.'" data-original-title="Delete" class="delete btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></a>
                                </div>';
                    })
                    ->editColumn('pelangganGolonganId', function ($row) {
                        return $row->golongan->golonganNama;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('masters.index', 
            [
                'form' => $this->form, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function store(Request $request)
    {
        $rules = [];
        foreach ($this->form as $value) {
            if (isset($value['required']) && $value['required']) {
                $rules[$value['field']] = 'required';
            }
        }
        
        if ($request->filled('pelangganPhone')) {
            $rules['pelangganPhone'] = array_merge(
                isset($rules['pelangganPhone']) 
                    ? (array) $rules['pelangganPhone'] 
                    : [],
                ['regex:/^62[0-9]+$/']
            );
        }
    
        $request->validate($rules);

        $user = User::create([
            'name' => $request->pelangganNama,
            'username' => strtolower($this->generateUniqueCode()),
            'password' => Hash::make('password'),
            'userRoleId' => Roles::where('roleName', 'pelanggan')->first()->roleId
        ]);

        $newPelanggan = Pelanggan::create([
            'pelangganKode' => $this->generateUniqueCode(),
            'pelangganNama' => $request->pelangganNama,
            'pelangganPhone' => $request->pelangganPhone,
            'pelangganDesa' => $request->pelangganDesa,
            'pelangganRt' => $request->pelangganRt,
            'pelangganRw' => $request->pelangganRw,
            'pelangganGolonganId' => $request->pelangganGolonganId,
            'pelangganStatus' => $request->pelangganStatus,
            'pelangganUserId' => $user->id
        ]);

        

        HistoryWeb::create([
            'riwayatUserId' => Auth::user()->id,
            'riwayatTable' => 'Pelanggan',
            'riwayatAksi' => 'Tambah Data',
            'riwayatData' => json_encode($newPelanggan),
        ]);

        return response()->json(['success' => 'Data Berhasil Disimpan']);
    }

    public function viewKartu($id)
    {
        $decodeId = Crypt::decryptString($id);

        $model = app($this->model);
        $data = $model->find($decodeId);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        // dd($data);
        return view('masters.cardnama.index', ['data'=> $data]);
    }

    public function cetakKartu()
    {
        // $data = Pelanggan::limit(4)->get();
        $data = Pelanggan::all();
        return view('masters.cardnama.cetak-kartu-bw', ['data'=> $data]);

        // $pdf = Pdf::loadView('masters.cardnama.cetak-kartu', compact('data'));

        // // Download PDF
        // return $pdf->download('business_cards.pdf');
    }
    
    public function search(Request $request)
    {
        $search = $request->search;
        $page = $request->page ?? 1;
        $per_page = 10;

        $query = Pelanggan::where('pelangganStatus', 'Aktif')
            ->where(function($q) use ($search) {
                $q->where('pelangganKode', 'like', "%{$search}%")
                  ->orWhere('pelangganNama', 'like', "%{$search}%");
            });

        $total = $query->count();
        
        $data = $query->skip(($page - 1) * $per_page)
                     ->take($per_page)
                     ->get();

        return response()->json([
            'data' => $data,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $per_page,
            'last_page' => ceil($total / $per_page)
        ]);
    }
}
