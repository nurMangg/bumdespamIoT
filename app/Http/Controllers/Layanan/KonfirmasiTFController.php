<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Roles;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class KonfirmasiTFController extends Controller
{
    protected $model = BuktiPembayaran::class;
    protected $grid;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'buktiPembayaranId';

    public function __construct()
    {
        $this->title = 'Konfirmasi Transaksi Manual';
        $this->breadcrumb = 'Layanan';
        $this->route = 'konfirmasi-transaksi-manual';

        $this->grid = array(
            array(
                'label' => 'Kode Tagihan',
                'field' => 'tagihanKode',
            ),
            array(
                'label' => 'Nama Pelanggan',
                'field' => 'tagihanPelangganNama',
            ),
            array(
                'label' => 'Tagihan Terbit',
                'field' => 'tagihanTerbit',
                ),
            array(
                'label' => 'Total Tagihan + Abonemen',
                'field' => 'totalTagihan',
                
            ),
            array(
                'label' => 'Bukti Pembayaran',
                'field' => 'buktiPembayaranFoto',
            ),
            array(
                'label' => 'Tanggal Pembayaran',
                'field' => 'buktiPembayaranTanggal',
            ),
            array(
                'label' => 'Status',
                'field' => 'pembayaranStatus',
            ),
        );
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pembayaran::where('pembayaranMetode', 'BANK MANUAL')->get();
            
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $PembayaranId = Crypt::encryptString($row->pembayaranId);
                    if ($row->pembayaranStatus == 'Lunas') {
                        return 'Approved';
                    } else {
                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                <a href="javascript:void(0)" data-toggle="tooltip" data-pembayaran="'.$PembayaranId.'" data-original-title="konfirmasi" id="konfirmasi" class="konfirmasi btn btn-primary btn-xs">
                                    <i class="fa-solid fa-circle-check"></i> Lihat
                                </a>
                            </div>';
                    }
                })
                ->editColumn('pembayaranStatus', function($row){
                    if ($row->pembayaranStatus == 'Lunas') {
                        return '<span class="badge badge-success">Lunas</span>';
                    } else {
                        return '<span class="badge badge-warning">Pending</span>';
                    }
                })
                ->addColumn('buktiPembayaranFoto', function($row){
                    $fotoBase64 = optional($row->buktiPembayaran)->buktiPembayaranFoto;
                    
                    if ($fotoBase64) {
                        $imageSrc = 'data:image/jpeg;base64,' . $fotoBase64;
                        return '<a href="javascript:void(0)" data-toggle="lightbox" data-title="Bukti Pembayaran" data-id="' .$imageSrc . '" class="lihat-foto">
                                    <img src="' . $imageSrc . '" class="img-thumbnail" style="max-width: 100px;" />
                                </a>';
                    }
                    
                    return '-'; // Jika tidak ada foto
                })
                
                ->addColumn('totalTagihan', function($row){
                    return 'Rp. ' . number_format($row->pembayaranJumlah + $row->pembayaranAbonemen, 0, ',', '.');
                })
                ->addColumn('tagihanTerbit', function($row){
                    return optional($row->tagihan)->bulan->bulanNama . ' ' . optional($row->tagihan)->tagihanTahun;
                })
                ->addColumn('tagihanKode', function($row){
                    return optional($row->tagihan)->tagihanKode;
                })
                ->addColumn('tagihanPelangganNama', function($row){
                    return optional($row->tagihan->pelanggan)->pelangganNama;
                })
                ->addColumn('buktiPembayaranTanggal', function($row){
                    return optional($row->buktiPembayaran)->created_at;
                })
                ->rawColumns(['action', 'pembayaranStatus', 'buktiPembayaranFoto'])
                ->make(true);

        }

        return view('transaksis.index2', 
            [
                'grid' => $this->grid, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function konfirmasiTransaksi(Request $request) {
        $request->validate([
            'pembayaranId' => 'required',
        ]);

        $data = $request->pembayaranId;

        $decryptId = crypt::decryptString($data);

        $pembayaran = Pembayaran::find($decryptId);

        if (!$pembayaran) {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran tidak ditemukan'], 404);
        }

        $tagihan = Tagihan::find($pembayaran->pembayaranTagihanId);

        if (!$tagihan) {
            return response()->json(['status' => 'error', 'message' => 'Tagihan tidak ditemukan'], 404);
        }

        $pembayaran->pembayaranStatus = 'Lunas';
        $pembayaran->pembayaranKasirId = Auth::user()->id;
        $pembayaran->save();

        $tagihan->tagihanStatus = 'Lunas';
        $tagihan->tagihanDibayarPadaWaktu = now();
        $tagihan->save();

        return response()->json(['status' => 'success', 'message' => 'Pembayaran berhasil dikonfirmasi'], 200);
        
    }

    public function getInfoAllTransaksiManual(Request $request)
    {
        
        $tagihan = Tagihan::whereHas('pembayaranInfo', function($q) {
            $q->where('pembayaranMetode', 'BANK MANUAL');
        })->whereNull('deleted_at')->get();

            
            
        $tagihan->transform(function($item) {
            $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
            $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;
            return $item;
        });

        $totalSemuaTagihanBelumLunas = $tagihan->where('tagihanStatus', 'Belum Lunas')->sum('tagihanJumlahTotal');
        $jumlahTagihanBelumLunas = $tagihan->where('tagihanStatus', 'Belum Lunas')->count();
        $totalSemuaTagihanLunas = $tagihan->where('tagihanStatus', 'Lunas')->sum('tagihanJumlahTotal');
        $jumlahTagihanLunas = $tagihan->where('tagihanStatus', 'Lunas')->count();

        
        return response()->json([
            'totalSemuaTagihanBelumLunas' => $totalSemuaTagihanBelumLunas,
            'totalSemuaTagihanLunas' => $totalSemuaTagihanLunas,
            'jumlahTagihanBelumLunas' => $jumlahTagihanBelumLunas,
            'jumlahTagihanLunas' => $jumlahTagihanLunas,
        ]);
    }
}
