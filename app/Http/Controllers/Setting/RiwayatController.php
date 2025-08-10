<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\HistoryWeb;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    protected $model = HistoryWeb::class;
    protected $grid;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'riwayatId';

    public function __construct()
    {
        $this->title = 'Riwayat Website';
        $this->breadcrumb = 'Setting';
        $this->route = 'riwayat-website';

        $this->grid = array(
            array(
                'label' => 'Tabel',
                'field' => 'riwayatTable',
            ),
            array(
                'label' => 'Aksi',
                'field' => 'riwayatAksi',
            ),
            array(
                'label' => 'Data',
                'field' => 'riwayatData',
            ),
            array(
                'label' => 'User ID',
                'field' => 'riwayatUserId',
            ),
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = HistoryWeb::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }

        return view('setting.riwayat', 
            [
                'form' => $this->grid, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }
}
