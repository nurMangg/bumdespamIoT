<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\HistoryWeb;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AksiTransaksiController extends Controller
{
    protected $model = Tagihan::class;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'tagihanId';
    protected $paymentMethod;

    public function __construct()
    {
        $this->title = 'Data Transaksi';
        $this->breadcrumb = 'Layanan';
        $this->route = 'aksi-tagihan';
        $this->paymentMethod = array(
            // array(
            //     'label' => 'QRIS (0.7%)',
            //     'value' => 'QRIS',
            //     'price' => '0.7%'
            // ),
            // array(
            //     'label' => 'VA Bank BCA (5000)',
            //     'value' => 'BCA',
            //     'price' => '5000'
            // ),
            // array(
            //     'label' => 'Alfamart',
            //     'value' => 'ALFAMART',
            //     'price' => '5000'
            // ),
            // array(
            //     'label' => 'Indomaret',
            //     'value' => 'INDOMARET',
            //     'price' => '5000'
            // ),
            array(
                'label' => 'TRANSFER BANK - CEK MANUAL',
                'value' => 'BANK MANUAL',
                'price' => '0'
            ),
            array(
                'label' => 'VA Bank BCA (5000) - DUITKU',
                'value' => 'BCA DUITKU',
                'price' => '5000'
            ),
            array(
                'label' => 'VA Bank BRI (3000) - DUITKU',
                'value' => 'BRI DUITKU',
                'price' => '3000'
            ),
            array(
                'label' => 'VA Bank MANDIRI (3000) - DUITKU',
                'value' => 'MANDIRI DUITKU',
                'price' => '3000'
            ),
            array(
                'label' => 'VA Bank BNI (3000) - DUITKU',
                'value' => 'BNI DUITKU',
                'price' => '3000'
            ),
            array(
                'label' => 'QRIS DUITKU',
                'value' => 'QRIS DUITKU',
                'price' => '0'
            ),
            array(
                'label' => 'ALFAMART DUITKU',
                'value' => 'ALFAMART DUITKU',
                'price' => '2500'
            ),
            array(
                'label' => 'INDOMARET DUITKU',
                'value' => 'INDOMARET DUITKU',
                'price' => '2500'
            ),
        );
    }


    public function show($tagihanId)
    {
        $decodeTagihanKode = Crypt::decryptString($tagihanId);
        
        $detailtagihan = Tagihan::where('tagihanId', $decodeTagihanKode)->first();

        // dd($detailtagihan->pembayaranInfo);
        $pelangganInfo = Pelanggan::where('pelangganId', $detailtagihan->tagihanPelangganId)->first();

        $detailTagihanCrypt = Crypt::encryptString($detailtagihan->tagihanId);
        
        // dd($penggunaanTagihan);

        return view('transaksis.detail', 
            [
                'detailPelanggan' => $pelangganInfo,
                'detailTagihan' => $detailtagihan,
                'tagihanIdCrypt' => $detailTagihanCrypt,
                'paymentMethod' => $this->paymentMethod,
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function pembayaranTunai(Request $request)
    {
        $request->validate([
            'tagihanId' => 'required|string',
            'totalTagihan' => 'required|numeric',
            'pembayaranAdminFee' => 'nullable|numeric',
            'totalTagihanTunai' => 'required|numeric',
            'uangKembaliTunai' => 'required|numeric',
            'uangBayarTunai' => 'required|numeric'
        ]);
    
        $tagihanId = $request->tagihanId;
        $decodeTagihanKode = Crypt::decryptString($tagihanId);
    
        $detailtagihan = Tagihan::where('tagihanId', $decodeTagihanKode)->first();
    
        if(!$detailtagihan){
            return response()->json(['error' => "Tagihan tidak ditemukan"], 404);
        }
    
        if ($detailtagihan->tagihanStatus != "Belum Lunas") {
            return response()->json(['error' => "Tagihan Sudah Lunas"], 400);
        }

        $totalTagihanReal = (($detailtagihan->tagihanMAkhir - $detailtagihan->tagihanMAwal) * $detailtagihan->tagihanInfoTarif);

        // dd($totalTagihanReal);
        if ($totalTagihanReal >= $request->uangBayarTunai) {
            return response()->json(['error' => "Uang Bayar Tidak Sesuai Dengan Total Tagihan"], 400);
        }
    
        $pembayaran = Pembayaran::where('pembayaranTagihanId', $detailtagihan->tagihanId)->first();
        if (!$pembayaran) {
            return response()->json(['error' => "Pembayaran Tidak ada"], 400);
        }

        DB::beginTransaction();
        try {
            $detailtagihan->tagihanStatus = "Lunas";
            $detailtagihan->tagihanDibayarPadaWaktu = now();
            $detailtagihan->save();
    
            $pembayaran->update([
                'pembayaranMetode' => "Tunai",
                'pembayaranUang' => $request->input('uangBayarTunai'),
                'pembayaranKembali' => $request->input('uangKembaliTunai'),
                'pembayaranAbonemen' => $detailtagihan->tagihanInfoAbonemen,
                'pembayaranJumlah' => $totalTagihanReal,
                'pembayaranAdminFee' => $request->input('pembayaranAdminFee') ?? '0',
                'pembayaranStatus' => "Lunas",
                'pembayaranKasirId' => Auth::user()->id,
            ]);
    
            HistoryWeb::create([
                'riwayatUserId' => Auth::user()->id,
                'riwayatTable' => 'Transaksi',
                'riwayatAksi' => 'Transaksi Tunai',
                'riwayatData' => json_encode($pembayaran),
            ]);
    
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Halaman Kasir
    public function showfromkasir($tagihanId)
    {
        $decodeTagihanKode = Crypt::decryptString($tagihanId);
        
        $detailtagihan = Tagihan::where('tagihanId', $decodeTagihanKode)->first();

        // dd($detailtagihan->pembayaranInfo);
        $pelangganInfo = Pelanggan::where('pelangganId', $detailtagihan->tagihanPelangganId)->first();

        $detailTagihanCrypt = Crypt::encryptString($detailtagihan->tagihanId);
        
        // dd($penggunaanTagihan);

        return view('kasir.detail-tagihan', 
            [
                'detailPelanggan' => $pelangganInfo,
                'detailTagihan' => $detailtagihan,
                'tagihanIdCrypt' => $detailTagihanCrypt,
                'paymentMethod' => $this->paymentMethod,
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    
    
}
