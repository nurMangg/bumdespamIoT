<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class RoleController extends BaseController
{
    protected $model = Roles::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'roleId';

    public function __construct()
    {
        $this->title = 'Role Pengguna';
        $this->breadcrumb = 'Setting';
        $this->route = 'role-aplikasi';

        $this->form = array(
            array(
                'label' => 'Nama Role',
                'field' => 'roleName',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Menu',
                'field' => 'roleMenuId',
                'type' => 'checkbox',
                'options' => Menu::whereNotNull('menuParentId')->pluck('menuName', 'menuId')->toArray(),
                'placeholder' => 'Masukan Menu',
                'required' => true,
                'checkbox' => true
            ),
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Roles::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $roleId = Crypt::encryptString($row->roleId);

                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$roleId.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$roleId.'" data-original-title="Delete" class="delete btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></a>
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
                if (isset($value['checkbox']) && $value['checkbox']) {
                    $rules[$value['field']] = 'required|array';
                } else {
                    $rules[$value['field']] = 'required';
                }
            }

            
        }
        $request->validate($rules);

        Roles::create([
            'roleName' => $request->roleName,
            'roleMenuId' => json_encode($request->roleMenuId),
        ]);

        return response()->json(['success' => 'Data Berhasil Disimpan']);
    }
}
