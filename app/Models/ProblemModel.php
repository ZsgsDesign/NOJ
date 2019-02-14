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
            $prob_detail["parsed"] = [
                "description"=>Markdown::convertToHtml($prob_detail["description"]),
                "input"=>Markdown::convertToHtml($prob_detail["input"]),
                "output"=>Markdown::convertToHtml($prob_detail["output"])
            ];
            $prob_detail["oj_detail"] = DB::table("oj")->where("oid", $prob_detail["OJ"])->first();
            $prob_detail["samples"] = DB::table("problem_sample")->where("pid", $prob_detail["pid"])->get()->all();
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

    public function pid($pcode)
    {
        $temp = DB::table($this->tableName)->where(["pcode"=>$pcode])->select("pid")->first();
        return empty($temp) ? 0 : $temp["pid"];
    }

    public function clearTags($pid)
    {
        DB::table("problem_tag")->where(["pid"=>$pid])->delete();
        return true;
    }

    public function addTags($pid,$tag)
    {
        DB::table("problem_tag")->insert(["pid"=>$pid,"tag"=>$tag]);
        return true;
    }

    public function getSolvedCount($oid){
        return DB::table($this->tableName)->select("pid","solved_count")->where(["OJ"=>$oid])->get()->all();
    }

    public function updateDifficulty($pid,$diff_level){
        DB::table("problem_tag")->where(["pid"=>$pid])->update(["difficulty"=>$diff_level]);
        return true;
    }
}
