<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\DuitkuPG;
use App\Models\MidtransPayment;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class DuitkuController extends Controller
{
    public function createInvoice(Request $request)
    {
        $duitkuConfig = new \Duitku\Config(env("DUITKU_API_KEY"), env("DUITKU_MERCHANT_KEY"));
        $merchantCode = env("DUITKU_MERCHANT_KEY");
        $apiKey = env("DUITKU_API_KEY");
        Log::info('Duitku Create Invoice', [$merchantCode, $apiKey]);
        // dd($merchantCode, $apiKey);
        // false for production mode
        // true for sandbox mode
        $duitkuConfig->setSandboxMode(true);
        // set sanitizer (default : true)
        $duitkuConfig->setSanitizedMode(true);
        // set log parameter (default : true)
        $duitkuConfig->setDuitkuLogs(true);

        $tagihanDecrypt = Crypt::decryptString($request->tagihanId);
        $tagihan = Tagihan::find($tagihanDecrypt);

        if($tagihan->tagihanStatus == "Lunas")
        {
            return response()->json(['error' => "SUdah ada"], 500);
        }else{
            $selectedMethod = $request->input('paymentMethod');

            // Tentukan metode pembayaran
            $paymentChannels = [
                'BRI DUITKU' => 'BR',
                'MANDIRI DUITKU' => 'M2',
                'BNI DUITKU' => 'I1',
                'BCA DUITKU' => 'BC',
                'QRIS DUITKU' => 'SP',
                'ALFAMART DUITKU' => 'FT',
                'INDOMARET DUITKU' => 'IR',
            ];

            // Validasi metode pembayaran
            if (!array_key_exists($selectedMethod, $paymentChannels)) {
                return response()->json(['error' => "Metode pembayaran tidak valid"], 400);
            }

            $enabledPayments = $paymentChannels[$selectedMethod];

            $paymentMethod      = $enabledPayments;
            // $paymentMethod      = ""; // PaymentMethod list => https://docs.duitku.com/pop/id/#payment-method
            $paymentAmount      = ($tagihan->tagihanMAkhir - $tagihan->tagihanMAwal) * $tagihan->tagihanInfoTarif + $tagihan->tagihanInfoAbonemen;
            $email              = ''; // your customer email
            $phoneNumber        = $tagihan->pelanggan->pelangganPhone ?? ''; // your customer phone number (optional)
            $productDetails     = 'Pembayaran Tagihan PDAM BUMDES PAGAR SEJAHTERA';
            $merchantOrderId    = time(); // from merchant, unique   
            $additionalParam    = ''; // optional
            $merchantUserInfo   = ''; // optional
            $customerVaName     = 'PDAM ' . $tagihan->pelanggan->pelangganNama ?? ''; // display name on bank confirmation display
            $callbackUrl        = 'https://bumdespam.withmangg.my.id/transaksi/handle-notification-duitku'; // url for callback
            $returnUrl          = 'https://bumdespam.withmangg.my.id/layanan/transaksi'; // url for redirect
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
                'paymentMethod' => $paymentMethod,
                'signature' => $signature
            );

            try {
                // createInvoice Request
                $responseDuitkuPop = \Duitku\Pop::createInvoice($params, $duitkuConfig);

                $transactionData = json_decode($responseDuitkuPop, true);

                $tagihan->tagihanStatus = "Pending";
                $tagihan->save();
                
                $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihan->tagihanId)->first();
                if ($pembayaran) {
                    $pembayaran->pembayaranMetode = $selectedMethod;
                    $pembayaran->pembayaranAbonemen = $tagihan->tagihanInfoAbonemen;;
                    $pembayaran->pembayaranAdminFee = $request->input('pembayaranAdminFee') ?? '0';
                    $pembayaran->save();
                }

                DuitkuPG::create([
                    'merchant_code_id' => $merchantOrderId,
                    'payment_pembayaranId' => $pembayaran->pembayaranId,
                    'reference' => $transactionData['reference'],
                    'payment_url' => $transactionData['paymentUrl'],
                    'status_code' => $transactionData['statusCode'],
                    'status_message' => $transactionData['statusMessage'],
                ]);

                header('Content-Type: application/json');
                echo $responseDuitkuPop;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
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


            $duitkuPG = DuitkuPG::where('merchant_code_id', $notif['merchantOrderId'])->first();
            $tagihanId = Pembayaran::where('pembayaranId', $duitkuPG->payment_pembayaranId)->value('pembayaranTagihanId');
            $tagihan = Tagihan::findOrFail($tagihanId);

            if ($notif['resultCode'] == "00") {
                $tagihan->update(['tagihanStatus' => 'Lunas', 'tagihanDibayarPadaWaktu' => now()]);
                
                $duitkuPG->update(['status_message' => $notif['statusMessage'], 'payment_success' => now()]);
                $duitkuPG->pembayaran->update(['pembayaranStatus' => 'Lunas']);
            } else if ($notif['resultCode'] == "01") {
                $tagihan->update(['tagihanStatus' => 'Belum Lunas']);
                $duitkuPG->update(['status_message' => $notif['statusMessage']]);
                $duitkuPG->pembayaran->update(['pembayaranStatus' => 'Belum Lunas']);
            }

            return response()->json(['message' => 'Notification handled'], 200);

        } catch (Exception $e) {
            http_response_code(400);
            echo $e->getMessage();
        }
    }


}
