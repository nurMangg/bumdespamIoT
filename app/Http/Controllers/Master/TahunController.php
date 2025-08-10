<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TahunController extends BaseController
{
    protected $model = Tahun::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'tahunId';

    public function __construct()
    {
        $this->title = 'Data Tahun';
        $this->breadcrumb = 'Master Data';
        $this->route = 'tahun';

        $this->form = array(
            array(
                'label' => 'Tahun',
                'field' => 'tahun',
                'type' => 'number',
                'placeholder' => 'Masukkan Tahun',
                'width' => 6,
                'required' => true

            ),
            array(
                'label' => 'Status Tahun',
                'field' => 'tahunStatus',
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
            $data = Tahun::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $tahunId = Crypt::encryptString($row->tahunId);
                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$tahunId.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$tahunId.'" data-original-title="Delete" class="delete btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></a>
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

        Tahun::create([
            'tahun' => $request->tahun,
            'tahunStatus' => $request->tahunStatus
        ]);

        return response()->json(['success' => 'Data Berhasil Disimpan']);
    }
}
