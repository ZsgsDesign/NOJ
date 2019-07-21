<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnnouncementModel extends Model
{
    protected $tableName='announcement';
    protected $table='announcement';
    protected $primaryKey='anid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    public function fetch()
    {
        $list=DB::table($this->table)->orderBy('post_date','desc')->get()->all();
        if (empty($list)) {
            return [];
        }
        foreach ($list as &$item) {
            $notice_author=DB::table("users")->where(["id"=>$item["uid"]])->first();
            $item["name"]=$notice_author["name"];
            $item["avatar"]=$notice_author["avatar"];
            $item["post_date_parsed"]=$this->formatPostTime($item["post_date"]);
            $item["content_parsed"]=clean(Markdown::convertToHtml($item["content"]));
        }
        return $list;
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
}
