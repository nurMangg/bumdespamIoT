<?php

namespace App\Http\Controllers\Notifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Silvanix\Wablas\Message;
use Silvanix\Wablas\Report;

class NotifikasiController extends Controller
{
    protected $grid;
    protected $title;
    protected $breadcrumb;
    protected $route;

    public function __construct()
    {
        $this->title = 'Laporan Notifikasi Whatsapp';
        $this->breadcrumb = 'Notifikasi';
        $this->route = 'notifikasi';

        $this->grid = array(
            array(
                'label' => 'ID Notifikasi',
                'field' => 'id',
            ),
            array(
                'label' => 'No Tujuan',
                'field' => 'phone.to',
            ),
            array(
                'label' => 'Status',
                'field' => 'message',
            ),
            array(
                'label' => 'Kategori',
                'field' => 'category',
            ),
            array(
                'label' => 'Status',
                'field' => 'statusNotifikasi',
            ),
            array(
                'label' => 'Pesan Dibuat',
                'field' => 'date.created_at',
            ),
            array(
                'label' => 'Pesan Terkirim',
                'field' => 'date.updated_at',
            ),
        );
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $report = new Report();
            $response = $report->real_time(); // Ambil langsung tanpa getContent()
            
            // dd($response);
            $data = collect($response['data'])->sortByDesc('date.created_at')->values()->toArray();
            //dd($data);

            // dd($data);
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('statusNotifikasi', function($row){
                        if (array_key_exists('status', $row) && $row['status'] == 'pending') {
                            return '<span class="badge badge-warning">Pending</span>';
                        } elseif (array_key_exists('status', $row) && $row['status'] == 'sent') {
                            return '<span class="badge badge-primary">Terkirim</span>';
                        } elseif (array_key_exists('status', $row) && $row['status'] == 'read') {
                            return '<span class="badge badge-success">Dibaca</span>';
                        } else {
                            return '<span class="badge badge-danger">Undefined</span>';
                        }
                    })
                    ->rawColumns(['statusNotifikasi'])
                    ->make(true);
        }


        return view('notifikasi.index', 
            [
                'grid' => $this->grid, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
        ]);
    }

    public function send_message_test()
    {
        $send = new Message();
    
        $phones = '085713050749';
    
        // Data tagihan (bisa diambil dari database)
        $nama_pelanggan = 'Nur Rohman';
        $bulan = 'Januari';
        $tahun = '2025';
        $link = 'https://pdam.withmangg.my.id';
    
        // Pesan utama dengan tambahan informasi tagihan
        $message = "*Tagihan PDAM Anda Sudah Tersedia!*\n\n"
                  ."Halo, *$nama_pelanggan*! 👋\n\n"
                  ."📅 Tagihan Anda bulan $bulan - $tahun sudah tersedia!. \n\n"
                  ."Segera lakukan pembayaran untuk memastikan layanan tetap berjalan lancar.\n"
                  ."Pembayaran dapat dilakukan melalui metode yang tersedia.\n\n"
                  ."🔗 *Cek tagihan dan bayar sekarang:* $link \n\n"
                  ."Terima kasih telah menggunakan layanan kami! \n\n"
                  ."—\n"
                  ."🔹 *PDAM BUMDES PAGAR SEJAHTERA* 🔹";
    
        // Mengirim pesan ke WhatsApp melalui Wablas
        $send_text = $send->single_text($phones, $message);
    
        return response()->json($send_text);
    }

    public function report() {
        $report = new Report();
        $send_report = $report->real_time();

        return response()->json($send_report);
    }
    
}
