<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Bulan;
use App\Models\Golongan;
use App\Models\HistoryWeb;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Tahun;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Silvanix\Wablas\Message;

class AksiTagihanController extends BaseController
{
    protected $model = Tagihan::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'tagihanId';

    public function __construct()
    {
        $this->title = 'Data Tagihan';
        $this->breadcrumb = 'Layanan';
        $this->route = 'aksi-tagihan';

        $this->form = array(
            array(
                'label' => 'Kode Tagihan',
                'field' => 'tagihanKode',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'disabled' => true
            ),
            array(
                'label' => 'Bulan Tagihan',
                'field' => 'tagihanBulan',
                'type' => 'select',
                'placeholder' => 'Pilih Bulan',
                'width' => 6,
                'required' => true,
                'options' => [
                    '1' => 'Januari',
                    '2' => 'Februari',
                    '3' => 'Maret',
                    '4' => 'April',
                    '5' => 'Mei',
                    '6' => 'Juni',
                    '7' => 'Juli',
                    '8' => 'Agustus',
                    '9' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember'
                ]
            ),
            array(
                'label' => 'Tahun Tagihan',
                'field' => 'tagihanTahun',
                'type' => 'select',
                'placeholder' => 'Pilih Tahun',
                'width' => 6,
                'required' => true,
                'options' => Tahun::all()->pluck('tahun', 'tahun')->toArray(),
            ),
            array(
                'label' => 'Meter Awal',
                'field' => 'tagihanMAwal',
                'type' => 'number',
                'placeholder' => 'Masukkan Meter Awal',
                'width' => 6,
                'required' => true,
            ),
            array(
                'label' => 'Meter Akhir',
                'field' => 'tagihanMAkhir',
                'type' => 'number',
                'placeholder' => 'Masukkan Meter Akhir',
                'width' => 6,
                'required' => true,
            ),
            
            array(
                'label' => 'Tagihan Abonemen (Default 3000)',
                'field' => 'tagihanInfoAbonemen',
                'type' => 'text',
                'placeholder' => 'Kosongkan Jika Abonemen Rp. 3000',
                'width' => 6,
            ),
            array(
                'label' => 'Status Tagihan',
                'field' => 'tagihanStatus',
                'type' => 'select',
                'placeholder' => 'Pilih Status',
                'width' => 6,
                'required' => true,
                'options' => [
                    'Lunas' => 'Lunas',
                    'Belum Lunas' => 'Belum Lunas',
                    'Pending' => 'Pending'
                ],
                'default' => 'Belum Lunas'
            ),
        );
    }

