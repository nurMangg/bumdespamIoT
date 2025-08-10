<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'msdesa';
    protected $primaryKey = 'desaId';
    
    protected $fillable = [
        'desaNama',
    ];
}
