<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class PenggunaAplikasiController extends BaseController
{
    protected $model = User::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelangganId';

    public function __construct()
    {
        $this->title = 'Pengguna Aplikasi';
        $this->breadcrumb = 'Setting';
        $this->route = 'pengguna-aplikasi';

        $this->form = array(
            array(
                'label' => 'Nama Pengguna',
                'field' => 'name',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
            ),
            array(
                'label' => 'Username',
                'field' => 'username',
                'type' => 'text',
                'placeholder' => 'Masukkan username',
                'width' => 6,
                'required' => true

            ),
            array(
                'label' => 'Role',
                'field' => 'userRoleId',
                'type' => 'select',
                'options' => Roles::pluck('roleName', 'roleId')->toArray(),
                'placeholder' => 'Pilih Role',
                'width' => 6,
                'required' => true
            ),
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $userId = Crypt::encryptString($row->id);

                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$userId.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$userId.'" data-original-title="Delete" class="delete btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['action'])
                    ->editColumn('userRoleId', function ($row) {
                        return $row->role->roleName;
                    })
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

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make('password'),
            'userRoleId' => $request->userRoleId,
        ]);

        return response()->json(['success' => 'Data Berhasil Disimpan']);
    }
}
