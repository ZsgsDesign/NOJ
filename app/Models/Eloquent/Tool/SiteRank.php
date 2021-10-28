<?php

namespace App\Models\Eloquent\Tool;

use App\Models\Eloquent\ProblemSolution;
use App\Models\Eloquent\Submission;
use App\Models\Eloquent\User;
use Arr;
use Cache;
use Carbon;
use DB;

class SiteRank
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

    private static $casualRankingPer = [
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

    public static function list($num)
    {
        $rankList = Cache::tags(['rank'])->get('general');
        if (blank($rankList)) {
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

    private static function getRecords(Carbon $from = null)
    {
        $userAcceptedRecords = Submission::select("uid", DB::raw("count(distinct pid) as solved"))->where("verdict", "Accepted");
        $userCommunityRecords = ProblemSolution::select("uid", DB::raw("count(distinct pid) as community"))->where("audit", 1);
        if(filled($from)){
            $userAcceptedRecords = $userAcceptedRecords->where("submission_date", ">", $from->timestamp);
            $userCommunityRecords = $userCommunityRecords->where("created_at", ">", $from);
        }
        $userAcceptedRecords = collect($userAcceptedRecords->groupBy("uid")->get()->toArray());
        $userCommunityRecords = collect($userCommunityRecords->groupBy("uid")->get()->toArray());
        $totUserRecords = $userAcceptedRecords->pluck('uid')->merge($userCommunityRecords->pluck('uid'))->unique();
        $rankList = [];
        foreach($totUserRecords as $uid) {
            $rankList[$uid]['uid'] = $uid;
            $rankList[$uid]['solved'] = 0;
            $rankList[$uid]['community'] = 0;
            $rankList[$uid]['tot'] = 0;
        }
        foreach($userAcceptedRecords as $userAcceptedRecord) {
            $rankList[$userAcceptedRecord['uid']]['solved'] = $userAcceptedRecord['solved'];
        }
        foreach($userCommunityRecords as $userCommunityRecord) {
            $rankList[$userCommunityRecord['uid']]['community'] = $userCommunityRecord['community'];
        }
        foreach($rankList as &$rankItem) {
            $rankItem['tot'] = $rankItem['solved'] + $rankItem['community'];
        }
        unset($rankItem);
        return $rankList;
    }

    private static function parseCoefficient($rankList)
    {
        $activityCoefficient = self::getRecords(Carbon::parse('-1 months'));
        $activityCoefficientDivider = collect($activityCoefficient)->max('tot');
        if(blank($activityCoefficientDivider)) {
            $activityCoefficientDivider = 1;
        }
        foreach ($rankList as $uid => $rankItem) {
            if(isset($activityCoefficient[$uid])){
                $activityTot = $activityCoefficient[$uid]['tot'];
            } else {
                $activityTot = 0;
            }
            $rankList[$uid]["activityCoefficient"] = ($activityTot / $activityCoefficientDivider) + 0.5;
            $rankList[$uid]["points"] = $rankList[$uid]["tot"] * $rankList[$uid]["activityCoefficient"];
        }
        usort($rankList, function($a, $b) {
            return $b['points'] <=> $a['points'];
        });
        return collect($rankList);
    }

    public static function isTopOneHundred($rank)
    {
        return (1 <= $rank && $rank <= 100);
    }

    public static function getRankString($rank)
    {
        return filled($rank) ? "#$rank" : "unrated";
    }

    private static function sendMessage($userID, $currentRank, $originalRank)
    {
        if(self::isTopOneHundred($currentRank)) {
            $title = __('message.rank.up.title');
            $level = 1;
        } else {
            $title = __('message.rank.down.title');
            $level = 2;
        }

        return sendMessage([
            'sender'   => config('app.official_sender'),
            'receiver' => $userID,
            'title'    => $title,
            'type'     => 6,
            'level'    => $level,
            'data'     => [
                'currentRank'  => $currentRank,
                'originalRank' => $originalRank,
            ]
        ]);
    }

    public static function rankList()
    {
        $originalRankList = self::list(100);
        Cache::tags(['rank'])->flush();
        $rankList = self::getRecords();
        $totUsers = count($rankList);
        if ($totUsers > 0) {
            // $rankList = DB::select("SELECT *,solvedCount+communityCount as totValue, 1 as activityCoefficient FROM (SELECT uid,sum(solvedCount) as solvedCount,sum(communityCount) as communityCount FROM ((SELECT uid,count(DISTINCT submission.pid) as solvedCount,0 as communityCount from submission where verdict=\"Accepted\" group by uid) UNION (SELECT uid,0 as solvedCount,count(DISTINCT pid) from problem_solution where audit=1 group by uid)) as temp GROUP BY uid) as temp2 ORDER BY solvedCount+communityCount DESC");
            $rankList = self::parseCoefficient($rankList);
            $rankIter = 1;
            $rankValue = 1;
            $rankSolved = -1;
            $rankListCached = [];
            self::procRankingPer($totUsers);
            foreach ($rankList as $rankItem) {
                if ($rankSolved != $rankItem["points"]) {
                    $rankValue = $rankIter;
                    $rankSolved = $rankItem["points"];
                }
                $rankTitle = self::getRankTitle($rankValue);
                Cache::tags(['rank', $rankItem["uid"]])->put("rank", $rankValue, 86400);
                Cache::tags(['rank', $rankItem["uid"]])->put("title", $rankTitle, 86400);
                $rankListCached[] = [
                    "uid" => $rankItem["uid"],
                    "rank" => $rankValue,
                    "title" => $rankTitle,
                    "titleColor" => self::getColor($rankTitle),
                    "solved" => $rankItem["solved"],
                    "community" => $rankItem["community"],
                    "activityCoefficient" => $rankItem["activityCoefficient"],
                ];
                $rankIter++;
            }
            Cache::tags(['rank'])->put("general", $rankListCached, 86400);
            $currentRankList = self::list(100);
            self::sendRankUpDownMessage($originalRankList, $currentRankList);
        }
    }

    private static function sendRankUpDownMessage($originalRankList, $currentRankList)
    {
        if(blank($originalRankList) || blank($currentRankList)) {
            return;
        }

        $originalRankUID = [];
        foreach($originalRankList as $originalRankItem) {
            $originalRankUID[] = $originalRankItem['uid'];
        }

        $currentRankUID = [];
        foreach($currentRankList as $currentRankItem) {
            $currentRankUID[] = $currentRankItem['uid'];
        }

        foreach($originalRankList as $originalRankItem) {
            if(in_array($originalRankItem['uid'], $currentRankUID)) {
                continue;
            }
            self::sendMessage($originalRankItem['uid'], Cache::tags(['rank', $originalRankItem['uid']])->get("rank", null), $originalRankItem['rank']);
        }

        foreach($currentRankList as $currentRankItem) {
            if(in_array($currentRankItem['uid'], $originalRankUID)) {
                continue;
            }
            self::sendMessage($currentRankItem['uid'], $currentRankItem['rank'], null);
        }
    }

    public static function getProfessionalRanking()
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

    private static function procRankingPer($totUsers)
    {
        if ($totUsers > 0) {
            $tot = 0;
            $cur = 0;
            foreach (self::$casualRankingPer as $c) {
                $tot += $c;
            }
            foreach (self::$casualRankingPer as &$c) {
                $c = round($c * $totUsers / $tot);
                $cur += $c;
                $c = $cur;
            }
            $c = $totUsers;
            unset($c);
        }
    }

    public static function getRankTitle($rankVal)
    {
        foreach (self::$casualRankingPer as $title => $c) {
            if ($rankVal <= $c) {
                return $title;
            }
        }
        return Arr::last(self::$casualRankingPer);
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
