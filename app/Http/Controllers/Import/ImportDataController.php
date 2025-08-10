<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Imports\DataImport;
use App\Models\HistoryWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ImportDataController extends Controller
{
    protected $grid;
    protected $form;

    protected $title;
    protected $image;

    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelangganKode';

    public function __construct()
    {
        $this->title = 'Import Data Tagihan';
        $this->breadcrumb = 'Import Data';
        $this->route = 'import-data-tagihan';
        $this->image = 'images/ss_tagihan.png';

        $this->form = array(
            array(
                'label' => 'Pilih File',
                'field' => 'file',
                'type' => 'file',
                'placeholder' => 'Pilih File',
                'width' => 6,
                'required' => true
            ),
            
        );
    }

    public function index()
    {
        return view('imports.index', [
            'title' => $this->title,
            'breadcrumb' => $this->breadcrumb,
            'route' => $this->route,
            'form' => $this->form,
            'image' => $this->image
        ]);
    }

    public function store(Request $request)
    {
        // Validasi file upload
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,csv,ods|max:2048', // Validasi file excel/csv
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Proses import file
        try {
            Excel::import(new DataImport, $request->file('file'));

            HistoryWeb::create([
                'riwayatUserId' => Auth::user()->id,
                'riwayatTable' => 'Pelanggan, Tagihan, Pembayaran',
                'riwayatAksi' => 'Import Data',
                'riwayatData' => json_encode(['file' => $request->file('file')->getClientOriginalName()]),
            ]);
            
            return response()->json(['success' => 'Data pelanggan berhasil diimport.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat import: ' . $e->getMessage()], 500);
        }
    }
}
