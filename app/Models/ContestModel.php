<?php

namespace App\Models;

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
}
