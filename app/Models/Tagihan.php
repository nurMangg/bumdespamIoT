<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use SoftDeletes;

    protected $table = 'tagihans';
    protected $primaryKey = 'tagihanId';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'tagihanKode',
        'tagihanPelangganId',
        'tagihanBulan',
        'tagihanTahun',
        'tagihanInfoTarif',
        'tagihanInfoAbonemen',
        'tagihanMAwal',
        'tagihanMAkhir',
        'tagihanUserId',
        'tagihanTanggal',
        'tagihanStatus',
        'tagihanCatatan',
        'tagihanDibayarPadaWaktu'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'tagihanPelangganId', 'pelangganId');
    }

    public function pembayaranInfo()
    {
        return $this->hasOne(Pembayaran::class, 'pembayaranTagihanId', 'tagihanId');
    }

    public function bulan()
    {
        return $this->belongsTo(Bulan::class, 'tagihanBulan', 'bulanId');
    }


}