    public function index(Request $request)
    {
        $tagihan = Pelanggan::where('pelangganKode', $request->pelangganKode)->first()->pelangganId;
        if ($request->ajax()) {
            $data = Tagihan::where('tagihanPelangganId', $tagihan)->whereNull('deleted_at')->get();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $tagihanId = Crypt::encryptString($row->tagihanId);
                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$tagihanId.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$tagihanId.'" data-original-title="Delete" class="delete btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></a>
                                </div>';
                    })
                    ->addColumn('tagihanJumlah', function($row){
                        $jumlah = Pembayaran::where('pembayaranTagihanId', $row->tagihanId)->first()->pembayaranJumlah;
                        $total = $jumlah + $row->tagihanInfoAbonemen;
                        return 'Rp ' . number_format($total, 0, ',', '.');
                    })
                    ->editColumn('tagihanInfoAbonemen', function($row){
                        return 'Rp ' . number_format($row->tagihanInfoAbonemen, 0, ',', '.');
                    })
                    ->editColumn('tagihanBulan', function($row){
                        return Bulan::where('bulanId', $row->tagihanBulan)->first()->bulanNama;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
    
    public function show($pelangganKode)
    {
        $decodePelangganKode = Crypt::decryptString($pelangganKode);
        $detailPelanggan = Pelanggan::where('pelangganKode', $decodePelangganKode)->first();
        $penggunaanTagihan = Tagihan::
            selectRaw('MAX(tagihanMAkhir) as tagihanMAkhir')
            ->selectRaw('SUM((tagihanMAkhir - tagihanMAwal) * tagihanInfoTarif + tagihanInfoAbonemen) as totalTagihan')
            ->where('tagihanPelangganId', $detailPelanggan->pelangganId)
            // ->where('tagihanStatus', 'Lunas')
            ->first();

        $jumlahTagihanBelumLunas = Tagihan::where('tagihanPelangganId', $detailPelanggan->pelangganId)
            ->where('tagihanStatus', '!=', 'Lunas')
            ->count();

        // dd($jumlahTagihanBelumLunas);
        
        // dd($penggunaanTagihan);

        return view('layanans.detail', 
            [
                'detailPelanggan' => $detailPelanggan,
                'jumlahTagihanBelumLunas' => $jumlahTagihanBelumLunas,
                'penggunaanTagihan' => $penggunaanTagihan,
                'form' => $this->form, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    // Generate Unique Code Tagihan
    public function generateUniqueCode(): string
    {
        $date = date('ym');
        $tagihanCount = Tagihan::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $tagihanPart = str_pad($tagihanCount + 1, 4, '0', STR_PAD_LEFT);

        return "TPAM-{$date}{$tagihanPart}";
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

        $tagihan = Tagihan::create([
            'tagihanKode' => $this->generateUniqueCode(),
            'tagihanPelangganId' => $request->pelangganId,
            'tagihanBulan' => $request->tagihanBulan,
            'tagihanTahun' => $request->tagihanTahun,
            'tagihanInfoTarif' => Pelanggan::where('pelangganId', $request->pelangganId)->first()->golongan->golonganTarif,
            'tagihanInfoAbonemen' => Pelanggan::where('pelangganId', $request->pelangganId)->first()->golongan->golonganAbonemen,
            'tagihanMAwal' => $request->tagihanMAwal,
            'tagihanMAkhir' => $request->tagihanMAkhir,
            'tagihanUserId' => Auth::user()->id,
            'tagihanTanggal' => date('Y-m-d'),
            'tagihanStatus' => $request->tagihanStatus,
        ]);

        Pembayaran::create([
            'pembayaranTagihanId' => $tagihan->tagihanId,
            'pembayaranJumlah' => (($request->tagihanMAkhir - $request->tagihanMAwal) * $tagihan->tagihanInfoTarif),
            'pembayaranStatus' => 'Belum Lunas'
        ]);

        HistoryWeb::create([
            'riwayatUserId' => Auth::user()->id,
            'riwayatTable' => 'Tagihan',
            'riwayatAksi' => 'Input Tagihan',
            'riwayatData' => json_encode($tagihan),
        ]);

        return response()->json(['success' => 'Data Berhasil Disimpan']);
    }

    public function update(Request $request, $id)
    {
        $model = app($this->model);
        $id = Crypt::decryptString($id);
        $data = $model->find($id);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }
        
        // dd($data);


        $data->update($request->except(['pembayaranJumlah', 'pembayaranStatus']));

        $pembayaran = Pembayaran::where('pembayaranTagihanId', $data->tagihanId)->first();
        $pembayaran->update([
            'pembayaranAbonemen' => $request->tagihanInfoAbonemen ?? '0',
            'pembayaranJumlah' => (($request->tagihanMAkhir - $request->tagihanMAwal) * $data->tagihanInfoTarif),
            'pembayaranStatus' => 'Belum Lunas'
        ]);

        HistoryWeb::create([
            'riwayatUserId' => Auth::user()->id,
            'riwayatTable' => 'Pembayaran',
            'riwayatAksi' => 'update',
            'riwayatData' => json_encode($pembayaran),
        ]);

        return response()->json(['message' => 'Data updated successfully', 'data' => json_encode($data)]);
    }

    public function kirimPeringatan($id)
    {
        // $send = new Message();
    
        $link = 'https://bumdespam.withmangg.my.id';

        $decodePelangganKode = Crypt::decryptString($id);
        $pelanggan = Pelanggan::where('pelangganKode', $decodePelangganKode)->first();
        if (!$pelanggan) {
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan'], 404);
        }
        $phones = $pelanggan->pelangganPhone;
    
        $tagihan = Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId);
        $bulan = $tagihan->where('tagihanStatus', '!=', 'Lunas')->count();
    
        // Pesan utama dengan tambahan informasi tagihan
        $message = "*Peringatan Tagihan PDAM*\n\n"
                  ."Halo, *$pelanggan->pelangganNama*! 👋\n\n"
                  ."Kami informasikan bahwa tagihan PDAM Anda telah menunggak selama $bulan bulan. Mohon segera melakukan pembayaran untuk menghindari pemutusan layanan. \n\n"
                  ."Silakan lakukan pembayaran melalui kanal resmi kami. Untuk informasi lebih lanjut, hubungi layanan pelanggan kami.\n"
                  ."Pembayaran dapat dilakukan melalui metode yang tersedia.\n\n"
                  ."🔗 *Cek tagihan dan bayar sekarang di aplikasi mobile atau melalui :* $link \n\n"
                  ."Terima kasih telah menggunakan layanan kami! \n\n"
                  ."—\n"
                  ."🔹 *PDAM BUMDES PAGAR SEJAHTERA* 🔹";
    
        $response = Http::withBasicAuth(
            env('GOWA_USERNAME'),
            env('GOWA_PASSWORD')
        )->post(env('GOWA_API_URL'), [
            'phone' => $phones . '@s.whatsapp.net',
            'message' => $message,
            'reply_message_id' => '',
            'is_forwarded' => false,
        ]);
        Log::info("Response dari Wablas: " . json_encode($response));

        return response()->json(['success' => true, 'message' => 'Pemberitahuan Berhasil Dikirim']);

    }
}
