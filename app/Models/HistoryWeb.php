<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryWeb extends Model
{
    protected $table = 'msriwayat';
    protected $primaryKey = 'riwayatId';

    protected $fillable = [
        'riwayatTable',
        'riwayatAksi',
        'riwayatData',
        'riwayatUserId',
    ];
}
