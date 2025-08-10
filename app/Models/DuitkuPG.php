<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuitkuPG extends Model
{
    protected $table = 'duitkuPayment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'merchant_code_id',
        'reference',
        'payment_url',
        'status_code',
        'status_message',
        'payment_pembayaranId',
        'payment_success',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'payment_pembayaranId');
    }
}
