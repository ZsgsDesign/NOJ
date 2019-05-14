<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use grubersjoe\BingPhoto;
use Cache;
use Storage;

class AccountModel extends Model
{
    public function generatePassword($length=8)
    {
        $chars='abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';

        $password='';
        for ($i=0; $i<$length; $i++) {
            $password.=$chars[mt_rand(0, strlen($chars)-1)];
        }
        return $password;
    }

    public function feed($uid=null)
    {
        $ret=[];
        $solution=DB::table("problem_solution")->join("problem","problem.pid","=","problem_solution.pid")->where(["uid"=>$uid,"audit"=>1])->select("problem.pid as pid","pcode","title","problem_solution.created_at as created_at")->get()->all();
        foreach($solution as &$s){
            $s["type"]="event";
            $s["color"]="wemd-orange";
            $s["icon"]="comment-check-outline";
            $ret[]=$s;
        }
        return $ret;
    }

    public function generateContestAccount($cid, $ccode, $num)
    {
        $ret=[];
        $starting=DB::table("users")->where(["contest_account"=>$cid])->count();
        $contestModel=new ContestModel();
        for ($i=1; $i<=$num; $i++) {
            $pass=$this->generatePassword();
            $name=strtoupper($ccode).str_pad($starting+$i, 3, "0", STR_PAD_LEFT);
            $uid=$this->add([
                'name' => $name,
                'email' => "$name@icpc.njupt.edu.cn",
                'email_verified_at' => date("Y-m-d H:i:s"),
                'password' => $pass,
                'avatar' => "/static/img/avatar/default.png",
                'contest_account' => $cid
            ]);
            $contestModel->grantAccess($uid, $cid, 1);
            $ret[]=[
                "uid"=>$uid,
                "name"=>$name,
                "email"=>"$name@icpc.njupt.edu.cn",
                "password"=>$pass
            ];
        }
        return $ret;
    }

    public function add($data)
    {
        return DB::table("users")->insertGetId([
            'name' => $data['name'],
            'email' => $data['email'],
            'email_verified_at' => $data["email_verified_at"],
            'password' => Hash::make($data['password']),
            'avatar' => $data["avatar"],
            'contest_account' => $data["contest_account"],
            'remember_token'=>null,
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s")
        ]);
    }

    public function detail($uid)
    {
        $ret=DB::table("users")->where(["id"=>$uid])->first();
        $ret["submissionCount"]=DB::table("submission")->where([
            "uid"=>$uid,
        ])->whereNotIn('verdict', [
            'System Error',
            'Submission Error'
        ])->count();
        $ret["solved"]=DB::table("submission")->where([
            "uid"=>$uid,
            "verdict"=>"Accepted"
        ])->join("problem", "problem.pid", "=", "submission.pid")->select('pcode')->distinct()->get()->all();
        $ret["solvedCount"]=count($ret["solved"]);
        $ret["rank"]=Cache::tags(['rank'])->get($ret["id"], "N/A");
        if (Cache::tags(['bing', 'pic'])->get(date("Y-m-d"))==null) {
            $bing=new BingPhoto([
                'locale' => 'zh-CN',
            ]);
            Storage::disk('NOJPublic')->put("static/img/bing/".date("Y-m-d").".jpg", file_get_contents($bing->getImage()['url']), 86400);
            Cache::tags(['bing', 'pic'])->put(date("Y-m-d"), "/static/img/bing/".date("Y-m-d").".jpg");
        }
        $ret["image"]=Cache::tags(['bing', 'pic'])->get(date("Y-m-d"));
        return $ret;
    }
}
