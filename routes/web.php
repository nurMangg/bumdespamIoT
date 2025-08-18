<?php

use App\Http\Controllers\Dashboard\ApexChartController;
use App\Http\Controllers\Data\AksiTagihanController;
use App\Http\Controllers\Data\CekTagihanController;
use App\Http\Controllers\Data\InputTagihanController;
use App\Http\Controllers\Data\TagihanController;
use App\Http\Controllers\Import\ImportDataController;
use App\Http\Controllers\Import\ImportPelangganController;
use App\Http\Controllers\Import\ImportPenggunaController;
use App\Http\Controllers\IoT\IoTController;
use App\Http\Controllers\IoT\InputTagihanFromBLController;
use App\Http\Controllers\Laporan\LaporanPenggunaController;
use App\Http\Controllers\Laporan\LaporanTagihanController;
use App\Http\Controllers\Laporan\LaporanTransaksiByKasirController;
use App\Http\Controllers\Laporan\LaporanTransaksiController;
use App\Http\Controllers\Layanan\AksiTransaksiController;
use App\Http\Controllers\Layanan\DuitkuController;
use App\Http\Controllers\Layanan\KonfirmasiTFController;
use App\Http\Controllers\Layanan\MidtransController;
use App\Http\Controllers\Layanan\TFManualController;
use App\Http\Controllers\Layanan\TransaksiController;
use App\Http\Controllers\Master\GolonganController;
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\TahunController;
use App\Http\Controllers\Notifikasi\NotifikasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Setting\MenuController;
use App\Http\Controllers\Setting\PenggunaAplikasiController;
use App\Http\Controllers\Setting\ResetPasswordController;
use App\Http\Controllers\Setting\RiwayatController;
use App\Http\Controllers\Setting\RoleController;
use App\Http\Controllers\Setting\SettingPenggunaController;
use App\Http\Controllers\Setting\WebController;
use App\Models\Roles;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    if (Auth::user()->userRoleId == Roles::where('roleName', 'pelanggan')->first()->roleId) {
        return view('pelanggan.dashboard');
    } else if (Auth::user()->userRoleId == Roles::where('roleName', 'kasir')->first()->roleId) {
        return view('dashboard-kasir');
    // } else if (Auth::user()->userRoleId == Roles::where('roleName', 'lapangan')->first()->roleId) {
    //     return view('dashboard-lapangan');
    } else {
        return view('dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard-lapangan', function () {
    return view('dashboard-lapangan');
})->middleware(['auth', 'verified'])->name('dashboard.lapangan');

Route::get('/dashboard-iot',[IoTController::class, 'index'] )->middleware(['auth', 'verified'])->name('dashboard.iot');
Route::resource('/input-tagihan-iot', InputTagihanFromBLController::class);
Route::post('/input-tagihan-iot/bulk-update', [InputTagihanFromBLController::class, 'bulkUpdate'])->name('input-tagihan-iot.bulk-update');
Route::get('/api/getWaterUsageChart', [IoTController::class, 'getWaterUsageChart'])->name('api.getWaterUsageChart');
Route::get('/api/getWaterUsageChartNotNull', [IoTController::class, 'getWaterUsageChartNotNull'])->name('api.getWaterUsageChartNotNull');

Route::get('/api/getPelanggan', [IoTController::class, 'getPelanggan'])->name('api.getPelanggan');
Route::get('/api/getWaterUsageSummary', [IoTController::class, 'getWaterUsageSummary'])->name('api.getWaterUsageSummary');
Route::post('transaksi/handle-notification', [MidtransController::class, 'handleNotification'])->name('transaksi.handleNotification')->withoutMiddleware(['auth', 'verified']);
Route::post('transaksi/handle-notification-duitku', [DuitkuController::class, 'callback_detail'])->name('transaksi.handleNotificationduitku')->withoutMiddleware(['auth', 'verified']);
//log-viewers
Route::get('log-viewers', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('/tagihan/kode-pelanggan', [CekTagihanController::class, 'getTagihanByKodePelanggan']);
Route::get('/cek-tagihan', [CekTagihanController::class, 'index']);
Route::get('notifikasi/index', [NotifikasiController::class, 'index'])->name('notifikasi.index');

Route::get('notifikasi/send-notifikasi', [NotifikasiController::class, 'send_message'])->name('notifikasi.send_message');
Route::get('notifikasi/get-report', [NotifikasiController::class, 'report'])->name('notifikasi.report');



Route::middleware(['auth', 'CheckUserRole'])->prefix('master')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('pelanggan/cetakKartu/', [PelangganController::class, 'cetakKartu'])->name('pelanggan.cetakKartu');
    Route::get('pelanggan/ViewKartu/{pelanggan}', [PelangganController::class, 'viewKartu'])->name('pelanggan.viewKartu');
    Route::get('pelanggan/search', [PelangganController::class, 'search'])->name('pelanggan.search');
    Route::resource('pelanggan', PelangganController::class);

    Route::resource('golongan-tarif', GolonganController::class);
    Route::resource('tahun', TahunController::class);
});

