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
        $tending_list = DB::table($this->tableName)->where(["public"=>1])->orderBy('create_time', 'desc')->select("gid","gcode","img","name","verified")->limit(12)->get()->all(); //Fake Tending
        foreach($tending_list as &$t) {
            $t["members"]=$this->count_group_members($t["gid"]);
        }
        return $tending_list;
    }

    public function mine_list()
    {
        $mine_list = DB::table("group_member")->join("group","group_member.gid","=","group.gid")->where(["uid"=>Auth::user()->id])->select("group.gid as gid","gcode","img","name","verified")->limit(12)->get()->all();
        foreach($mine_list as &$m) {
            $m["members"]=$this->count_group_members($m["gid"]);
        }
        return $mine_list;
    }

    public function count_group_members($gid){
        return DB::table("group_member")->where(["gid"=>$gid])->count();
    }
}
