<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'msmenu';
    protected $primaryKey = 'menuId';

    public $fillable = [
        'menuName',
        'menuRoute',
        'menuParentId',
        'menuOrder',
        'menuIcon',
    ];
    
    public function menuParent()
    {
        return $this->belongsTo(Menu::class, 'menuParentId');
    }
}
