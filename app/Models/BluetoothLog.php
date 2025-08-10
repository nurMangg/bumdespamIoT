<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BluetoothLog extends Model
{
    protected $table = 'bluetooth_logs';

    protected $fillable = [
        'pelanggan_id',
        'datetime',
        'total',
        'volume_m3',
        'created_at'
    ];

    protected $casts = [
        'total' => 'decimal:3',
        'volume_m3' => 'decimal:6'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelangganId');
    }
}