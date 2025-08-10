<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MenuController extends BaseController
{
    protected $model = Menu::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'menuId';

    public function __construct()
    {
        $this->title = 'Menu Aplikasi';
        $this->breadcrumb = 'Setting';
        $this->route = 'menu-aplikasi';

        $this->form = array(
            array(
                'label' => 'Nama Menu',
                'field' => 'menuName',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Route Menu',
                'field' => 'menuRoute',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Parent Menu',
                'field' => 'menuParentId',
                'type' => 'select',
                'options' => Menu::where('menuParentId', null)->pluck('menuName', 'menuId')->toArray(),
                'placeholder' => 'Pilih Parent Menu',
                'width' => 6,
            ),
            array(
                'label' => 'Urutan Menu',
                'field' => 'menuOrder',
                'type' => 'number',
                'placeholder' => '',
                'width' => 2,
            ),
            array(
                'label' => 'Icon Menu',
                'field' => 'menuIcon',
                'type' => 'text',
                'placeholder' => '',
                'width' => 2,
            ),
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Menu::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $menuId = Crypt::encryptString($row->menuId);

                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$menuId.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$menuId.'" data-original-title="Delete" class="delete btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></a>
                                </div>';
                    })
                    ->editColumn('menuParentId', function ($row) {
                        if ($row->menuParentId == null) {
                            return 'Root';
                        } else {
                            return $row->menuParent->menuName;
                        }
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

        Menu::create([
            'menuName' => $request->menuName,
            'menuRoute' => $request->menuRoute,
            'menuParentId' => $request->menuParentId,
            'menuOrder' => $request->menuOrder,
            'menuIcon' => $request->menuIcon,
        ]);

        return response()->json(['success' => 'Data Berhasil Disimpan']);
    }
}
