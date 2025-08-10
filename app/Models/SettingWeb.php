<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingWeb extends Model
{
    protected $table = 'mssetting_web';
    protected $primaryKey = 'id';

    protected $fillable = [
        'settingWebNama',
        'settingWebLogo',
        'settingWebLogoLandscape',
        'settingWebAlamat',
        'settingWebEmail',
        'settingWebPhone',
    ];
}
