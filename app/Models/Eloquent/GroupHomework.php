<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon;
use DateTimeInterface;
use Exception;
use Cache;
use Log;
use DB;
use Throwable;

class GroupHomework extends Model
{
    use HasFactory;

    public function group()
    {
        return $this->belongsTo('App\Models\Eloquent\Group', 'group_id', 'gid');
    }

    public function problems()
    {
        return $this->hasMany('App\Models\Eloquent\GroupHomeworkProblem');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getStatisticsAttribute()
    {
        $cachedStatistics = Cache::tags(['homework', 'statistics'])->get($this->id);

        if (blank($cachedStatistics)) {
            $cachedStatistics = $this->cacheStatistics();
        }

        if ($cachedStatistics === false) {
            return null;
        }

        $cachedStatistics['timestamp'] = Carbon::createFromTimestamp($cachedStatistics['timestamp']);

        return $cachedStatistics;
    }

    public function cacheStatistics()
    {
        try {
            $statistics = [];
            $homeworkProblems = $this->problems->sortBy('order_index');
            $users = $this->group->members()->where('role', '>=', 1)->get();
            $userIDArr = $users->pluck('uid');
            $defaultVerdict = [];

            foreach ($homeworkProblems as $homeworkProblem) {
                $statistics['problems'][] = [
                    'pid' => $homeworkProblem->problem_id,
                    'readable_name' => $homeworkProblem->problem->readable_name,
                ];
                $defaultVerdict[$homeworkProblem->problem_id] = [
                    "icon" => "checkbox-blank-circle-outline",
                    "color" => "wemd-grey-text"
                ];
            }

            foreach ($users as $user) {
                $statistics['data'][$user->uid] = [
                    'name' => $user->user->name,
                    'nick_name' => blank($user->nick_name) ? null : $user->nick_name,
                    'solved' => 0,
                    'attempted' => 0,
                    'verdict' => $defaultVerdict
                ];
            }

            $endedAt = Carbon::parse($this->ended_at);

            foreach ($homeworkProblems as $homeworkProblem) {
                $userProbIDArr = [];

                foreach ($homeworkProblem->problem->users_latest_submission($userIDArr->diff($userProbIDArr), null, $endedAt, ['Accepted'])->get() as $acceptedRecord) {
                    $statistics['data'][$acceptedRecord['uid']]['verdict'][$homeworkProblem->problem_id] = [
                        "icon" => "checkbox-blank-circle",
                        "color" => $acceptedRecord['color']
                    ];
                    $statistics['data'][$acceptedRecord['uid']]['solved']++;
                    $userProbIDArr[] = $acceptedRecord['uid'];
                }

                foreach ($homeworkProblem->problem->users_latest_submission($userIDArr->diff($userProbIDArr), null, $endedAt, ['Partially Accepted'])->get() as $acceptedRecord) {
                    $statistics['data'][$acceptedRecord['uid']]['verdict'][$homeworkProblem->problem_id] = [
                        "icon" => "cisco-webex",
                        "color" => $acceptedRecord['color']
                    ];
                    $statistics['data'][$acceptedRecord['uid']]['attempted']++;
                    $userProbIDArr[] = $acceptedRecord['uid'];
                }

                foreach ($homeworkProblem->problem->users_latest_submission($userIDArr->diff($userProbIDArr), null, $endedAt)->get() as $acceptedRecord) {
                    $statistics['data'][$acceptedRecord['uid']]['verdict'][$homeworkProblem->problem_id] = [
                        "icon" => "cisco-webex",
                        "color" => $acceptedRecord['color']
                    ];
                    $statistics['data'][$acceptedRecord['uid']]['attempted']++;
                    $userProbIDArr[] = $acceptedRecord['uid'];
                }
            }

            usort($statistics['data'], function ($a, $b) {
                return $b["solved"] == $a["solved"] ? $b["attempted"] <=> $a["attempted"] : $b["solved"] <=> $a["solved"];
            });

            $statistics['timestamp'] = time();
            Cache::tags(['homework', 'statistics'])->put($this->id, $statistics, 360);
            return $statistics;
        } catch (Exception $e) {
            Log::alert($e);
            return false;
        }
    }

    public function sendMessage()
    {
        DB::beginTransaction();
        try {
            foreach($this->group->members()->where('role', '>', '0')->get() as $member) {
                sendMessage([
                    'sender'   => config('app.official_sender'),
                    'receiver' => $member->uid,
                    'title'    => __('message.homework.new.title'),
                    'type'     => 5,
                    'level'    => 1,
                    'data'     => [
                        'homework' => [[
                            'id' => $this->id,
                            'gcode' => $this->group->gcode,
                            'title' => $this->title
                        ]]
                    ]
                ]);
            }
            DB::commit();
        } catch (Throwable $e) {
            Log::error($e);
            DB::rollback();
        }
    }
}
