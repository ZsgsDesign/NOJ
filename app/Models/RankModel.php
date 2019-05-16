<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Rating\RatingCalculator;
use Cache,Redis;

class RankModel extends Model
{
    private static $professionalRanking=[
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

    public $professionalRankingPer=[
        "Legendary Grandmaster"=>3000,
        "International Grandmaster"=>2600,
        "Grandmaster"=>2400,
        "International Master"=>2300,
        "Master"=>2100,
        "Candidate Master"=>1900,
        "Expert"=>1600,
        "Specialist"=>1400,
        "Pupil"=>1200,
        "Newbie"=>1,
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

    public static function getProfessionalColor($rankTitle)
    {
        if(is_null($rankTitle)) return self::$professionalRanking["None"];
        return self::$professionalRanking[$rankTitle];
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
        $totUsers=DB::table("submission")->where(["verdict"=>"Accepted"])->select(DB::raw("count(distinct uid) as res"))->get()->first()["res"];
        if ($totUsers>0) {
            $rankList=DB::select("SELECT *,solvedCount+communityCount as totValue FROM (SELECT uid,sum(solvedCount) as solvedCount,sum(communityCount) as communityCount FROM ((SELECT uid,count(DISTINCT submission.pid) as solvedCount,0 as communityCount from submission where verdict=\"Accepted\" group by uid) UNION (SELECT uid,0 as solvedCount,count(DISTINCT pid) from problem_solution where audit=1 group by uid)) as temp GROUP BY uid) as temp2 ORDER BY solvedCount+communityCount DESC");
            $rankIter=1;
            $rankValue=1;
            $rankSolved=-1;
            $rankListCached=[];
            $this->procRankingPer();
            foreach ($rankList as $rankItem) {
                if ($rankSolved!=$rankItem["totValue"]) {
                    $rankValue=$rankIter;
                    $rankSolved=$rankItem["totValue"];
                }
                $rankTitle=$this->getRankTitle($rankValue);
                Cache::tags(['rank',$rankItem["uid"]])->put("rank", $rankValue, 86400);
                Cache::tags(['rank',$rankItem["uid"]])->put("title", $rankTitle, 86400);
                $rankListCached[]=[
                    "uid"=>$rankItem["uid"],
                    "rank"=>$rankValue,
                    "title"=>$rankTitle,
                    "titleColor"=>self::getColor($rankTitle),
                    "solved"=>$rankItem["solvedCount"],
                    "community"=>$rankItem["communityCount"]
                ];
                $rankIter++;
            }
            Cache::tags(['rank'])->put("general", $rankListCached, 86400);
        }
    }

    public function getProfessionalRanking()
    {
        $professionalRankList = [];
        $verifiedUsers = DB::table("users")->select("professional_rate","id as uid","avatar","name")->get()->all();
        $rankIter = 0;
        foreach($verifiedUsers as $user) {
            $rankVal = $user['professional_rate'];
            $rankTitle = self::getProfessionalTitle($rankVal);
            $titleColor = self::getProfessionalColor($rankTitle);
            $professionalRankList[$rankIter++] = [
                "name"=>$user["name"],
                "uid"=>$user["uid"],
                "avatar"=>$user["avatar"],
                "professionalRate"=>$user["professional_rate"],
                "rankTitle"=>$rankTitle,
                "titleColor"=>$titleColor
            ];
        }
        return $rankList;
    }

    private function procRankingPer()
    {
        $totUsers=DB::table("submission")->where(["verdict"=>"Accepted"])->select(DB::raw("count(distinct uid) as res"))->get()->first()["res"];
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

    public function getRankTitle($rankVal)
    {
        foreach($this->casualRankingPer as $title=>$c){
            if($rankVal<=$c) return $title;
        }
        return Arr::last($this->casualRankingPer);
    }

    public static function getProfessionalTitle($rankVal)
    {
        foreach(self::$professionalRankingPer as $title=>$point) {
            if($rankVal >= $point) return $title;
        }
        return Arr::last(self::$professionalRankingPer);
    }
}