Route::middleware(['auth', 'CheckUserRole'])->prefix('layanan')->group(function () {

   Route::get('tagihan/getInfoTagihan', [TagihanController::class, 'getInfoTagihan'])->name('tagihan.getInfoTagihan');
   Route::resource('tagihan', TagihanController::class);
   Route::resource('aksi-tagihan', AksiTagihanController::class);
   Route::resource('transaksi/konfirmasi-transaksi-manual', KonfirmasiTFController::class);

   Route::post('transaksi/konfirmasi-transaksi/konfirmasi', [KonfirmasiTFController::class, 'konfirmasiTransaksi'])->name('konfirmasi-transaksi-manual.konfirmasiTransaksi');

   Route::get('tagihan/aksi-tagihan/kirim-peringatan/{id}', [AksiTagihanController::class, 'kirimPeringatan'])->name('tagihan.aksi-tagihan.kirim-peringatan');

   Route::get('transaksi/getInfoAllTrx', [TransaksiController::class, 'getInfoAllTransaksi'])->name('transaksi.getInfoAllTransaksi');
   Route::get('transaksi/getInfoAllTrxManual', [KonfirmasiTFController::class, 'getInfoAllTransaksiManual'])->name('konfirmasi-transaksi-manual.getInfoAllTrxManual');

   Route::resource('transaksi', TransaksiController::class);
   Route::resource('aksi-transaksi', AksiTransaksiController::class);

   Route::Post('transaksi/pembayaran-tunai', [AksiTransaksiController::class, 'pembayaranTunai'])->name('transaksi.pembayarantunai');
   Route::get('transaksi/unduh-struk/{id}', [TransaksiController::class, 'unduhStruk'])->name('transaksi.struk');


   Route::Post('transaksi/create-snap-token', [MidtransController::class, 'createSnapToken'])->name('transaksi.createsnaptoken');
//    Route::Post('transaksi/update-database', [MidtransController::class, 'updateDatabase'])->name('transaksi.updateDatabase');

   Route::Post('transaksi/create-invoice-duitku', [DuitkuController::class, 'createInvoice'])->name('create-invoice-duitku.createInvoice');

   Route::post('transaksi/tfmanual/store', [TFManualController::class, 'store'])->name('transaksi.tfmanual.store');
   Route::Post('transaksi/tfmanual/cekPayManual', [TFManualController::class, 'cekPayManual'])->name('transaksi.tfmanual.cekPayManual');




});

Route::get('input-tagihan/list-tagihan', [InputTagihanController::class, 'listTagihan'])->name('input-tagihan.listTagihan');
Route::resource('input-tagihan', InputTagihanController::class);
Route::post('input-tagihan/scanqrcode', [InputTagihanController::class, 'scanQRCode'])->name('input-tagihan.scanqrcode');

// Route::middleware(['auth', 'CheckUserRole'])->prefix('input-tagihan')->group(function () {
//     Route::resource('input-tagihan', InputTagihanController::class);
// });

