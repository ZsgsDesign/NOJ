<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class GroupModel extends Model
{
    protected $tableName = 'group';

    public function tending_list()
    {
        $tending_list = DB::table($this->tableName)->orderBy('create_time', 'desc')->limit(10)->get(); //Fake Tending
        return $tending_list;
    }

    public function mine_list()
    {
        return DB::table("group_member")->where(["uid"=>Auth::user()->id])->limit(10)->get();
    }
}
