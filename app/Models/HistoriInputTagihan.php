<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriInputTagihan extends Model
{
    protected $table = 'historiInputTagihan';
    protected $fillable = ['tagihan_id', 'lapangan_id'];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id', 'tagihanId');
    }

    public function lapangan()
    {
        return $this->belongsTo(User::class, 'lapangan_id', 'id');
    }
}
