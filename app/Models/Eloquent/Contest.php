<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProblemModel as OutdatedProblemModel;
use Illuminate\Support\Facades\DB;
use App\Models\ContestModel as OutdatedContestModel;
use Cache;
use DateTimeInterface;

class Contest extends Model
{
    protected $table='contest';
    protected $primaryKey='cid';

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getParsedRuleAttribute()
    {
        $rule=["Unknown", "ICPC", "IOI", "Custom ICPC", "Custom IOI", "HASAAOSE Compulter Exam"];
        return $rule[$this->rule];
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function($model) {
            if ($model->custom_icon!="" && $model->custom_icon!=null && $model->custom_icon[0]!="/") {
                $model->custom_icon="/$model->custom_icon";
            }
            if ($model->img!="" && $model->img!=null && $model->img[0]!="/") {
                $model->img="/$model->img";
            }
        });
    }

    //Repository function
    public function participants($ignore_frozen=true)
    {
        if ($this->registration) {
            $participants=ContestParticipant::where('cid', $this->cid)->get();
            $participants->load('user');
            $users=collect();
            foreach ($participants as $participant) {
                $user=$participant->user;
                $users->add($user);
            }
            return $users->unique();
        } else {
            $this->load('submissions.user');
            if ($ignore_frozen) {
                $frozen_time=$this->frozen_time;
                $submissions=$this->submissions()->where('submission_date', '<', $frozen_time)->get();
            } else {
                $submissions=$this->submissions;
            }
            $users=collect();
            foreach ($submissions as $submission) {
                $user=$submission->user;
                $users->add($user);
            }
            return $users->unique();
        }
    }

    // Repository/Service? function
    public function rankRefresh()
    {
        $ret=[];
        $participants=$this->participants();
        $contest_problems=$this->challenges;
        $contest_problems->load('problem');
        if ($this->rule==1) {
            // ACM/ICPC Mode
            foreach ($participants as $participant) {
                $prob_detail=[];
                $totPen=0;
                $totScore=0;
                foreach ($contest_problems as $contest_problem) {
                    $prob_stat=$contest_problem->userStatus($participant);
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
            usort($ret, function($a, $b) {
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
        } else {
            // IO Mode
            $c=new OutdatedContestModel();
            return $c->contestRankCache($this->cid);
        }
    }

    public function clarifications()
    {
        return $this->hasMany('App\Models\Eloquent\ContestClarification', 'cid', 'cid');
    }

    public function challenges()
    {
        return $this->hasMany(ContestProblem::class, 'cid', 'cid')->orderBy('number', 'asc');
    }

    public function problems()
    {
        return $this->belongsToMany(Problem::class, 'contest_problem', 'cid', 'pid', 'cid' ,'pid');
    }

    public function submissions()
    {
        return $this->hasMany('App\Models\Eloquent\Submission', 'cid', 'cid');
    }

    public function group()
    {
        return $this->hasOne('App\Models\Eloquent\Group', 'gid', 'gid');
    }

    public function getFrozenTimeAttribute()
    {
        $end_time=strtotime($this->end_time);
        return $end_time-$this->froze_length;
    }

    public function getIsEndAttribute()
    {
        return strtotime($this->end_time)<time();
    }

    public function isJudgingComplete()
    {
        return $this->submissions->whereIn('verdict', ['Waiting', 'Pending'])->count()==0;
    }
}
