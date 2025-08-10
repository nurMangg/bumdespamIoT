<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DuitkuPG;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiDuitkuController extends Controller
{
    public function createInvoice(Request $request)
    {
        Log::info('Duitku Create Invoice Request', ['request' => $request->all()]);
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'tagihan_id' => 'required|exists:tagihans,tagihanId',
                'payment_method' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $duitkuConfig = new \Duitku\Config(env("DUITKU_API_KEY"), env("DUITKU_MERCHANT_KEY"));
            $merchantCode = env("DUITKU_MERCHANT_KEY");
            $apiKey = env("DUITKU_API_KEY");
            Log::info('Duitku Create Invoice', ['merchant_code' => $merchantCode, 'api_key_exists' => !empty($apiKey)]);
            
            // false for production mode
            // true for sandbox mode
            $duitkuConfig->setSandboxMode(true);
            // set sanitizer (default : true)
            $duitkuConfig->setSanitizedMode(true);
            // set log parameter (default : true)
            $duitkuConfig->setDuitkuLogs(true);

            $tagihanId = (string) $request->tagihan_id;
            $tagihan = Tagihan::find($tagihanId);

            if (!$tagihan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan tidak ditemukan'
                ], 404);
            }

            if ($tagihan->tagihanStatus == "Lunas") {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan sudah lunas'
                ], 400);
            }

            $selectedMethod = $request->payment_method;

            // Tentukan metode pembayaran
            // $paymentChannels = [
            //     'BRI DUITKU' => 'BR',
            //     'MANDIRI DUITKU' => 'M2',
            //     'BNI DUITKU' => 'I1',
            //     'BCA DUITKU' => 'BC',
            //     'QRIS DUITKU' => 'SP',
            //     'ALFAMART DUITKU' => 'FT',
            //     'INDOMARET DUITKU' => 'IR',
            // ];

            // // Validasi metode pembayaran
            // if (!array_key_exists($selectedMethod, $paymentChannels)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Metode pembayaran tidak valid'
            //     ], 400);
            // }

            $enabledPayments = $selectedMethod;

            $paymentMethod      = $enabledPayments;
            $paymentAmount      = ($tagihan->tagihanMAkhir - $tagihan->tagihanMAwal) * $tagihan->tagihanInfoTarif + $tagihan->tagihanInfoAbonemen;
            $email              = $tagihan->pelanggan->pelangganEmail ?? ''; // your customer email
            $phoneNumber        = $tagihan->pelanggan->pelangganPhone ?? ''; // your customer phone number (optional)
            $productDetails     = 'Pembayaran Tagihan PDAM BUMDES PAGAR SEJAHTERA';
            $merchantOrderId    = time(); // from merchant, unique   
            $additionalParam    = ''; // optional
            $merchantUserInfo   = ''; // optional
            $customerVaName     = 'PDAM ' . ($tagihan->pelanggan->pelangganNama ?? ''); // display name on bank confirmation display
            $callbackUrl        = 'https://bumdespam.withmangg.my.id/transaksi/handle-notification-duitku'; // url for callback
            $returnUrl          = ''; // url for redirect
            $expiryPeriod       = 720; // set the expired time in minutes
            $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);

            // Customer Detail
            $firstName          = $tagihan->pelanggan->pelangganKode;
            $lastName           = $tagihan->pelanggan->pelangganNama;

            // Address
            $alamat             = "Desa Pagerbarang";
            $city               = "Kab. Tegal";
            $postalCode         = "52462";
            $countryCode        = "ID";

            $address = array(
                'firstName'     => $firstName,
                'lastName'      => $lastName,
                'address'       => $alamat,
                'city'          => $city,
                'postalCode'    => $postalCode,
                'phone'         => $phoneNumber,
                'countryCode'   => $countryCode
            );

            $customerDetail = array(
                'firstName'         => $firstName,
                'lastName'          => $lastName,
                'email'             => $email,
                'phoneNumber'       => $phoneNumber,
                'billingAddress'    => $address,
                'shippingAddress'   => $address
            );

            // Item Details
            $item1 = array(
                'name'      => $productDetails,
                'price'     => $paymentAmount,
                'quantity'  => 1
            );

            $itemDetails = array(
                $item1
            );

            $params = array(
                'paymentAmount'     => $paymentAmount,
                'merchantOrderId'   => $merchantOrderId,
                'productDetails'    => $productDetails,
                'additionalParam'   => $additionalParam,
                'merchantUserInfo'  => $merchantUserInfo,
                'customerVaName'    => $customerVaName,
                'email'             => $email,
                'phoneNumber'       => $phoneNumber,
                'itemDetails'       => $itemDetails,
                'customerDetail'    => $customerDetail,
                'callbackUrl'       => $callbackUrl,
                'returnUrl'         => $returnUrl,
                'expiryPeriod'      => $expiryPeriod,
                'paymentMethod'     => $paymentMethod,
                'signature'         => $signature
            );

            try {
                // createInvoice Request
                $responseDuitkuPop = \Duitku\Pop::createInvoice($params, $duitkuConfig);
                $transactionData = json_decode($responseDuitkuPop, true);
                // Log::info('Duitku Response', $transactionData);
                
                if (!isset($transactionData['statusCode']) || $transactionData['statusCode'] != '00') {
                    Log::error('Duitku Error', $transactionData);
                    return response()->json([
                        'success' => false,
                        'message' => $transactionData['statusMessage'] ?? 'Gagal membuat invoice'
                    ], 500);
                }

                $tagihan->tagihanStatus = "Pending";
                $tagihan->save();
                
                $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihan->tagihanId)->first();
                if ($pembayaran) {
                    $pembayaran->pembayaranMetode = $selectedMethod;
                    $pembayaran->pembayaranAbonemen = $tagihan->tagihanInfoAbonemen;
                    $pembayaran->pembayaranAdminFee = $request->input('pembayaranAdminFee') ?? '0';
                    $pembayaran->save();
                } else {
                    // Create new payment record if it doesn't exist
                    $pembayaran = Pembayaran::create([
                        'pembayaranTagihanId' => $tagihan->tagihanId,
                        'pembayaranMetode' => $selectedMethod,
                        'pembayaranJumlah' => ($tagihan->tagihanMAkhir - $tagihan->tagihanMAwal) * $tagihan->tagihanInfoTarif,
                        'pembayaranAbonemen' => $tagihan->tagihanInfoAbonemen,
                        'pembayaranAdminFee' => $request->input('pembayaranAdminFee') ?? '0',
                        'pembayaranStatus' => 'Pending',
                    ]);
                }

                DuitkuPG::create([
                    'merchant_code_id' => $merchantOrderId,
                    'payment_pembayaranId' => $pembayaran->pembayaranId,
                    'reference' => $transactionData['reference'],
                    'payment_url' => $transactionData['paymentUrl'],
                    'status_code' => $transactionData['statusCode'],
                    'status_message' => $transactionData['statusMessage'],
                ]);
                
                if ($tagihan->pelanggan->pelangganPhone) {
                    // Send payment confirmation message
                    $this->send_payment_confirmation_message_local(
                        $tagihan->pelanggan->pelangganPhone,
                        $tagihan->pelanggan->pelangganNama,
                        $tagihan->pelanggan->pelangganKode,
                        $tagihan->tagihanBulan,
                        $tagihan->tagihanTahun,
                        $paymentAmount,
                        $selectedMethod,
                        $merchantOrderId
                    );
                }

                // Return proper JSON response
                return response()->json([
                    'success' => true,
                    'payment_url' => $transactionData['paymentUrl'],
                    'order_id' => $merchantOrderId,
                    'reference' => $transactionData['reference'],
                    'message' => 'Invoice berhasil dibuat'
                ]);
            } catch (Exception $e) {
                Log::error('Duitku Exception', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat invoice: ' . $e->getMessage()
                ], 500);
            }
        } catch (Exception $e) {
            Log::error('Duitku Controller Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function callback_detail() {
        try {
            // Configure Duitku POP
            $duitkuConfig = new \Duitku\Config(env("DUITKU_API_KEY"), env("DUITKU_MERCHANT_KEY"));
            $duitkuConfig->setSandboxMode(true);

            $callback = \Duitku\Api::callback($duitkuConfig);
            Log::info('Duitku Callback Notification', [$callback]);

            header('Content-Type: application/json');
            $notif = json_decode($callback, true);

            if (!$notif || !isset($notif['merchantOrderId'])) {
                Log::error('Invalid Duitku callback data', ['data' => $callback]);
                return response()->json(['message' => 'Invalid callback data'], 400);
            }

            $duitkuPG = DuitkuPG::where('merchant_code_id', $notif['merchantOrderId'])->first();
            
            if (!$duitkuPG) {
                Log::error('Duitku payment not found', ['merchant_order_id' => $notif['merchantOrderId']]);
                return response()->json(['message' => 'Payment not found'], 404);
            }
            
            $tagihanId = Pembayaran::where('pembayaranId', $duitkuPG->payment_pembayaranId)->value('pembayaranTagihanId');
            $tagihan = Tagihan::findOrFail($tagihanId);

            if ($notif['resultCode'] == "00") {
                $tagihan->update(['tagihanStatus' => 'Lunas', 'tagihanDibayarPadaWaktu' => now()]);
                
                $duitkuPG->update(['status_message' => $notif['statusMessage'], 'payment_success' => now()]);
                $duitkuPG->pembayaran->update(['pembayaranStatus' => 'Lunas']);
                
                Log::info('Payment successful', [
                    'tagihan_id' => $tagihanId,
                    'merchant_order_id' => $notif['merchantOrderId']
                ]);
            } else if ($notif['resultCode'] == "01") {
                $tagihan->update(['tagihanStatus' => 'Belum Lunas']);
                $duitkuPG->update(['status_message' => $notif['statusMessage']]);
                $duitkuPG->pembayaran->update(['pembayaranStatus' => 'Belum Lunas']);
                
                Log::info('Payment failed', [
                    'tagihan_id' => $tagihanId,
                    'merchant_order_id' => $notif['merchantOrderId'],
                    'reason' => $notif['statusMessage'] ?? 'Unknown'
                ]);
            }

            return response()->json(['message' => 'Notification handled'], 200);

        } catch (Exception $e) {
            Log::error('Duitku callback error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Add a new method to check payment status
    public function checkPaymentStatus($tagihanId) {
        try {
            $tagihan = Tagihan::findOrFail($tagihanId);
            
            return response()->json([
                'success' => true,
                'status' => $tagihan->tagihanStatus,
                'message' => $tagihan->tagihanStatus == 'Lunas' 
                    ? 'Pembayaran berhasil' 
                    : ($tagihan->tagihanStatus == 'Pending' 
                        ? 'Pembayaran sedang diproses' 
                        : 'Pembayaran belum dilakukan')
            ]);
        } catch (Exception $e) {
            Log::error('Error checking payment status', ['error' => $e->getMessage(), 'tagihan_id' => $tagihanId]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa status pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelPayment(Request $request, $tagihanId)
{
    
    try {
        // Validate request
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $tagihan = Tagihan::findOrFail($tagihanId);
        
        if ($tagihan->tagihanStatus != 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pembayaran dengan status pending yang dapat dibatalkan'
            ], 400);
        }

        $duitkuPG = DuitkuPG::where('merchant_code_id', $request->order_id)->first();
        
        if (!$duitkuPG) {
            return response()->json([
                'success' => false,
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }

        // Update tagihan status
        $tagihan->tagihanStatus = 'Belum Lunas';
        $tagihan->save();
        
        // Update pembayaran status
        $pembayaran = Pembayaran::where('pembayaranId', $duitkuPG->payment_pembayaranId)->first();
        if ($pembayaran) {
            $pembayaran->pembayaranStatus = 'Belum Lunas';
            $pembayaran->save();
        }
        
        // Update duitku payment record
        $duitkuPG->status_message = 'Dibatalkan oleh pengguna';
        $duitkuPG->save();
        
        Log::info('Payment cancelled', [
            'tagihan_id' => $tagihanId,
            'merchant_order_id' => $request->order_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dibatalkan'
        ]);
    } catch (Exception $e) {
        Log::error('Error cancelling payment', ['error' => $e->getMessage(), 'tagihan_id' => $tagihanId]);
        return response()->json([
            'success' => false,
            'message' => 'Gagal membatalkan pembayaran: ' . $e->getMessage()
        ], 500);
    }
}

public function getPaymentUrl(Request $request, $tagihanId)
{
    try {
        if (!$request->has('order_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Order ID diperlukan'
            ], 400);
        }

        $duitkuPG = DuitkuPG::where('merchant_code_id', $request->order_id)->first();
        
        if (!$duitkuPG) {
            return response()->json([
                'success' => false,
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'payment_url' => $duitkuPG->payment_url,
            'reference' => $duitkuPG->reference,
            'message' => 'URL pembayaran berhasil didapatkan'
        ]);
    } catch (Exception $e) {
        Log::error('Error getting payment URL', ['error' => $e->getMessage(), 'tagihan_id' => $tagihanId]);
        return response()->json([
            'success' => false,
            'message' => 'Gagal mendapatkan URL pembayaran: ' . $e->getMessage()
        ], 500);
    }
}

public function getPendingPayment($tagihanId)
{
    try {
        $tagihan = Tagihan::findOrFail($tagihanId);
        
        if ($tagihan->tagihanStatus != 'Pending') {
            return response()->json([
                'success' => true,
                'has_pending' => false,
                'message' => 'Tidak ada pembayaran pending'
            ]);
        }

        $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihanId)->first();
        
        if (!$pembayaran) {
            return response()->json([
                'success' => true,
                'has_pending' => false,
                'message' => 'Data pembayaran tidak ditemukan'
            ]);
        }

        $duitkuPG = DuitkuPG::where('payment_pembayaranId', $pembayaran->pembayaranId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$duitkuPG) {
            return response()->json([
                'success' => true,
                'has_pending' => false,
                'message' => 'Data pembayaran Duitku tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'has_pending' => true,
            'order_id' => $duitkuPG->merchant_code_id,
            'payment_method' => $pembayaran->pembayaranMetode,
            'payment_url' => $duitkuPG->payment_url,
            'reference' => $duitkuPG->reference,
            'message' => 'Data pembayaran pending berhasil didapatkan'
        ]);
    } catch (Exception $e) {
        Log::error('Error getting pending payment', ['error' => $e->getMessage(), 'tagihan_id' => $tagihanId]);
        return response()->json([
            'success' => false,
            'message' => 'Gagal mendapatkan data pembayaran pending: ' . $e->getMessage()
        ], 500);
    }
}

}
