<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Silvanix\Wablas\Message;

abstract class Controller
{
    public function send_message($phones, $nama_pelanggan, $bulan, $tahun)
    {
        $send = new Message();
    
        // $phones = '085713050749';
    
        // // Data tagihan (bisa diambil dari database)
        // $nama_pelanggan = 'Nur Rohman';
        // $bulan = 'Januari';
        // $tahun = '2025';
        $link = 'https://pdam.withmangg.my.id';
    
        // Pesan utama dengan tambahan informasi tagihan
        $message = "*Tagihan PDAM Anda Sudah Tersedia!*\n\n"
                  ."Halo, *$nama_pelanggan*! ðŸ‘‹\n\n"
                  ."ðŸ“… Tagihan Anda bulan $bulan - $tahun sudah tersedia!. \n\n"
                  ."Segera lakukan pembayaran untuk memastikan layanan tetap berjalan lancar.\n"
                  ."Pembayaran dapat dilakukan melalui metode yang tersedia.\n\n"
                  ."ðŸ”— *Cek tagihan dan bayar sekarang:* $link \n\n"
                  ."Terima kasih telah menggunakan layanan kami! \n\n"
                  ."â€”\n"
                  ."ðŸ”¹ *PDAM BUMDES PAGAR SEJAHTERA* ðŸ”¹";
    
        // Mengirim pesan ke WhatsApp melalui Wablas
        $send_text = $send->single_text($phones, $message);
        // return response()->json($send_text);
        Log::info("Response dari Wablas: " . json_encode($send_text));
    }
    
    public function send_payment_confirmation_message($phones, $nama_pelanggan, $nomor_pelanggan, $bulan, $tahun, $total_tagihan, $metode_pembayaran, $nomor_transaksi)
    {
        $send = new Message();

        $link = 'https://bumdespam.withmangg.my.id';

        // Pesan konfirmasi pembayaran
        $message = "*Konfirmasi Pembayaran Tagihan PDAM*\n\n"
                  ."Halo, *$nama_pelanggan*! 👋\n\n"
                  ."✅ Pembayaran tagihan Anda telah berhasil dibuat!\n\n"
                  ."📋 *Detail Transaksi:*\n"
                  ."• Nomor Pelanggan: *$nomor_pelanggan*\n"
                  ."• Periode: *$bulan - $tahun*\n"
                  ."• Total Tagihan: *Rp " . number_format($total_tagihan, 0, ',', '.') . "*\n"
                  ."• Metode Pembayaran: *$metode_pembayaran*\n"
                  ."• Nomor Transaksi: *$nomor_transaksi*\n\n"
                  ."🔄 Status pembayaran Anda sedang diproses.\n"
                  ."Anda akan menerima notifikasi setelah pembayaran selesai.\n\n"
                  ."🔗 *Cek status pembayaran:* $link \n\n"
                  ."Terima kasih telah melakukan pembayaran! \n\n"
                  ."—\n"
                  ."🔹 *PDAM BUMDES PAGAR SEJAHTERA* 🔹";

        // Mengirim pesan ke WhatsApp melalui Wablas
        $send_text = $send->single_text($phones, $message);
        Log::info("Response konfirmasi pembayaran dari Wablas: " . json_encode($send_text));
    }
    
    public function send_message_local($phones, $nama_pelanggan, $bulan, $tahun)
    {
        // Link untuk cek tagihan
        $link = 'https://bumdespam.withmangg.my.id';
    
        // Pesan WhatsApp
        $message = "*Tagihan PDAM Anda Sudah Tersedia!*\n\n"
                  ."Halo, *$nama_pelanggan*! 👋\n\n"
                  ."📅 Tagihan Anda bulan *$bulan - $tahun* sudah tersedia!\n\n"
                  ."Segera lakukan pembayaran untuk memastikan layanan tetap berjalan lancar.\n"
                  ."Pembayaran dapat dilakukan melalui metode yang tersedia.\n\n"
                  ."🔗 *Cek tagihan dan bayar sekarang:* $link \n\n"
                  ."Terima kasih telah menggunakan layanan kami!\n\n"
                  ."—\n"
                  ."🔹 *PDAM BUMDES PAGAR SEJAHTERA* 🔹";
    
        // Kirim pesan ke Gowa Blast
        $response = Http::withBasicAuth(
            env('GOWA_USERNAME'),
            env('GOWA_PASSWORD')
        )->post(env('GOWA_API_URL'), [
            'phone' => $phones . '@s.whatsapp.net',
            'message' => $message,
            'reply_message_id' => '',
            'is_forwarded' => false,
        ]);
    
        // Logging
        Log::info("Response dari Gowa Blast:", [
            'status' => $response->status(),
            'body' => $response->json()
        ]);
    }
    
    public function send_payment_confirmation_message_local(
        $phones,
        $nama_pelanggan,
        $nomor_pelanggan,
        $bulan,
        $tahun,
        $total_tagihan,
        $metode_pembayaran,
        $nomor_transaksi
    ) {
        // Base link
        $link = 'https://bumdespam.withmangg.my.id';
    
        // Compose message
        $message = "*Konfirmasi Pembayaran Tagihan PDAM*\n\n"
                  ."Halo, *$nama_pelanggan*! 👋\n\n"
                  ."✅ Pembayaran tagihan Anda telah berhasil dibuat!\n\n"
                  ."📋 *Detail Transaksi:*\n"
                  ."• Nomor Pelanggan: *$nomor_pelanggan*\n"
                  ."• Periode: *$bulan - $tahun*\n"
                  ."• Total Tagihan: *Rp " . number_format($total_tagihan, 0, ',', '.') . "*\n"
                  ."• Metode Pembayaran: *$metode_pembayaran*\n"
                  ."• Nomor Transaksi: *$nomor_transaksi*\n\n"
                  ."🔄 Status pembayaran Anda sedang diproses.\n"
                  ."Anda akan menerima notifikasi setelah pembayaran selesai.\n\n"
                  ."🔗 *Cek status pembayaran:* $link\n\n"
                  ."Terima kasih telah melakukan pembayaran!\n\n"
                  ."—\n"
                  ."🔹 *PDAM BUMDES PAGAR SEJAHTERA* 🔹";
    
        // Kirim ke endpoint Gowa Blast
        $response = Http::withBasicAuth(
            env('GOWA_USERNAME'),
            env('GOWA_PASSWORD')
        )->post(env('GOWA_API_URL'), [
            'phone' => $phones . '@s.whatsapp.net',
            'message' => $message,
            'reply_message_id' => '',
            'is_forwarded' => false,
        ]);
    
    }
    
    public function send_otp_server($phone, $otp){
        
        $message = "*Permintaan OTP Reset Password!*\n\n"
                  ."Kode OTP Anda: $otp\n\n"
                  ."Kode ini akan kedaluwarsa dalam 5 menit.\n\n"
                  ."â€”\n"
                  ."ðŸ”¹ *PDAM BUMDES PAGAR SEJAHTERA* ðŸ”¹";

        // Kirim ke endpoint Gowa Blast
        $response = Http::withBasicAuth(
            env('GOWA_USERNAME'),
            env('GOWA_PASSWORD')
        )->post(env('GOWA_API_URL'), [
            'phone' => $phone . '@s.whatsapp.net',
            'message' => $message,
            'reply_message_id' => '',
            'is_forwarded' => false,
        ]);
    }
}
