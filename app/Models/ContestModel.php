<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContestModel extends Model
{
    protected $tableName = 'contest';
    public $rule = ["Unknown","ACM","OI","Special OI"];

    public function calc_length($a,$b){
        $s=strtotime($b)-strtotime($a);
        $h=intval($s/3600);
        $m=round(($s-$h*3600)/60);
        if ($m==60) {
            $h++;
            $m=0;
        }
        if($m==0 && $h==0){
            $text="$s Seconds";
        }else if($m==0){
            $text="$h Hours";
        }else if($h==0){
            $text="$m Minutes";
        }else{
            $text="$h Hours $m Minutes";
        }
        return $text;
    }

    public function canViewContest($cid,$uid){
        $contest_detail = DB::table($this->tableName)->where([
            "cid"=>$cid
        ])->first();

        if($contest_detail["public"]==1){
            return $contest_detail;
        }else{
            // group contest
            if($uid==0) return [];
            $group_info = DB::table("group_member")->where([
                "uid"=>$uid,
                "gid"=>$contest_detail['gid'],
                ["role",">",0]
            ])->first();
            return empty($group_info) ? [] : $contest_detail;
        }
    }

    public function detail($cid,$uid=0)
    {
        $contest_detail= $this->canViewContest($cid,$uid);
        if(empty($contest_detail)){
            return [
                "ret"=>1000,
                "desc"=>"You have no right to view this contest.",
                "data"=>null
            ];
        }else{
            $contest_detail["rule_parsed"]=$this->rule[$contest_detail["rule"]];
            $contest_detail["date_parsed"]=[
                "date"=>date_format(date_create($contest_detail["begin_time"]),'j'),
                "month_year"=>date_format(date_create($contest_detail["begin_time"]),'M, Y'),
            ];
            $contest_detail["length"]=$this->calc_length($contest_detail["begin_time"],$contest_detail["end_time"]);
            $contest_detail["description_parsed"]=Markdown::convertToHtml($contest_detail["description"]);
            $contest_detail["group_info"]=DB::table("group")->where(["gid"=>$contest_detail["gid"]])->first();
            return [
                "ret"=>200,
                "desc"=>"succeed",
                "data"=>[
                    "contest_detail"=>$contest_detail
                ]
            ];
        }
    }

    public function list()
    {
        $contest_list = DB::table($this->tableName)->where([
            "public"=>1,
            "audit_status"=>1
        ])->orderBy('begin_time', 'desc')->get()->all();

        foreach($contest_list as &$c) {
            $c["rule_parsed"]=$this->rule[$c["rule"]];
            $c["date_parsed"]=[
                "date"=>date_format(date_create($c["begin_time"]),'j'),
                "month_year"=>date_format(date_create($c["begin_time"]),'M, Y'),
            ];
            $c["length"]=$this->calc_length($c["begin_time"],$c["end_time"]);
        }
        return $contest_list;
    }

    public function featured()
    {
        $featured = DB::table($this->tableName)->where([
            "public"=>1,
            "audit_status"=>1,
            "featured"=>1
        ])->orderBy('begin_time', 'desc')->first();

        $featured["rule_parsed"]=$this->rule[$featured["rule"]];
        $featured["date_parsed"]=[
            "date"=>date_format(date_create($featured["begin_time"]),'j'),
            "month_year"=>date_format(date_create($featured["begin_time"]),'M, Y'),
        ];
        $featured["length"]=$this->calc_length($featured["begin_time"],$featured["end_time"]);
        return $featured;
    }

    public function IntToChr($index, $start = 65) {
        $str = '';
        if (floor($index / 26) > 0) {
            $str .= IntToChr(floor($index / 26)-1);
        }
        return $str . chr($index % 26 + $start);
    }

    public function contestProblems($cid)
    {
        $problemSet = DB::table("contest_problem")->join("problem","contest_problem.pid","=","problem.pid")->where([
            "cid"=>$cid
        ])->orderBy('ncode', 'asc')->select("ncode","alias","contest_problem.pid as pid","title")->get()->all();

        return $problemSet;
    }
}
