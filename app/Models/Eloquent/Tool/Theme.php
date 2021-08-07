<?php

namespace App\Models\Eloquent\Tool;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected static $theme=[
        "default"=>[
            'name'=>'Default',
            'primaryColor'=>'#3E4551',
        ],
    ];

    public static function getTheme($id){
        if(!isset(self::$theme[$id])){
            return self::$theme['default'];
        }
        return self::$theme[$id];
    }

    public static function getAll(){
        return self::$theme;
    }
}
