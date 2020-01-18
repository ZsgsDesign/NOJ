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
        $list=DB::table($this->table)->orderBy('created_at','desc')->get()->all();
        if (empty($list)) {
            return [];
        }
        foreach ($list as &$item) {
            $notice_author=DB::table("users")->where(["id"=>$item["uid"]])->first();
            $item["name"]=$notice_author["name"];
            $item["avatar"]=$notice_author["avatar"];
            $item["post_date_parsed"]=formatHumanReadableTime($item["created_at"]);
            $item["content_parsed"]=clean(convertMarkdownToHtml($item["content"]));
        }
        return $list;
    }
}
