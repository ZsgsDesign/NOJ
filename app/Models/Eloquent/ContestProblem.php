<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\Compiler;
use App\Models\Services\ProblemService;

class ContestProblem extends Model
{
    protected $table='contest_problem';
    protected $primaryKey='cpid';

    protected $fillable=[
        'number', 'ncode', 'pid', 'alias', 'points'
    ];

    private array $_presenter = [];

    public function contest()
    {
        return $this->belongsTo(Contest::class, 'cid', 'cid');
    }

    public function problem()
    {
        return $this->belongsTo(Problem::class, 'pid', 'pid');
    }

    public function submissions()
    {
        return $this->problem->submissions()->where('cid', $this->contest->cid);
    }

    public function getStatisticsAttribute()
    {
        return $this->_presenter['statistics'] ?? $this->_presenter['statistics'] = ProblemService::getStatistics($this->problem, $this->cid);
    }

    public function getLastSubmission($userId)
    {
        return ProblemService::getLastSubmission($this->problem, $userId, $this->cid);
    }

    public function getPreferableCompiler($userId)
    {
        return ProblemService::getPreferableCompiler($this->problem, $userId, $this->cid);
    }

    //This should be a repository...or service function ?
    public function userStatus($user)
    {
        $ret=[
            'solved'             => 0,
            'solved_time'        => '',
            'solved_time_parsed' => '',
            'wrong_doings'       => 0,
            'color'              => '',
        ];
        $ac_record=$this->ac_record($user);
        if (!empty($ac_record[0])) {
            $ret['solved']=1;
            $ret['solved_time']=$ac_record[0]->submission_date-strtotime($this->contest->begin_time);
            $ret['solved_time_parsed']=formatProblemSolvedTime($ret['solved_time']);
            $ret['wrong_doings']=$ac_record[2];
            $ret['color']=$ac_record[1] ? 'wemd-green-text' : 'wemd-teal-text';
        } else {
            $ret['wrong_doings']=$ac_record[2];
        }
        return $ret;
    }

    public function ac_record($user)
    {
        $frozen_time=$this->contest->frozen_time;
        $user_ac=$this->submissions()->where([
            'uid'     => $user->id,
            'verdict' => 'Accepted'
        ])->where("submission_date", "<", $frozen_time)->orderBy('submission_date', 'asc')->first();

        $other_ac=1;
        $wrong_trys=0;
        if (!empty($user_ac)) {
            $other_ac=$this->submissions()
                ->where('verdict', 'Accepted')
                ->where('submission_date', '<', $user_ac->submission_date)
                ->count();
            $wrong_trys=$this->submissions()->where([
                    'uid'     => $user->id,
                ])->whereIn('verdict', [
                    'Runtime Error',
                    'Wrong Answer',
                    'Time Limit Exceed',
                    'Real Time Limit Exceed',
                    'Memory Limit Exceed',
                    'Presentation Error',
                    'Output Limit Exceeded'
                ])->where('submission_date', '<', $user_ac->submission_date)->count();
        } else {
            $wrong_trys=$this->submissions()->where([
                'uid'     => $user->id,
            ])->whereIn('verdict', [
                'Runtime Error',
                'Wrong Answer',
                'Time Limit Exceed',
                'Real Time Limit Exceed',
                'Memory Limit Exceed',
                'Presentation Error',
                'Output Limit Exceeded'
            ])->where('submission_date', '<', $this->contest->frozen_time)->count();
        }
        return [
            $user_ac,
            $other_ac,
            $wrong_trys
        ];
    }
}
