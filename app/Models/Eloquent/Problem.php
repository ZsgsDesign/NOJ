<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\Contest;
use Auth;
use Carbon;
use DB;
use Exception;
use App\Models\Traits\LikeScope;
use DateTimeInterface;

class Problem extends Model
{
    use LikeScope;

    protected $table = 'problem';
    protected $primaryKey = 'pid';
    const UPDATED_AT = "update_date";

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getReadableNameAttribute()
    {
        return $this->pcode . '. ' . $this->title;
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'pid', 'pid');
    }

    public function samples()
    {
        return $this->hasMany(ProblemSample::class, 'pid', 'pid');
    }

    public function dialects()
    {
        return $this->hasMany(ProblemDialect::class, 'problem_id', 'pid');
    }

    public function solutions()
    {
        return $this->hasMany(ProblemSolution::class, 'pid', 'pid');
    }

    public function tags()
    {
        return $this->hasMany(ProblemTag::class, 'pid', 'pid');
    }

    public function homework_problems()
    {
        return $this->hasMany(GroupHomeworkProblem::class, 'problem_id', 'pid');
    }

    public function onlinejudge()
    {
        return $this->belongsTo(OJ::class, 'OJ', 'oid');
    }

    public function getProblemStatusAttribute()
    {
        return $this->getProblemStatus();
    }

    public function getPublicDialectsAttribute()
    {
        return $this->dialects()->where('is_hidden', false)->get();
    }

    public function getDialect($dialectId = 0): array
    {
        if ($dialectId == 0) {
            $dialect = [
                'title' => $this->title,
                'description' => $this->description,
                'input' => $this->input,
                'output' => $this->output,
                'note' => $this->note,
            ];
            $parseMarkdown = !$this->force_raw && $this->markdown;
        } else {
            $dialectInstance = $this->dialects()->where(['is_hidden' => false, 'id' => $dialectId])->first();
            $parseMarkdown = true;
            if (filled($dialectInstance)) {
                $dialect = [
                    'title' => filled($dialectInstance->title) ? $dialectInstance->title : $this->title,
                    'description' => $dialectInstance->description,
                    'input' => $dialectInstance->input,
                    'output' => $dialectInstance->output,
                    'note' => $dialectInstance->note,
                ];
            } else {
                return [];
            }
        }
        if($parseMarkdown) {
            foreach (['description', 'input', 'output', 'note'] as $field) {
                $dialect[$field] = clean(convertMarkdownToHtml($dialect[$field]));
            }
        }
        return $dialect;
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
        $endedAt = Carbon::now();

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

    public function users_latest_submission($users, $contestID = null, Carbon $till = null, $verdictFilter = [])
    {
        if (filled($contestID)) {
            $endedAt = Carbon::parse(Contest::findOrFail($contestID)->endedAt);
        }

        $lastRecordSubQuery = $this->submissions()->select('uid', DB::raw('MAX(submission_date) as submission_date'))->whereIntegerInRaw('uid', $users)->where('cid', $contestID)->groupBy('uid');

        if (filled($contestID)) {
            $lastRecordSubQuery = $lastRecordSubQuery->where("submission_date", "<", $endedAt->timestamp);
        }

        if (filled($till)) {
            $lastRecordSubQuery = $lastRecordSubQuery->where("submission_date", "<", $till->timestamp);
        }

        if (filled($verdictFilter)) {
            $lastRecordSubQuery = $lastRecordSubQuery->whereIn('verdict', $verdictFilter);
        }

        $query = DB::table(DB::raw("({$lastRecordSubQuery->toSql()}) last_sub"))->leftJoinSub(Submission::toBase(), 'submissions', function ($join) {
            $join->on('last_sub.submission_date', '=', 'submissions.submission_date')->on('last_sub.uid', '=', 'submissions.uid');
        })->select('sid', 'last_sub.submission_date as submission_date', 'last_sub.uid as uid', 'verdict', 'color')->orderBy('uid', 'ASC');

        return $query->mergeBindings($lastRecordSubQuery->toBase());
    }
}
