<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'msrole';
    protected $primaryKey = 'roleId';

    protected $fillable = [
        'roleName',
        'roleMenuId'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->hasMany(User::class, 'userRoleId', 'roleId');
    }
}
