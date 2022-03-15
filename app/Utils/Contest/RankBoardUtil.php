<?php

namespace App\Utils\Contest;

use App\Models\Eloquent\Contest;
use Illuminate\Support\Facades\DB;

class RankBoardUtil
{

    private Contest $contest;

    public function __construct(Contest $contest)
    {
        $this->contest = $contest;
    }

    public function getRankBoard(): array
    {
        if ($this->contest->rule == 1) {
            return $this->rankRefreshICPC();
        } else {
            return $this->rankRefreshIOI();
        }
    }

    private function rankRefreshICPC(): array
    {
        $ret = [];
        $participants = $this->contest->participants();
        $contest_problems = $this->contest->challenges;
        $contest_problems->load('problem');
        // ACM/ICPC Mode
        foreach ($participants as $participant) {
            $prob_detail = [];
            $totPen = 0;
            $totScore = 0;
            foreach ($contest_problems as $contest_problem) {
                $prob_stat = $contest_problem->userStatus($participant);
                $prob_detail[] = [
                    'ncode' => $contest_problem->ncode,
                    'pid' => $contest_problem->pid,
                    'color' => $prob_stat['color'],
                    'wrong_doings' => $prob_stat['wrong_doings'],
                    'solved_time_parsed' => $prob_stat['solved_time_parsed']
                ];
                if ($prob_stat['solved']) {
                    $totPen += $prob_stat['wrong_doings'] * 20;
                    $totPen += $prob_stat['solved_time'] / 60;
                    $totScore += $prob_stat['solved'];
                }
            }
            $ret[] = [
                "uid" => $participant->id,
                "name" => $participant->name,
                "nick_name" => DB::table("group_member")->where([
                    "uid" => $participant->id,
                    "gid" => $this->contest->group->gid
                ])->where("role", ">", 0)->first()["nick_name"] ?? '',
                "score" => $totScore,
                "penalty" => $totPen,
                "problem_detail" => $prob_detail
            ];
        }
        usort($ret, function ($a, $b) {
            if ($a["score"] == $b["score"]) {
                if ($a["penalty"] == $b["penalty"]) {
                    return 0;
                } elseif (($a["penalty"] > $b["penalty"])) {
                    return 1;
                } else {
                    return -1;
                }
            } elseif ($a["score"] > $b["score"]) {
                return -1;
            } else {
                return 1;
            }
        });
        return $ret;
    }

    private function rankRefreshIOI(): array
    {
        $rankBoard = [];
        $participantsIds = $this->contest->participants()->pluck('id');
        [$problemDetailTemplate, $challengeInfo] = $this->getProblemDetailTemplateIOI();
        foreach ($this->contest->submissions()->where("submission_date", "<", $this->contest->frozen_time)->get() as $submission) {
            if (blank($challengeInfo[$submission->pid] ?? null) || !$participantsIds->contains($submission->uid)) {
                continue;
            }
            if (blank($rankBoard[$submission->uid] ?? null)) {
                $rankBoard[$submission->uid] = [
                    "uid" => $submission->user->id,
                    "name" => $submission->user->name,
                    "nick_name" => $submission->nick_name,
                    "score" => 0,
                    "solved" => 0,
                    "problem_detail" => $problemDetailTemplate
                ];
            }
            $rankUser = &$rankBoard[$submission->uid];
            $rankProblem = &$rankUser["problem_detail"][$challengeInfo[$submission->pid]['index']];
            if (is_null($rankProblem['score']) || $rankProblem['score'] < $submission->score) {
                $points = $challengeInfo[$submission->pid]['points'];
                $totalScore = $challengeInfo[$submission->pid]['tot_score'];

                $rankUser["solved"] += ($submission->score == $totalScore) ? 1 : 0;

                $rankProblem['score'] = $submission->score;
                $rankProblem["color"] = $rankUser["solved"] ? "wemd-teal-text" : "wemd-green-text";
                $rankProblem["score_parsed"] = $rankProblem["score"] / max($totalScore, 1) * ($points);
            }
        }
        unset($rankUser, $rankProblem);
        return collect($rankBoard)->transform(function ($rankUser) {
            $rankUser['score'] = collect($rankUser['problem_detail'])->sum('score_parsed');
            return $rankUser;
        })->sortBy([['score', 'desc'], ['solved', 'desc']])->values()->all();
    }

    private function getProblemDetailTemplateIOI(): array
    {
        $problemDetailTemplate = [];
        $challengeInfo = [];
        foreach ($this->contest->challenges as $index => $challenge) {
            $problemDetailTemplate[] = [
                "ncode" => $challenge->ncode,
                "pid" => $challenge->pid,
                "color" => null,
                "score" => null,
                "score_parsed" => null
            ];
            $challengeInfo[$challenge->pid] = [
                'index' => $index,
                'points' => $challenge->points,
                'tot_score' => $challenge->problem->tot_score,
            ];
        }
        return [$problemDetailTemplate, $challengeInfo];
    }
}
