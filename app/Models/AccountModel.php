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
    private $user_extra = [
        'gender',
        'contact',
        'school',
        'country',
        'location',
        'editor_left_width'
    ];

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
        $solution=DB::table("problem_solution")->join("problem","problem.pid","=","problem_solution.pid")->where(["uid"=>$uid,"audit"=>1])->select("problem.pid as pid","pcode","title","problem_solution.created_at as created_at")->orderBy("problem_solution.created_at","DESC")->get()->all();
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
        // Casual
        $ret["rank"]=Cache::tags(['rank',$ret["id"]])->get("rank", "N/A");
        $ret["rankTitle"]=Cache::tags(['rank',$ret["id"]])->get("title");
        $ret["rankTitleColor"]=RankModel::getColor($ret["rankTitle"]);
        // Professional
        $ret["professionalTitle"]=RankModel::getProfessionalTitle($ret["professional_rate"]);
        $ret["professionalTitleColor"]=RankModel::getProfessionalColor($ret["professionalTitle"]);
        // Administration Group
        $ret["admin"]=$uid==1?1:0;
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

    /**
     * To get some extra info of a user.
     *
     * @param int $uid id of the user
     * @param string|array $need An array is returned when an array is passed in,Only one value is returned when a string is passed in.
     * @return string $result
     */
    public function getExtra($uid,$need, $secret_level = 0){
        $ret = DB::table('users_extra')->where('uid',$uid)->orderBy('key')->get()->all();
        $result = [];
        if(!empty($ret)){
            if(is_string($need)){
                foreach ($ret as $value) {
                    if(empty($value['secret_level']) || $value['secret_level'] <= $secret_level){
                        $key_name = $this->user_extra[$value['key']] ?? 'unknown';
                        if($key_name == $need){
                            return $value['value'];
                        }
                    }
                }
            }else{
                foreach ($ret as $value) {
                    if(empty($value['secret_level']) || $value['secret_level'] <= $secret_level){
                        $key_name = $this->user_extra[$value['key']] ?? 'unknown';
                        if(in_array($key_name,$need)){
                            $result[$key_name] = $value['value'];
                        }
                    }
                }
            }

        }
        return $result;
    }

    /**
     * To set some extra info of a user.
     *
     * @param int $uid id of the user
     * @param string $key_name insert when key not found or update when key exists. Only values declared in the AccountModel are accepted
     * @param string|null $value the extra info will be delete when value is null
     * @return mixed $result
     */
    public function setExtra($uid,$key_name,$value = null,$secret_level = -1){
        $key = array_search($key_name,$this->user_extra);
        if($key === false){
            return false;
        }
        $ret = DB::table('users_extra')->where('uid',$uid)->where('key',$key)->first();
        if(!empty($ret)){
            unset($ret['id']);
            if(!is_null($value)){
                $ret['value'] = $value;
            }else{
                DB::table('users_extra')->where('uid',$uid)->where('key',$key)->delete($ret);
                return true;
            }
            if($secret_level != -1){
                $ret['secret_level'] = $secret_level;
            }
            return DB::table('users_extra')->where('uid',$uid)->where('key',$key)->update($ret);
        }else{
            if($value === null){
                return true;
            }
            return DB::table('users_extra')->insertGetId(
                [
                    'uid' => $uid,
                    'key' => $key,
                    'value' => $value,
                    'secret_level' => $secret_level == -1 ? 0 : $secret_level,
                ]
            );
        }
    }
}
