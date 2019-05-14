<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Cache,Redis;

class RankModel extends Model
{
    private static $professionalRankiing=[
        "None"=>"wemd-black-text"
    ];

    public $professionalRankiingPer=[
        "None"=>1
    ];

    private static $casualRanking=[
        "Legendary Grandmaster"=>"cm-colorful-text",
        "International Grandmaster"=>"wemd-pink-text",
        "Grandmaster"=>"wemd-red-text",
        "International Master"=>"wemd-amber-text",
        "Master"=>"wemd-orange-text",
        "Candidate Master"=>"wemd-purple-text",
        "Expert"=>"wemd-blue-text",
        "Specialist"=>"wemd-cyan-text",
        "Pupil"=>"wemd-green-text",
        "Newbie"=>"wemd-gray-text",
    ];

    public $casualRankingPer=[
        "Legendary Grandmaster"=>1,
        "International Grandmaster"=>5,
        "Grandmaster"=>10,
        "International Master"=>10,
        "Master"=>50,
        "Candidate Master"=>100,
        "Expert"=>300,
        "Specialist"=>700,
        "Pupil"=>1000,
        "Newbie"=>400,
    ];

    public static function getColor($rankTitle)
    {
        if(is_null($rankTitle)) return "";
        return self::$casualRanking[$rankTitle];
    }

    public function list()
    {
        $rankList=Cache::tags(['rank'])->get('general');
        $userInfoRaw=DB::table("users")->select("id as uid","avatar","name")->get()->all();
        $userInfo=[];
        foreach($userInfoRaw as $u){
            $userInfo[$u["uid"]]=$u;
        }
        foreach($rankList as &$r){
            $r["details"]=isset($userInfo[$r["uid"]])?$userInfo[$r["uid"]]:[];
        }
        // var_dump($rankList); exit();
        return $rankList;
    }

    public function rankList()
    {
        Cache::tags(['rank'])->flush();
        $totUsers=DB::table("submission")->distinct()->where(["verdict"=>"Accepted"])->count();
        if ($totUsers>0) {
            $rankList=DB::select("SELECT * FROM (SELECT uid,count(DISTINCT pcode) as solvedCount from submission inner join problem on problem.pid=submission.pid and verdict=\"Accepted\" group by uid) as temp ORDER BY solvedCount desc");
            $rankIter=1;
            $rankValue=1;
            $rankSolved=-1;
            $rankListCached=[];
            $this->procRankingPer();
            foreach ($rankList as $rankItem) {
                if ($rankSolved!=$rankItem["solvedCount"]) {
                    $rankValue=$rankIter;
                    $rankSolved=$rankItem["solvedCount"];
                }
                $rankTitle=$this->getRankTitle($rankValue);
                Cache::tags(['rank',$rankItem["uid"]])->put("rank", $rankValue, 86400);
                Cache::tags(['rank',$rankItem["uid"]])->put("title", $rankTitle, 86400);
                $rankListCached[]=[
                    "uid"=>$rankItem["uid"],
                    "rank"=>$rankValue,
                    "title"=>$rankTitle,
                    "titleColor"=>self::getColor($rankTitle),
                    "solved"=>$rankItem["solvedCount"]
                ];
                $rankIter++;
            }
            Cache::tags(['rank'])->put("general", $rankListCached, 86400);
        }
    }

    private function procRankingPer()
    {
        $totUsers=DB::table("submission")->distinct()->where(["verdict"=>"Accepted"])->count();
        if ($totUsers>0) {
            $tot=0;
            $cur=0;
            foreach ($this->casualRankingPer as $c) {
                $tot+=$c;
            }
            foreach ($this->casualRankingPer as &$c) {
                $c=round($c*$totUsers/$tot);
                $cur+=$c;
                $c=$cur;
            }
            $c=$totUsers;
            unset($c);
        }
    }

    private function getRankTitle($rankVal)
    {
        foreach($this->casualRankingPer as $title=>$c){
            if($rankVal<=$c) return $title;
        }
        return Arr::last($this->casualRankingPer);
    }
}
