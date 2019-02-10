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
        if(!is_null($prob_detail)) {
            $prob_detail["desc_parsed"]=Markdown::convertToHtml($prob_detail["desc"]);
        }
        return $prob_detail;
    }
}
