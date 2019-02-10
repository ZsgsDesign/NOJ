<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Problem extends Model
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
        $prob_list = DB::table($this->tableName)->select("pcode","title")->get();
        // [ToDo] Paging required
        // [ToDo] ACRate / Submitted & Passed data required
        return $prob_list;
    }
}
