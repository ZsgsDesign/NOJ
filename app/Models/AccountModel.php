<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
}
