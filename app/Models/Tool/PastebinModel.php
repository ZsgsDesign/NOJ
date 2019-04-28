<?php

namespace App\Models\Tool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PastebinModel extends Model
{
    protected $tableName='pastebin';

    public function generatePbCode($length=6)
    {
        $chars='abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';

        $code='';
        for ($i=0; $i<$length; $i++) {
            $code.=$chars[mt_rand(0, strlen($chars)-1)];
        }
        return $code;
    }

    public function detail($code)
    {
        return DB::table($this->tableName)->where(["code"=>$code])->first();
    }

    public function generate($all_data)
    {
        $lang=$all_data["syntax"];
        $expire=intval($all_data["expiration"]);
        $content=$all_data["content"];
        $title=$all_data["title"];
        $uid=$all_data["uid"];

        if($expire==0){
            $expire_time=null;
        }elseif($expire==1){
            $expire_time=date('Y-m-d', strtotime('+1 days'));
        }elseif($expire==7){
            $expire_time=date('Y-m-d', strtotime('+7 days'));
        }elseif($expire==30){
            $expire_time=date('Y-m-d', strtotime('+30 days'));
        }

        $code=$this->generatePbCode(6);
        $ret=$this->detail($code);
        if(empty($ret)){
            return DB::table($this->tableName)->insertGetId([
                'lang' => $lang,
                'expire' => $expire_time,
                'uid' => $uid,
                'title' => $title,
                'content' => $content,
                'code' => $code
            ]);
        }else{
            return -1;
        }
    }
}
