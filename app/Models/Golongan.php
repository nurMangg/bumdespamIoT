<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    protected $table = 'msgolongan';

    protected $primaryKey = 'golonganId';

    protected $fillable = [
        'golonganNama',
        'golonganTarif',
        'golonganAbonemen',
        'golonganStatus',
    ];

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class, 'pelangganGolonganId', 'golonganId');
    }
}
