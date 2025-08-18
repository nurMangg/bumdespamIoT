<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\Bulan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Roles;
use App\Models\Tagihan;
use App\Models\Tahun;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TransaksiController extends Controller
{
    protected $model = Pembayaran::class;
    protected $grid;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pembayaranId';

    public function __construct()
    {
        $this->title = 'Data Transaksi';
        $this->breadcrumb = 'Layanan';
        $this->route = 'transaksi';

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
                'label' => 'Meter Awal (m3)',
                'field' => 'tagihanMAwal',

            ),
            array(
                'label' => 'Meter Akhir (m3)',
                'field' => 'tagihanMAkhir',

            ),
            array(
                'label' => 'Status Tagihan',
                'field' => 'tagihanStatus',

            ),
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $isAdmin = Auth::user()->userRoleId != Roles::where('roleName', 'pelanggan')->first()->roleId;

            // Always use the correct primary key for Tagihan: tagihanId, not id
            if ($isAdmin) {
                $data = Tagihan::with([
                    'pelanggan' => function($q) {
                        $q->withTrashed();
                    },
                    'pembayaranInfo',
                    'bulan'
                ])
                ->whereNull('deleted_at');
            } else {
                $user = Pelanggan::where('pelangganUserId', Auth::user()->id)->first();
                $data = Tagihan::with([
                    'pelanggan' => function($q) {
                        $q->withTrashed();
                    },
                    'pembayaranInfo',
                    'bulan'
                ])
                ->where('tagihanPelangganId', $user->pelangganId)
                ->whereNull('deleted_at');
            }

            // Apply filters - default to current month/year
            $filterBulan = $request->get('filter_bulan', date('n')); // Current month (1-12)
            $filterTahun = $request->get('filter_tahun', date('Y')); // Current year
            $filterStatus = $request->get('filter_status'); // Status filter

            if ($filterBulan && $filterBulan != 'all') {
                $data->where('tagihanBulan', $filterBulan);
            }

            if ($filterTahun && $filterTahun != 'all') {
                $data->where('tagihanTahun', $filterTahun);
            }

            if ($filterStatus && $filterStatus != 'all') {
                $data->where('tagihanStatus', $filterStatus);
            }

            // Fix: Use correct searchable columns and avoid 'id'
            return datatables()::of($data)
                ->addIndexColumn()
                ->setRowId('tagihanId')
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? null) {
                        $search = strtolower($search);
                        $query->where(function ($q) use ($search) {
                            $q->whereRaw('LOWER(`tagihanKode`) LIKE ?', ["%{$search}%"])
                              ->orWhereRaw('LOWER(`tagihanMAwal`) LIKE ?', ["%{$search}%"])
                              ->orWhereRaw('LOWER(`tagihanMAkhir`) LIKE ?', ["%{$search}%"])
                              ->orWhereRaw('LOWER(`tagihanStatus`) LIKE ?', ["%{$search}%"])
                              ->orWhereHas('pelanggan', function($pelangganQuery) use ($search) {
                                  $pelangganQuery->withTrashed()->whereRaw('LOWER(`pelangganNama`) LIKE ?', ["%{$search}%"]);
                              });
                        });
                    }
                })
                ->order(function ($query) {
                    $query->orderBy('tagihanId', 'desc');
                })
                ->addColumn('action', function($row){
                    $tagihanId = Crypt::encryptString($row->tagihanId);
                    if ($row->tagihanStatus == 'Lunas') {
                        return '<div class="btn-group" role="group">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$tagihanId.'" data-original-title="Lihat Detail" class="bayar btn btn-primary btn-xs">
                                        <i class="fa-solid fa-circle-check"></i> Lihat
                                    </a>
                                </div>';
                    } else {
                        return '<div class="btn-group" role="group">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$tagihanId.'" data-original-title="Bayar Tagihan" class="bayar btn btn-success btn-xs">
                                        <i class="fa-solid fa-circle-dollar-to-slot"></i> Bayar
                                    </a>
                                </div>';
                    }
                })
                ->editColumn('tagihanStatus', function($row){
                    switch($row->tagihanStatus) {
                        case 'Lunas':
                            return '<span class="badge badge-success">Lunas</span>';
                        case 'Pending':
                            return '<span class="badge badge-warning">Pending</span>';
                        default:
                            return '<span class="badge badge-danger">Belum Lunas</span>';
                    }
                })
                ->addColumn('tagihanJumlah', function($row){
                    return 'Rp ' . number_format(optional($row->pembayaranInfo)->pembayaranJumlah ?? 0, 0, ',', '.');
                })
                ->addColumn('tagihanTerbit', function($row){
                    return optional($row->bulan)->bulanNama . ' - ' . $row->tagihanTahun;
                })
                ->addColumn('tagihanPelangganNama', function($row){
                    return optional($row->pelanggan)->pelangganNama ?? '<i class="text-muted">Pelanggan Dihapus</i>';
                })
                ->rawColumns(['action', 'tagihanStatus', 'tagihanPelangganNama'])
                ->make(true);
        }

        // Data untuk dropdown filter
        $bulanList = Bulan::orderBy('bulanId')->get();
        $tahunList = Tagihan::select('tagihanTahun')
            ->distinct()
            ->orderBy('tagihanTahun', 'desc')
            ->pluck('tagihanTahun');

        return view('transaksis.index', [
            'grid' => $this->grid,
            'title' => $this->title,
            'breadcrumb' => $this->breadcrumb,
            'route' => $this->route,
            'primaryKey' => $this->primaryKey,
            'bulanList' => $bulanList,
            'tahunList' => $tahunList,
            'currentMonth' => date('n'),
            'currentYear' => date('Y')
        ]);
    }

    public function getInfoAllTransaksi(Request $request)
    {
        if ($request->ajax()) {
            if (Auth::user()->userRoleId != Roles::where('roleName', 'pelanggan')->first()->roleId) {
                $tagihan = Tagihan::whereNull('deleted_at')->get();
            } else {
                $pelanggan = Pelanggan::where('pelangganUserId', Auth::user()->id)->first();
                $tagihan = Tagihan::whereNull('deleted_at')->where('tagihanPelangganId', $pelanggan->pelangganId)->get();

            }


            $tagihan->transform(function($item) {
                $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
                $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;
                return $item;
            });

            $totalSemuaTagihanBelumLunas = $tagihan->whereIn('tagihanStatus', ['Belum Lunas', 'Pending'])->sum('tagihanJumlahTotal');
            $jumlahTagihanBelumLunas = $tagihan->whereIn('tagihanStatus', ['Belum Lunas', 'Pending'])->count();
            $totalSemuaTagihanLunas = $tagihan->where('tagihanStatus', 'Lunas')->sum('tagihanJumlahTotal');
            $jumlahTagihanLunas = $tagihan->where('tagihanStatus', 'Lunas')->count();

            $tanggalSekarang = Carbon::now();
            $tanggalBulanLalu = $tanggalSekarang->subMonth();
            $bulanIni = $tanggalBulanLalu->month;
            $tahunIni = $tanggalBulanLalu->year;

            $totalTagihanLunasBulanIni = $tagihan->where('tagihanStatus', 'Lunas')->where('tagihanBulan', $bulanIni)->where('tagihanTahun', $tahunIni)->sum('tagihanJumlahTotal');
            $totalTagihanBelumLunasBulanIni = $tagihan->where('tagihanStatus', 'Belum Lunas')->where('tagihanBulan', $bulanIni)->where('tagihanTahun', $tahunIni)->sum('tagihanJumlahTotal');
            $jumlahTagihanLunasBulanIni = $tagihan->where('tagihanStatus', 'Lunas')->where('tagihanBulan', $bulanIni)->where('tagihanTahun', $tahunIni)->count();
            $jumlahTagihanBelumLunasBulanIni = $tagihan->where('tagihanStatus', 'Belum Lunas')->where('tagihanBulan', $bulanIni)->where('tagihanTahun', $tahunIni)->count();

            return response()->json([
                'totalSemuaTagihanBelumLunas' => $totalSemuaTagihanBelumLunas,
                'totalSemuaTagihanLunas' => $totalSemuaTagihanLunas,
                'jumlahTagihanBelumLunas' => $jumlahTagihanBelumLunas,
                'jumlahTagihanLunas' => $jumlahTagihanLunas,
                'totalTagihanLunasBulanIni' => $totalTagihanLunasBulanIni,
                'totalTagihanBelumLunasBulanIni' => $totalTagihanBelumLunasBulanIni,
                'jumlahTagihanLunasBulanIni' => $jumlahTagihanLunasBulanIni,
                'jumlahTagihanBelumLunasBulanIni' => $jumlahTagihanBelumLunasBulanIni
            ]);
        }
    }

    public function unduhStruk($id)
    {
        $tagihanId = Crypt::decryptString($id);
        $tagihan = Tagihan::findOrFail($tagihanId);
        // dd($tagihan);
        $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihanId)->firstOrFail();

        $data = [
            'tagihanKode' => $tagihan->tagihanKode,
            'pelangganKode' => $tagihan->pelanggan->pelangganKode,
            'pelangganNama' => $tagihan->pelanggan->pelangganNama,
            'tagihanMeteranAwal' => $tagihan->tagihanMAwal,
            'tagihanMeteranAkhir' => $tagihan->tagihanMAkhir,
            'nama_bulan' => $tagihan->bulan->bulanNama,
            'tagihanTahun' => $tagihan->tagihanTahun,
            'formattedTagihanTotal' => number_format($pembayaran->pembayaranJumlah, 0, ',', '.'),
            'formattedTotalDenda' => number_format($pembayaran->pembayaranAbonemen, 0, ',', '.'),
            'pembayaranKasirName' => User::find($pembayaran->pembayaranKasirId)->name ?? 'Aplikasi',
            'formattedTotal' => number_format($pembayaran->pembayaranJumlah + $pembayaran->pembayaranAbonemen, 0, ',', '.'),
            'date' => $tagihan->tagihanDibayarPadaWaktu,
            'name' => "Kasir",
        ];
        // dd($data);
        $pdf = Pdf::loadView('transaksis.struk.index', compact('data'))
            ->setPaper([0, 0, 306, 1181], 'portrait')
            ->set_option('isHtml5ParserEnabled', true)
            ->set_option('isPhpEnabled', true)
            ->set_option('defaultFont', 'Tahomaku');

        //return $pdf->stream('struk-pembayaran-' . $data['pelangganKode'] . '-' . $data['tagihanKode'] . '.pdf');

        return view('transaksis.struk.index2', compact('data'));

    }
}
