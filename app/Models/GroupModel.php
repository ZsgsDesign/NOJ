<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroupModel extends Model
{
    protected $tableName = 'group';
    public $role = ["Pending","Member","Manager","Leader"];
    public $role_color = ["wemd-red","wemd-grey","wemd-light-blue","wemd-amber"];

    public function tendingGroups()
    {
        $tending_groups = DB::table($this->tableName)->where(["public"=>1])->orderBy('create_time', 'desc')->select("gid", "gcode", "img", "name", "verified")->limit(12)->get()->all(); //Fake Tending
        foreach ($tending_groups as &$t) {
            $t["members"]=$this->countGroupMembers($t["gid"]);
        }
        return $tending_groups;
    }

    public function userGroups($uid)
    {
        $user_groups = DB::table("group_member")->join("group", "group_member.gid", "=", "group.gid")->where(["uid"=>$uid])->select("group.gid as gid", "gcode", "img", "name", "verified")->limit(12)->get()->all();
        foreach ($user_groups as &$m) {
            $m["members"]=$this->countGroupMembers($m["gid"]);
        }
        return $user_groups;
    }

    public function countGroupMembers($gid)
    {
        return DB::table("group_member")->where(["gid"=>$gid])->count();
    }

    public function getGroupTags($gid)
    {
        return DB::table("group_tag")->where(["gid"=>$gid])->select("tag")->get()->all();
    }

    public function details($gcode)
    {
        $basic_info = DB::table($this->tableName)->where(["gcode"=>$gcode])->first();
        $basic_info["members"]=$this->countGroupMembers($basic_info["gid"]);
        $basic_info["tags"]=$this->getGroupTags($basic_info["gid"]);
        $basic_info["create_time_foramt"]=date_format(date_create($basic_info["create_time"]), 'M jS, Y');
        return $basic_info;
    }

    public function userProfile($uid, $gid)
    {
        $info=DB::table("group_member")->where(["gid"=>$gid,"uid"=>$uid])->first();
        $info["role_parsed"]=$this->role[$info["role"]];
        return $info;
    }

    public function userList($gid)
    {
        $user_list = DB::table("group_member")->join(
            "users",
            "users.id",
            "=",
            "group_member.uid"
        )->where(["gid"=>$gid])->orderBy('role', 'desc')->select(
            "role",
            "uid",
            "name",
            "nick_name",
            "avatar"
        )->get()->all();
        foreach ($user_list as &$u) {
            $u["role_parsed"]=$this->role[$u["role"]];
            $u["role_color"]=$this->role_color[$u["role"]];
        }
        return $user_list;
    }

    public function groupNotice($gid)
    {
        $notice_item = DB::table("group_notice")->where(["gid"=>$gid])->first();
        if (empty($notice_item)) {
            return [];
        }
        $notice_author = DB::table("users")->where(["id"=>$notice_item["uid"]])->first();
        $notice_item["name"]=$notice_author["name"];
        $notice_item["avatar"]=$notice_author["avatar"];
        $notice_item["post_date_parsed"]=$this->formatPostTime($notice_item["post_date"]);
        $notice_item["content_parsed"]=clean(Markdown::convertToHtml($notice_item["content"]));
        return $notice_item;
    }

    public function formatPostTime($date)
    {
        $periods = ["second", "minute", "hour", "day", "week", "month", "year", "decade"];
        $lengths = ["60","60","24","7","4.35","12","10"];

        $now = time();
        $unix_date = strtotime($date);

        if (empty($unix_date)) {
            return "Bad date";
        }

        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            $tense = "ago";
        } else {
            $difference = $unix_date - $now;
            $tense = "from now";
        }

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if ($difference != 1) {
            $periods[$j].= "s";
        }

        return "$difference $periods[$j] {$tense}";
    }
}
