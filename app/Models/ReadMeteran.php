<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadMeteran extends Model
{
    protected $table = 'readMeteran';
    protected $primaryKey = 'readMeteranId';

    protected $fillable = [
        'readMeteranDeviceId',
        'readMeteranPelangganKode',
        'readMeteranWaterUsage',
        'readMeteranReadingDate',
    ];
}
