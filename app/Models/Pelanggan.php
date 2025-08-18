<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dates = ['deleted_at'];
    protected $table = 'mspelanggan';

    protected $primaryKey = 'pelangganId';
    protected $fillable = [
        'pelangganKode',
        'pelangganNama',
        'pelangganPhone',
        'pelangganDesa',
        'pelangganRt',
        'pelangganRw',
        'pelangganGolonganId',
        'pelangganStatus',
        'pelangganUserId',
    ];

    public function golongan()
    {
        return $this->belongsTo(Golongan::class, 'pelangganGolonganId', 'golonganId');
    }

    public function tagihanTerakhir()
    {
        return $this->hasOne(Tagihan::class, 'tagihanPelangganId', 'pelangganId')
                    ->latestOfMany('tagihanId');
    }

    public function tagihanBelumLunas()
    {
        return $this->hasMany(Tagihan::class, 'tagihanPelangganId', 'pelangganId')
                    ->whereIn('tagihanStatus', ['Belum Lunas', 'Pending']);
    }


    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'tagihanPelangganId', 'pelangganId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pelangganUserId', 'id');
    }

}

