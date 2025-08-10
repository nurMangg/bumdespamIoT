<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\HistoryWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class GolonganController extends BaseController
{
    protected $model = Golongan::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'golonganId';

    public function __construct()
    {
        $this->title = 'Golongan Tarif';
        $this->breadcrumb = 'Master Data';
        $this->route = 'golongan-tarif';

        $this->form = array(
            array(
                'label' => 'Nama Golongan',
                'field' => 'golonganNama',
                'type' => 'text',
                'placeholder' => 'Masukkan Nama',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Tarif Harga',
                'field' => 'golonganTarif',
                'type' => 'number',
                'placeholder' => 'Masukkan Harga Tarif',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Abonemen',
                'field' => 'golonganAbonemen',
                'type' => 'number',
                'placeholder' => 'Masukkan Harga Abonemen',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Status Golongan',
                'field' => 'golonganStatus',
                'type' => 'select',
                'placeholder' => 'Pilih Status',
                'width' => 6,
                'required' => true,
                'options' => [
                    'Aktif' => 'Aktif',
                    'Tidak Aktif' => 'Tidak Aktif'
                ],
                'default' => 'Aktif'
            ),
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Golongan::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $golonganId = Crypt::encryptString($row->golonganId);
                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$golonganId.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$golonganId.'" data-original-title="Delete" class="delete btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></a>
                                </div>';
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
        $request->validate($rules);

        $newGolongan = Golongan::create([
            'golonganNama' => $request->golonganNama,
            'golonganTarif' => $request->golonganTarif,
            'golonganDenda' => $request->golonganDenda,
            'golonganStatus' => $request->golonganStatus
        ]);

        HistoryWeb::create([
            'riwayatUserId' => Auth::user()->id,
            'riwayatTable' => 'Golongan',
            'riwayatAksi' => 'Tambah Golongan',
            'riwayatData' => json_encode($newGolongan),
        ]);

        return response()->json(['success' => 'Data Berhasil Disimpan']);
    }
}
