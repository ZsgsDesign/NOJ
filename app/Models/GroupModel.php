<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroupModel extends Model
{
    protected $tableName = 'group';
    public $role = ["Pending","Member","Manager","Leader"];
    public $role_color = ["wemd-red","wemd-grey","wemd-light-blue","wemd-amber"];

    public function tending_groups()
    {
        $tending_groups = DB::table($this->tableName)->where(["public"=>1])->orderBy('create_time', 'desc')->select("gid","gcode","img","name","verified")->limit(12)->get()->all(); //Fake Tending
        foreach($tending_groups as &$t) {
            $t["members"]=$this->count_group_members($t["gid"]);
        }
        return $tending_groups;
    }

    public function user_groups($uid)
    {
        $user_groups = DB::table("group_member")->join("group","group_member.gid","=","group.gid")->where(["uid"=>$uid])->select("group.gid as gid","gcode","img","name","verified")->limit(12)->get()->all();
        foreach($user_groups as &$m) {
            $m["members"]=$this->count_group_members($m["gid"]);
        }
        return $user_groups;
    }

    public function count_group_members($gid){
        return DB::table("group_member")->where(["gid"=>$gid])->count();
    }

    public function get_group_tags($gid){
        return DB::table("group_tag")->where(["gid"=>$gid])->select("tag")->get()->all();
    }

    public function details($gcode){
        $basic_info = DB::table($this->tableName)->where(["gcode"=>$gcode])->first();
        $basic_info["members"]=$this->count_group_members($basic_info["gid"]);
        $basic_info["tags"]=$this->get_group_tags($basic_info["gid"]);
        $basic_info["create_time_foramt"]=date_format(date_create($basic_info["create_time"]),'M jS, Y');
        return $basic_info;
    }

    public function user_profile($uid,$gid){
        $info=DB::table("group_member")->where(["gid"=>$gid,"uid"=>$uid])->first();
        $info["role_parsed"]=$this->role[$info["role"]];
        return $info;
    }

    public function user_list($gid){
        $user_list = DB::table("group_member")->where(["gid"=>$gid])->orderBy('role', 'desc')->get()->all();
        foreach($user_list as &$u){
            $u["role_parsed"]=$this->role[$u["role"]];
            $u["role_color"]=$this->role_color[$u["role"]];
        }
        return $user_list;
    }
}
