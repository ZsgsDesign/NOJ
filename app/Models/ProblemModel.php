<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProblemModel extends Model
{
    protected $tableName = 'problem';

    public function detail($pcode)
    {
        $prob_detail = DB::table($this->tableName)->where("pcode", $pcode)->first();
        // [Depreciated] Joint Query was depreciated here for code maintenance reasons
        if (!is_null($prob_detail)) {
            $prob_detail["desc_parsed"] = Markdown::convertToHtml($prob_detail["desc"]);
            $prob_detail["oj_detail"] = DB::table("oj")->where("oid", $prob_detail["OJ"])->first();
        }
        return $prob_detail;
    }

    public function list()
    {
        $prob_list = DB::table($this->tableName)->select("pid","pcode","title")->get()->all(); // return a array
        // [ToDo] Paging required
        foreach($prob_list as &$p) {
            $prob_stat = DB::table("submission")->select(
                DB::raw("count(sid) as submission_count"),
                DB::raw("sum(verdict='accepted') as passed_count"),
                DB::raw("sum(verdict='accepted')/count(sid)*100 as ac_rate")
            )->where(["pid"=>$p["pid"]])->first();
            if($prob_stat["submission_count"]==0){
                $p["submission_count"]=0;
                $p["passed_count"]=0;
                $p["ac_rate"]=0;
            }else{
                $p["submission_count"]=$prob_stat["submission_count"];
                $p["passed_count"]=$prob_stat["passed_count"];
                $p["ac_rate"]=round($prob_stat["ac_rate"],2);
            }
        }
        return $prob_list;
    }
}
