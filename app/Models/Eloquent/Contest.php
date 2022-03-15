<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use DateTimeInterface;
use App\Models\Services\ContestService;

class Contest extends Model
{
    protected $table = 'contest';
    protected $primaryKey = 'cid';

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getParsedRuleAttribute()
    {
        $rule = ["Unknown", "ICPC", "IOI", "Custom ICPC", "Custom IOI", "Examination"];
        return $rule[$this->rule];
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if ($model->custom_icon != "" && $model->custom_icon != null && $model->custom_icon[0] != "/") {
                $model->custom_icon = "/$model->custom_icon";
            }
            if ($model->img != "" && $model->img != null && $model->img[0] != "/") {
                $model->img = "/$model->img";
            }
        });
    }

    //Repository function
    public function participants(bool $ignoreFrozen = false)
    {
        if ($this->registration) {
            $participants = ContestParticipant::where('cid', $this->cid)->get();
            $participants->load('user');
            $users = collect();
            foreach ($participants as $participant) {
                $user = $participant->user;
                $users->add($user);
            }
            return $users->unique();
        } else {
            $this->load('submissions.user');
            if ($ignoreFrozen) {
                $submissions = $this->submissions;
            } else {
                $submissions = $this->submissions()->where('submission_date', '<', $this->frozen_time)->get();
            }
            $users = collect();
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
        return ContestService::rankRefresh($this);
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
        return $this->belongsToMany(Problem::class, 'contest_problem', 'cid', 'pid', 'cid', 'pid');
    }

    public function submissions()
    {
        return $this->hasMany('App\Models\Eloquent\Submission', 'cid', 'cid')->orderBy('submission_date');
    }

    public function group()
    {
        return $this->hasOne('App\Models\Eloquent\Group', 'gid', 'gid');
    }

    public function getFrozenTimeAttribute()
    {
        return $this->frozed_at->getTimestamp();
    }

    public function getFrozedAtAttribute()
    {
        return Carbon::parse($this->end_time)->subSeconds($this->froze_length ?? 0);
    }

    public function getHasEndedAttribute()
    {
        return Carbon::parse($this->end_time)->isBefore(Carbon::now());
    }

    public function isJudgingComplete()
    {
        return $this->submissions->whereIn('verdict', ['Waiting', 'Pending'])->count() == 0;
    }

    public function generateContestAccount($ccode, $cdomain, $num, $userName = [])
    {
        return ContestService::generateContestAccount($this, $ccode, $cdomain, $num, $userName);
    }
}
