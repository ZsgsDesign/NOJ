<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\Contest;
use Carbon\Carbon;
use Auth;
use DB;
use Exception;

class Problem extends Model
{
    protected $table = 'problem';
    protected $primaryKey = 'pid';
    const UPDATED_AT = "update_date";

    public function getReadableNameAttribute()
    {
        return $this->pcode . '. ' . $this->title;
    }

    public function submissions()
    {
        return $this->hasMany('App\Models\Eloquent\Submission', 'pid', 'pid');
    }

    public function problemSamples()
    {
        return $this->hasMany('App\Models\Eloquent\ProblemSample', 'pid', 'pid');
    }

    public function homework_problems()
    {
        return $this->hasMany('App\Models\Eloquent\GroupHomeworkProblem', 'problem_id', 'pid');
    }

    public function onlinejudge()
    {
        return $this->belongsTo('App\Models\Eloquent\OJ', 'OJ', 'oid');
    }

    public function getProblemStatusAttribute()
    {
        return $this->getProblemStatus();
    }

    public function getProblemStatus($userID = null, $contestID = null, Carbon $till = null)
    {
        if (blank($userID)) {
            if (Auth::guard('web')->check()) {
                $userID = Auth::guard('web')->user()->id;
            }
        }

        if (filled($userID)) {
            $probStatus = $this->getProblemStatusFromDB($userID, $contestID, $till);
            if (blank($probStatus)) {
                return [
                    "icon" => "checkbox-blank-circle-outline",
                    "color" => "wemd-grey-text"
                ];
            } else {
                return [
                    "icon" => $probStatus->verdict == "Accepted" ? "checkbox-blank-circle" : "cisco-webex",
                    "color" => $probStatus->color
                ];
            }
        } else {
            return [
                "icon" => "checkbox-blank-circle-outline",
                "color" => "wemd-grey-text"
            ];
        }
    }

    private function getProblemStatusFromDB($userID, $contestID = null, Carbon $till = null)
    {
        if (filled($contestID)) {
            try {
                $endedAt = Carbon::parse(Contest::findOrFail($contestID)->endedAt);
            } catch (Exception $e) {
                return null;
            }
        }

        // Get the very first AC record

        $acRecords = $this->submissions()->where([
            'uid' => $userID,
            'cid' => $contestID,
            'verdict' => 'Accepted'
        ]);
        if (filled($contestID)) {
            $acRecords = $acRecords->where("submission_date", "<", $endedAt->timestamp);
        }
        if (filled($till)) {
            $acRecords = $acRecords->where("submission_date", "<", $till->timestamp);
        }
        $acRecords = $acRecords->orderBy('submission_date', 'desc')->first();
        if (blank($acRecords)) {
            $pacRecords = $this->submissions()->where([
                'uid' => $userID,
                'cid' => $contestID,
                'verdict' => 'Partially Accepted'
            ]);
            if (filled($contestID)) {
                $pacRecords = $pacRecords->where("submission_date", "<", $endedAt->timestamp);
            }
            if (filled($till)) {
                $pacRecords = $pacRecords->where("submission_date", "<", $till->timestamp);
            }
            $pacRecords = $pacRecords->orderBy('submission_date', 'desc')->first();
            if (blank($pacRecords)) {
                $otherRecords = $this->submissions()->where([
                    'uid' => $userID,
                    'cid' => $contestID
                ]);
                if (filled($contestID)) {
                    $otherRecords = $otherRecords->where("submission_date", "<", $endedAt->timestamp);
                }
                if (filled($till)) {
                    $otherRecords = $otherRecords->where("submission_date", "<", $till->timestamp);
                }
                return $otherRecords->orderBy('submission_date', 'desc')->first();
            }
            return $pacRecords;
        } else {
            return $acRecords;
        }
    }

    /*     public function getSamplesAttribute()
    {
        return array_map(function($sample) {
            return [
                'sample_input' => $sample->sample_input,
                'sample_output' => $sample->sample_output,
                'sample_note' => $sample->sample_note,
            ];
        }, $this->problemSamples()->select('sample_input', 'sample_output', 'sample_note')->get()->all());
    }

    public function setSamplesAttribute($value)
    {
        return;
    } */
}
