<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ContestModel as OutdatedContestModel;
use Cache;

class ContestModel extends Model
{
    protected $table='contest';
    protected $primaryKey='cid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    //Repository function
    public function participants($ignore_frozen = true)
    {
        if($this->registration){
            $participants = ContestParticipant::where('cid',$this->cid)->get();
            $participants->load('user');
            $users = new EloquentCollection;
            foreach ($participants as $participant) {
                $user = $participant->user;
                $users->add($user);
            }
            return $users->unique();
        }else{
            $this->load('submissions.user');
            if($ignore_frozen){
                $frozen_time = $this->frozen_time;
                $submissions = $this->submissions()->where('submission_date','<',$frozen_time)->get();
            }else{
                $submissions = $this->submissions;
            }
            $users = new EloquentCollection;
            foreach ($submissions as $submission) {
                $user = $submission->user;
                $users->add($user);
            }
            return $users->unique();
        }
    }

    // Repository/Service? function
    public function rankRefresh()
    {
        $ret = [];
        $participants = $this->participants();
        $contest_problems = $this->problems;
        $contest_problems->load('problem');
        if($this->rule == 1){
            // ACM/ICPC Mode
            foreach ($participants as $participant) {
                $prob_detail=[];
                $totPen=0;
                $totScore=0;
                foreach ($contest_problems as $contest_problem) {
                    $prob_stat = $contest_problem->userStatus($participant);
                    $prob_detail[]=[
                        'ncode'=>$contest_problem->ncode,
                        'pid'=>$contest_problem->pid,
                        'color'=>$prob_stat['color'],
                        'wrong_doings'=>$prob_stat['wrong_doings'],
                        'solved_time_parsed'=>$prob_stat['solved_time_parsed']
                    ];
                    if ($prob_stat['solved']) {
                        $totPen+=$prob_stat['wrong_doings'] * 20;
                        $totPen+=$prob_stat['solved_time'] / 60;
                        $totScore+=$prob_stat['solved'];
                    }
                }
                $ret[]=[
                    "uid" => $participant->id,
                    "name" => $participant->name,
                    "nick_name" => DB::table("group_member")->where([
                        "uid" => $participant->id,
                        "gid" => $this->group->gid
                    ])->where("role", ">", 0)->first()["nick_name"] ?? '',
                    "score" => $totScore,
                    "penalty" => $totPen,
                    "problem_detail" => $prob_detail
                ];
            }
            usort($ret, function ($a, $b) {
                if ($a["score"]==$b["score"]) {
                    if ($a["penalty"]==$b["penalty"]) {
                        return 0;
                    } elseif (($a["penalty"]>$b["penalty"])) {
                        return 1;
                    } else {
                        return -1;
                    }
                } elseif ($a["score"]>$b["score"]) {
                    return -1;
                } else {
                    return 1;
                }
            });
            Cache::tags(['contest', 'rank'])->put($this->cid, $ret, 60);
            return $ret;
        }else{
            // IO Mode
            $c = new OutdatedContestModel();
            return $c->contestRankCache($this->cid);
        }
    }

    public function problems()
    {
        return $this->hasMany('App\Models\Eloquent\ContestProblem','cid','cid');
    }

    public function submissions()
    {
        return $this->hasMany('App\Models\Eloquent\SubmissionModel','cid','cid');
    }

    public function group()
    {
        return $this->hasOne('App\Models\Eloquent\GroupModel','gid','gid');
    }

    public function getFrozenTimeAttribute()
    {
        $end_time = strtotime($this->end_time);
        return $end_time - $this->froze_length;
    }
}
