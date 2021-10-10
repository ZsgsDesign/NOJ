<?php

namespace App\Models\Eloquent\Tool;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\Submission;
use App\Models\Eloquent\User;
use Arr;
use Cache;
use DB;

class SiteRank extends Model
{
    private static $professionalRanking = [
        "Legendary Grandmaster" => "cm-colorful-text",
        "International Grandmaster" => "wemd-pink-text",
        "Grandmaster" => "wemd-red-text",
        "International Master" => "wemd-amber-text",
        "Master" => "wemd-orange-text",
        "Candidate Master" => "wemd-purple-text",
        "Expert" => "wemd-blue-text",
        "Specialist" => "wemd-cyan-text",
        "Pupil" => "wemd-green-text",
        "Newbie" => "wemd-gray-text",
    ];

    private static $professionalRankingPer = [
        "Legendary Grandmaster" => 3000,
        "International Grandmaster" => 2600,
        "Grandmaster" => 2400,
        "International Master" => 2300,
        "Master" => 2100,
        "Candidate Master" => 1900,
        "Expert" => 1600,
        "Specialist" => 1400,
        "Pupil" => 1200,
        "Newbie" => 1,
    ];

    private static $casualRanking = [
        "Fleet Admiral" => "cm-colorful-text",
        "Admiral" => "wemd-pink-text",
        "Vice Admiral" => "wemd-red-text",
        "Captain" => "wemd-deep-orange-text",
        "Commander" => "wemd-orange-text",
        "Lieutenant Commander" => "wemd-purple-text",
        "Lieutenant" => "wemd-blue-text",
        "Ensign" => "wemd-cyan-text",
        "Apprentice" => "wemd-green-text",
        "Recruit" => "wemd-gray-text",
    ];

    public $casualRankingPer = [
        "Fleet Admiral" => 1,
        "Admiral" => 5,
        "Vice Admiral" => 10,
        "Captain" => 10,
        "Commander" => 50,
        "Lieutenant Commander" => 100,
        "Lieutenant" => 300,
        "Ensign" => 700,
        "Apprentice" => 1000,
        "Recruit" => 400,
    ];

    public static function getColor($rankTitle)
    {
        if (is_null($rankTitle)) {
            return "";
        }
        return self::$casualRanking[$rankTitle];
    }

    public static function getProfessionalColor($rankTitle)
    {
        if (is_null($rankTitle)) {
            return self::$professionalRanking["None"];
        }
        return self::$professionalRanking[$rankTitle];
    }

    public function list($num)
    {
        $rankList = Cache::tags(['rank'])->get('general');
        if ($rankList == null) {
            $rankList = [];
        }
        $rankList = collect($rankList)->slice(0, $num);
        $userIDArr = $rankList->pluck('uid');
        $userInfoRaw = User::whereIntegerInRaw('id', $userIDArr)->get();
        $userInfo = [];
        foreach ($userInfoRaw as $u) {
            $userInfo[$u->id] = $u;
        }
        return $rankList->map(function ($item) use ($userInfo) {
            $item["details"] = isset($userInfo[$item["uid"]]) ? $userInfo[$item["uid"]] : [];
            return $item;
        });
    }

    public function rankList()
    {
        Cache::tags(['rank'])->flush();
        $totUsers = Submission::where(["verdict" => "Accepted"])->select('uid')->distinct()->count('uid');
        if ($totUsers > 0) {
            $rankList = DB::select("SELECT *,solvedCount+communityCount as totValue, 1 as activityCoefficient FROM (SELECT uid,sum(solvedCount) as solvedCount,sum(communityCount) as communityCount FROM ((SELECT uid,count(DISTINCT submission.pid) as solvedCount,0 as communityCount from submission where verdict=\"Accepted\" group by uid) UNION (SELECT uid,0 as solvedCount,count(DISTINCT pid) from problem_solution where audit=1 group by uid)) as temp GROUP BY uid) as temp2 ORDER BY solvedCount+communityCount DESC");
            $rankIter = 1;
            $rankValue = 1;
            $rankSolved = -1;
            $rankListCached = [];
            $this->procRankingPer(count($rankList));
            foreach ($rankList as &$rankItem) {
                $rankItem["totValue"] *= $rankItem["activityCoefficient"];
            }
            unset($rankItem);
            usort($rankList, function($a, $b) {
                return $b['totValue'] <=> $a['totValue'];
            });
            foreach ($rankList as $rankItem) {
                if ($rankSolved != $rankItem["totValue"]) {
                    $rankValue = $rankIter;
                    $rankSolved = $rankItem["totValue"];
                }
                $rankTitle = $this->getRankTitle($rankValue);
                Cache::tags(['rank', $rankItem["uid"]])->put("rank", $rankValue, 86400);
                Cache::tags(['rank', $rankItem["uid"]])->put("title", $rankTitle, 86400);
                $rankListCached[] = [
                    "uid" => $rankItem["uid"],
                    "rank" => $rankValue,
                    "title" => $rankTitle,
                    "titleColor" => self::getColor($rankTitle),
                    "solved" => $rankItem["solvedCount"],
                    "community" => $rankItem["communityCount"],
                    "activityCoefficient" => $rankItem["activityCoefficient"],
                ];
                $rankIter++;
            }
            Cache::tags(['rank'])->put("general", $rankListCached, 86400);
        }
    }

    public function getProfessionalRanking()
    {
        $professionalRankList = [];
        $verifiedUsers = User::all();
        $rankIter = 0;
        foreach ($verifiedUsers as $user) {
            $rankVal = $user->professional_rate;
            $rankTitle = self::getProfessionalTitle($rankVal);
            $titleColor = self::getProfessionalColor($rankTitle);
            $professionalRankList[$rankIter++] = [
                "name" => $user->name,
                "uid" => $user->id,
                "avatar" => $user->avatar,
                "professionalRate" => $user->professional_rate,
                "rankTitle" => $rankTitle,
                "titleColor" => $titleColor
            ];
        }
        return $professionalRankList;
    }

    private function procRankingPer($totUsers)
    {
        if ($totUsers > 0) {
            $tot = 0;
            $cur = 0;
            foreach ($this->casualRankingPer as $c) {
                $tot += $c;
            }
            foreach ($this->casualRankingPer as &$c) {
                $c = round($c * $totUsers / $tot);
                $cur += $c;
                $c = $cur;
            }
            $c = $totUsers;
            unset($c);
        }
    }

    public function getRankTitle($rankVal)
    {
        foreach ($this->casualRankingPer as $title => $c) {
            if ($rankVal <= $c) {
                return $title;
            }
        }
        return Arr::last($this->casualRankingPer);
    }

    public static function getProfessionalTitle($rankVal)
    {
        foreach (self::$professionalRankingPer as $title => $point) {
            if ($rankVal >= $point) {
                return $title;
            }
        }
        return Arr::last(self::$professionalRankingPer);
    }
}