Route::middleware(['auth', 'CheckUserRole'])->prefix('laporan')->group(function () {
    Route::get('laporan-pelanggan', [LaporanPenggunaController::class, 'index'])->name('laporan-pelanggan.index');
    Route::post('laporan-pelanggan/export-pdf', [LaporanPenggunaController::class, 'exportPdf'])->name('laporan-pelanggan.exportPdf');

    Route::get('laporan-tagihan', [LaporanTagihanController::class, 'index'])->name('laporan-tagihan.index');
    Route::post('laporan-tagihan/export-pdf', [LaporanTagihanController::class, 'exportPdf'])->name('laporan-tagihan.exportPdf');

    Route::get('laporan-tagihan/export-excel', [LaporanTagihanController::class, 'exportExcel'])->name('laporan-tagihan.exportExcel');
    Route::get('laporan-pelanggan/export-excel', [LaporanPenggunaController::class, 'exportExcel'])->name('laporan-pelanggan.exportExcel');
    Route::get('laporan-transaksi/export-excel', [LaporanTransaksiController::class, 'exportExcel'])->name('laporan-transaksi.exportExcel');
    Route::get('laporan-transaksi-by-kasir/export-excel', [LaporanTransaksiByKasirController::class, 'exportExcel'])->name('laporan-transaksi-by-kasir.exportExcel');


    Route::get('laporan-transaksi', [LaporanTransaksiController::class, 'index'])->name('laporan-transaksi.index');
    Route::post('laporan-transaksi/export-pdf', [LaporanTransaksiController::class, 'exportPdf'])->name('laporan-transaksi.exportPdf');

    Route::get('laporan-transaksi-by-kasir', [LaporanTransaksiByKasirController::class, 'index'])->name('laporan-transaksi-by-kasir.index');
    Route::post('laporan-transaksi-by-kasir/export-pdf', [LaporanTransaksiByKasirController::class, 'exportPdf'])->name('laporan-transaksi-by-kasir.exportPdf');
});

Route::middleware(['auth', 'CheckUserRole'])->prefix('import')->group(function () {
    Route::get('import-pelanggan', [ImportPelangganController::class, 'index'])->name('import-pelanggan.index');
    Route::Post('import-pelanggan/store', [ImportPelangganController::class, 'store'])->name('import-pelanggan.store');

    Route::get('import-data-tagihan', [ImportDataController::class, 'index'])->name('import-data-tagihan.index');
    Route::Post('import-data-tagihan/store', [ImportDataController::class, 'store'])->name('import-data-tagihan.store');
});

Route::middleware(['auth', 'CheckUserRole'])->group(function () {
    Route::get('/api/tagihan-apex-chart', [ApexChartController::class, 'tagihanApexChart'])->name('api.tagihan-apex-chart');

    Route::get('/tagihan/{id}/detail', [AksiTransaksiController::class, 'showfromkasir'])->name('kasir-tagihan.showfromkasir');
});


Route::middleware(['auth', 'CheckUserRole'])->prefix('setting')->group(function () {
    Route::resource('pengguna-aplikasi', PenggunaAplikasiController::class);
    Route::resource('menu-aplikasi', MenuController::class);
    Route::resource('role-aplikasi', RoleController::class);
    Route::resource('setting-pengguna', SettingPenggunaController::class);

    Route::resource('setting-web', WebController::class);
    Route::get('riwayat-website', [RiwayatController::class, 'index'])->name('riwayat-website.index');

    Route::get('reset-password', [ResetPasswordController::class, 'index'])->name('reset-password.index');
    Route::post('reset-password/{id}', [ResetPasswordController::class, 'resetPassword'])->name('reset-password.resetPassword');
});

Route::get('/check-opcache', function () {
    if (function_exists('opcache_get_status')) {
        return response()->json(opcache_get_status());
    } else {
        return response()->json(['error' => 'OPcache tidak tersedia'], 500);
    }
});

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return response()->json(['success' => 'Cache berhasil dihapus. Website akan berjalan lebih cepat']);
})->middleware(['auth', 'CheckUserRole'])->name('clear');

Route::get('/route-clear', function () {
    Artisan::call('route:clear');
    return response()->json(['success' => 'Cache berhasil dihapus. Website akan berjalan lebih cepat']);
})->middleware(['auth', 'CheckUserRole'])->name('route-clear');

Route::get('/queue-table', function () {
    Artisan::call('queue:table');
    Artisan::call('migrate');
    return response()->json(['success' => 'Cache berhasil dihapus. Website akan berjalan lebih cepat']);
})->middleware(['auth', 'CheckUserRole'])->name('queue-clear');


Route::fallback(function () {
    if (app()->environment('production')) {
        return response()->view('errors.404', [], 404);
    } else {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }
});


require __DIR__.'/auth.php';
