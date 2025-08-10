<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tahun extends Model
{
    protected $table = 'mstahun';
    protected $primaryKey = 'tahunId';

    protected $fillable = [
        'tahun',
        'tahunStatus'
    ];
}
