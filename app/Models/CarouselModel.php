<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CarouselModel extends Model
{
    public static function list()
    {
        return DB::table('carousel')->where(["available"=>1])->get()->all();
    }
}
