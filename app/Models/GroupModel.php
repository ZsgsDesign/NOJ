<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroupModel extends Model
{
    protected $tableName='group';
    protected $table='group';
    protected $primaryKey='gid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    /*
        join_policy:
            1:只能通过邀请加入 
            2:只能通过申请加入 
            3:申请与邀请均可加入
    */
    public $role=[
        "-3"=>"None",
        "-1"=>"Invited",
        "0"=>"Pending",
        "1"=>"Member",
        "2"=>"Manager",
        "3"=>"Leader"
    ];
    public $role_color=[
        "-3"=>"wemd-black",
        "-1"=>"wemd-deep-purple",
        "0"=>"wemd-red",
        "1"=>"wemd-grey",
        "2"=>"wemd-light-blue",
        "3"=>"wemd-amber"
    ];

    public function tendingGroups()
    {
        $tending_groups=DB::table($this->tableName)->where(["public"=>1])->orderBy('create_time', 'desc')->select("gid", "gcode", "img", "name", "verified")->limit(12)->get()->all(); //Fake Tending
        foreach ($tending_groups as &$t) {
            $t["members"]=$this->countGroupMembers($t["gid"]);
        }
        return $tending_groups;
    }

    public function userGroups($uid)
    {
        $user_groups=DB::table("group_member")->join("group", "group_member.gid", "=", "group.gid")->where(["uid"=>$uid])->select("group.gid as gid", "gcode", "img", "name", "verified")->limit(12)->get()->all();
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

    public function countGroupContest($gid)
    {
        return [
            "contest_ahead" => DB::table("contest")->where(["gid"=>$gid])->where("begin_time", ">", DB::raw("now()"))->count(),
            "contest_going" => DB::table("contest")->where(["gid"=>$gid])->where("begin_time", "<=", DB::raw("now()"))->where("end_time", ">=", DB::raw("now()"))->count(),
            "contest_end" => DB::table("contest")->where(["gid"=>$gid])->where("end_time", "<", DB::raw("now()"))->count()
        ];
    }

    public function changeNickName($gid, $uid, $nickName)
    {
        return DB::table("group_member")->where(["gid"=>$gid, "uid"=>$uid])->update([
            "nick_name"=>$nickName
        ]);
    }

    public function changeGroupName($gid, $GroupName)
    {
        return DB::table("group")->where('gid',$gid)->update([
            "name"=>$GroupName
        ]);
    }

    public function changeJoinPolicy($gid, $JoinPolicy){
        return DB::table("group")->where('gid',$gid)->update([
            "join_policy"=>$JoinPolicy
        ]);
    }

    public function details($gcode)
    {
        $basic_info=DB::table($this->tableName)->where(["gcode"=>$gcode])->first();
        if(empty($basic_info)) return [];
        $basic_info["members"]=$this->countGroupMembers($basic_info["gid"]);
        $basic_info["tags"]=$this->getGroupTags($basic_info["gid"]);
        $basic_info["create_time_foramt"]=date_format(date_create($basic_info["create_time"]), 'M jS, Y');
        $basic_info["contest_stat"]=$this->countGroupContest($basic_info["gid"]);
        return $basic_info;
    }

    public function joinPolicy($gid)
    {
        $ret=DB::table($this->tableName)->where(["gid"=>$gid])->first();
        return empty($ret) ? null : $ret["join_policy"];
    }

    public function userProfile($uid, $gid)
    {
        $info=DB::table("group_member")->where(["gid"=>$gid, "uid"=>$uid])->where("role", ">", 0)->first();
        if (!empty($info)) {
            $info["role_parsed"]=$this->role[$info["role"]];
            $info["role_color"]=$this->role_color[$info["role"]];
        }
        return $info;
    }

    public function userList($gid)
    {
        $user_list=DB::table("group_member")->join(
            "users",
            "users.id",
            "=",
            "group_member.uid"
        )->where(["gid"=>$gid])->orderBy('role', 'desc')->select(
            "role",
            "uid",
            "name",
            "nick_name",
            "avatar",
            "sub_group"
        )->get()->all();
        foreach ($user_list as &$u) {
            $u["role_parsed"]=$this->role[$u["role"]];
            $u["role_color"]=$this->role_color[$u["role"]];
            if(is_null($u["sub_group"])) $u["sub_group"]="None";
        }
        return $user_list;
    }

    public function groupNotice($gid)
    {
        $notice_item=DB::table("group_notice")->where(["gid"=>$gid])->first();
        if (empty($notice_item)) {
            return [];
        }
        $notice_author=DB::table("users")->where(["id"=>$notice_item["uid"]])->first();
        $notice_item["name"]=$notice_author["name"];
        $notice_item["avatar"]=$notice_author["avatar"];
        $notice_item["post_date_parsed"]=$this->formatPostTime($notice_item["post_date"]);
        $notice_item["content_parsed"]=clean(Markdown::convertToHtml($notice_item["content"]));
        return $notice_item;
    }

    public function judgeClearance($gid, $uid)
    {
        $ret=DB::table("group_member")->where(["gid"=>$gid, "uid"=>$uid])->first();
        return empty($ret) ? -3 : $ret["role"];
    }

    public function changeClearance($uid, $gid, $clearance)
    {
        return DB::table("group_member")->where([
            "uid"=>$uid,
            "gid"=>$gid
        ])->update([
            "role"=>$clearance
        ]);
    }

    public function removeClearance($uid, $gid)
    {
        return DB::table("group_member")->where([
            "uid"=>$uid,
            "gid"=>$gid
        ])->delete();
    }

    public function addClearance($uid, $gid, $clearance)
    {
        return DB::table("group_member")->insert([
            "uid"=>$uid,
            "gid"=>$gid,
            "role"=>$clearance,
            "join_time"=>date("Y-m-d H:i:s")
        ]);
    }

    public function isMember($gid, $uid)
    {
        return DB::table("group_member")->where([
            "gid"=> $gid,
            "uid"=> $uid
        ])->where("role", ">", 0)->count();
    }

    public function formatPostTime($date)
    {
        $periods=["second", "minute", "hour", "day", "week", "month", "year", "decade"];
        $lengths=["60", "60", "24", "7", "4.35", "12", "10"];

        $now=time();
        $unix_date=strtotime($date);

        if (empty($unix_date)) {
            return "Bad date";
        }

        if ($now>$unix_date) {
            $difference=$now-$unix_date;
            $tense="ago";
        } else {
            $difference=$unix_date-$now;
            $tense="from now";
        }

        for ($j=0; $difference>=$lengths[$j] && $j<count($lengths)-1; $j++) {
            $difference/=$lengths[$j];
        }

        $difference=round($difference);

        if ($difference!=1) {
            $periods[$j].="s";
        }

        return "$difference $periods[$j] {$tense}";
    }

        
    public function judgeEmailClearance($gid, $email)
    {
        $uid=DB::table("users")->where(["email"=>$email])->first();
        if(empty($uid)) return -4;
        $ret=DB::table("group_member")->where([
            "gid"=>$gid,
            "uid"=>$uid["id"],
        ])->first();
        return empty($ret) ? -3 : $ret["role"];
    }

    public function inviteMember($gid, $email)
    {
        $uid=DB::table("users")->where(["email"=>$email])->first();
        return DB::table("group_member")->insert([
            "uid"=>$uid["id"],
            "gid"=>$gid,
            "role"=>-1,
            "join_time"=>date("Y-m-d H:i:s")
        ]);
    }
    
    public function isUser($email)
    {
        return DB::table("users")->where([
            "email"=>$email
        ])->count();
    }

    public function isGroup($gcode)
    {
        return DB::table("group")->where([
            "gcode"=>$gcode,
        ])->count();
    }

    public function createGroup($uid, $gcode, $img, $name, $public, $description, $join_policy)
    {
        $new_group=DB::table("group")->insert([
            "gcode"=>$gcode,
            "img"=>$img,
            "name"=>$name,
            "public"=>$public,
            "verified"=>0,
            "description"=>$description,
            "join_policy"=>$join_policy,
            "custom_icon"=>null,
            "custom_title"=>null,
            "create_time"=>date("Y-m-d H:i:s")
        ]);
        return DB::table("group_member")->insert([
            "uid"=>$uid,
            "gid"=>$new_group,
            "role"=>3,
            "join_time"=>date("Y-m-d H:i:s")
        ]);
    }
}
