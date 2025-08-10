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

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'tagihanPelangganId', 'pelangganId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pelangganUserId', 'id');
    }
    
    protected static function boot()
    {
        parent::boot();
    
        // Soft delete semua tagihan saat pelanggan dihapus
        static::deleting(function ($pelanggan) {
            if ($pelanggan->isForceDeleting()) {
                // Jika force delete, hapus permanen tagihan juga
                $pelanggan->tagihan()->withTrashed()->forceDelete();
            } else {
                // Soft delete tagihan
                $pelanggan->tagihan()->each(function ($tagihan) {
                    $tagihan->delete();
                });
            }
        });
    
        // Restore otomatis tagihan saat pelanggan di-restore
        static::restoring(function ($pelanggan) {
            $pelanggan->tagihan()->withTrashed()->get()->each(function ($tagihan) {
                $tagihan->restore();
            });
        });
    }


}

