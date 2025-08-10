<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\Lapangan\AuthApiLapanganController;

use App\Http\Controllers\Api\Lapangan\getDataPelangganController;
use App\Http\Controllers\Api\Lapangan\getLastTagihanController;
use App\Http\Controllers\Api\Lapangan\StoreInputTagihanController;
use App\Http\Controllers\Api\TagihanApiController;
use App\Http\Controllers\Api\ApiDuitkuController;
use App\Http\Controllers\Api\ApiWebhookController;
use App\Http\Controllers\IoT\BluetoothLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/login-lapangan', [AuthApiLapanganController::class, 'login']);


    // Lapangan
    Route::get('/getPelanggan/{id}', [getDataPelangganController::class, 'index']);
    Route::get('/getLastTagihan/{id}', [getLastTagihanController::class, 'index']);
    

    Route::get('transaksi/handle-notification-duitku-api', [ApiDuitkuController::class, 'callback_detail']);
    
    //wa webhook
    Route::post('/wa-webhook', [ApiWebhookController::class, 'webhook']);
    Route::post('/send-log-data', [BluetoothLogController::class, 'store']);
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('pelanggan');
    });

    Route::get('/invoice/generate/{tagihanId}', [InvoiceController::class, 'generateInvoice']);
    Route::get('/invoice/preview/{tagihanId}', [InvoiceController::class, 'previewInvoice']);

    Route::get('/invoice/generate/{tagihanId}', [InvoiceController::class, 'generateInvoice']);
    Route::get('/invoice/preview/{tagihanId}', [InvoiceController::class, 'previewInvoice']);
    
    // Payment status check route
    Route::get('/payment/status/{tagihanId}', [ApiDuitkuController::class, 'checkPaymentStatus']);
    Route::post('/payment/create', [ApiDuitkuController::class, 'createInvoice']);

    Route::get('/payment/status/{tagihanId}', [ApiDuitkuController::class, 'checkPaymentStatus']);
    Route::post('/payment/cancel/{tagihanId}', [ApiDuitkuController::class, 'cancelPayment']);
    Route::get('/payment/url/{tagihanId}', [ApiDuitkuController::class, 'getPaymentUrl']);
    Route::get('/payment/pending/{tagihanId}', [ApiDuitkuController::class, 'getPendingPayment']);

    // Route::get('/user/{id}', [AuthApiController::class, 'getUser']);

    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    Route::get('/tagihan', [TagihanApiController::class, 'index']);
    
    Route::post('/storeInputTagihan', [StoreInputTagihanController::class, 'store']);
    
    Route::get('/bluetooth-logs/{pelangganId}', [BluetoothLogController::class, 'getByPelanggan']);
    Route::get('/bluetooth-stats/{pelangganId}', [BluetoothLogController::class, 'getUsageStats']);

    Route::post('/change-password', [AuthApiController::class, 'changePassword']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
});