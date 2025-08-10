<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Bulan;
use App\Models\Golongan;
use App\Models\Pelanggan;
use App\Models\Roles;
use App\Models\Tagihan;
use App\Models\Tahun;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class TagihanController extends Controller
{
    protected $model = Tahun::class;
    protected $grid;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelangganKode';

    public function __construct()
    {
        $this->title = 'Data Tagihan';
        $this->breadcrumb = 'Layanan';
        $this->route = 'tagihan';

        $this->grid = array(
            array(
                'label' => 'Kode Pelanggan',
                'field' => 'pelangganKode',
            ),
            array(
                'label' => 'Pelanggan ID',
                'field' => 'pelangganNama',
            ),
            [
                'label' => 'RT/RW',
                'field' => 'pelangganAlamat'
            ],
            array(
                'label' => 'Golongan Tarif',
                'field' => 'pelangganGolonganId',
            ),
            [
                'label' => 'Tagihan Terakhir',
                'field' => 'tagihanTerakhir',
            ],
            [
                'label' => 'Tagihan Belum Lunas',
                'field' => 'tagihanBelumLunas'
            ]
        );
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pelanggan::select('pelangganId', 'pelangganKode', 'pelangganNama', 'pelangganRt', 'pelangganRw', 'pelangganGolonganId')
                ->orderBy('created_at', 'desc')
                ->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $encodedKode = Crypt::encryptString($row->pelangganKode);
                    return '<div class="btn-group" role="group" aria-label="Basic example">
                                <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$encodedKode.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-eye"></i> Lihat</a>
                            </div>';
                })
                ->editColumn('pelangganGolonganId', function($row){
                    return Golongan::find($row->pelangganGolonganId)->golonganNama;
                })
                ->addColumn('pelangganAlamat', function($row){
                    return $row->pelangganRt . '/' . $row->pelangganRw;
                })
                ->addColumn('tagihanTerakhir', function($row){
                    $tagihan = Tagihan::where('tagihanPelangganId', $row->pelangganId)->orderBy('created_at', 'desc')->first();
                    return $tagihan ? Bulan::where('bulanId',$tagihan->tagihanBulan)->first()->bulanNama . ' - ' . $tagihan->tagihanTahun : '-';
                })
                ->addColumn('tagihanBelumLunas', function($row){
                    $jumlahBelumLunas = Tagihan::where('tagihanPelangganId', $row->pelangganId)
                        ->where('tagihanStatus', '!=', 'Lunas')
                        ->count();
                    
                    if ($jumlahBelumLunas >= 3) {
                        return '<span class="text-danger">' . $jumlahBelumLunas . ' </span>';
                    } else {
                        return $jumlahBelumLunas > 0 ? $jumlahBelumLunas : '-';
                    }
                })
                ->rawColumns(['action', 'tagihanBelumLunas'])
                ->make(true);
        }
        

        return view('layanans.index', 
            [
                'form' => $this->grid, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function getInfoTagihan(Request $request)
    {
        if ($request->ajax()) {
            if (Auth::user()->userRoleId != Roles::where('roleName', 'pelanggan')->first()->roleId) {
                $tagihan = Tagihan::whereNull('deleted_at')->get();
            } else {
                $pelanggan = Pelanggan::where('pelangganUserId', Auth::user()->id)->first();
                $tagihan = Tagihan::whereNull('deleted_at')->where('tagihanPelangganId', $pelanggan->pelangganId)->get();

            }
            
            $tanggalSekarang = Carbon::now();

            $bulanIni = $tanggalSekarang->month;
            $tahunIni = $tanggalSekarang->year;
            
            $tanggalBulanDepan = $tanggalSekarang->copy()->addMonth();
            $bulanDepan = $tanggalBulanDepan->month;
            $tahunDepan = $tanggalBulanDepan->year;
            
            $tanggalBulanLalu = $tanggalSekarang->copy()->subMonth();
            $bulanLalu = $tanggalBulanLalu->month;
            $tahunLalu = $tanggalBulanLalu->year;


            // dd($bulanIni, $tahunIni, $bulanLalu, $tahunLalu);

            $jumlahTagihan = Pelanggan::whereNull('deleted_at')->where('pelangganStatus', 'Aktif')->count();
            $jumlahTagihanBulanLalu = $tagihan->where('tagihanBulan', $bulanLalu)->where('tagihanTahun', $tahunLalu)->count();
            $jumlahTagihanBulanIni = $tagihan->where('tagihanBulan', $bulanIni)->where('tagihanTahun', $tahunIni)->count();
            $jumlahTagihanBulanDepan = $tagihan->where('tagihanBulan', $bulanDepan)->where('tagihanTahun', $tahunDepan)->count();


            return response()->json([
                'jumlahInputTagihan' => $jumlahTagihan,
                'bulanLalu' => Bulan::where('bulanId', $bulanLalu)->first()->bulanNama,
                'bulanIni' => Bulan::where('bulanId', $bulanIni)->first()->bulanNama,
                'bulanDepan' => Bulan::where('bulanId', $bulanDepan)->first()->bulanNama,
                'jumlahInputTagihanBulanLalu' => $jumlahTagihanBulanLalu,
                'jumlahInputTagihanBulanIni' => $jumlahTagihanBulanIni,
                'jumlahInputTagihanBulanDepan' => $jumlahTagihanBulanDepan
            ]);
        };
        
    }
}
