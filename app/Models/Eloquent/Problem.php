<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\Contest;
use App\Models\Services\ProblemService;
use Auth;
use Carbon;
use DB;
use Exception;
use App\Models\Traits\LikeScope;
use DateTimeInterface;
use Cache;

class Problem extends Model
{
    use LikeScope;

    protected $table = 'problem';
    protected $primaryKey = 'pid';
    const UPDATED_AT = "update_date";

    protected $casts = [
        'update_date' => 'date',
        'force_raw' => 'boolean',
        'markdown' => 'boolean',
        'hide' => 'boolean',
    ];

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

    public function online_judge()
    {
        return $this->belongsTo(OJ::class, 'OJ', 'oid');
    }

    public function contests()
    {
        return $this->belongsToMany(Contest::class, 'contest_problem', 'pid', 'cid', 'pid', 'cid');
    }

    public function challenges()
    {
        return $this->hasMany(ContestProblem::class, 'pid', 'pid');
    }

    public function getProblemStatusAttribute()
    {
        return $this->getProblemStatus();
    }

    public function getFileExtensionAttribute()
    {
        return ($this->file && filled($this->file_url)) ? pathinfo($this->file_url, PATHINFO_EXTENSION) : false;
    }

    public function getIsPdfAttribute()
    {
        return $this->file_extension == 'pdf';
    }

    public function getPublicDialectsAttribute()
    {
        return $this->dialects()->where('is_hidden', false)->get();
    }

    public function getParseMarkdownAttribute()
    {
        return !$this->force_raw && $this->markdown;
    }

    public function getIsHiddenAttribute()
    {
        return $this->hide;
    }

    public function getConflictContestsAttribute()
    {
        $conflicts = Cache::tags(['problem', 'conflict_contests'])->get($this->pid);
        if (is_null($conflicts)) {
            $conflicts = $this->contests()->where("end_time", ">", Carbon::now())->where('verified', true)->get()->pluck(['cid'])->all();
            Cache::tags(['problem', 'conflict_contests'])->put($this->pid, $conflicts, 60);
        }
        return $conflicts;
    }

    public function checkContestBlockade(int $currentContestId = 0): bool
    {
        return filled($this->conflict_contests) && !collect($this->conflict_contests)->contains($currentContestId);
    }

    public function getAvailableCompilersAttribute()
    {
        return blank($this->special_compiler) ? $this->online_judge->available_compilers : $this->online_judge->available_compilers->whereIn('coid', explode(',', $this->special_compiler));
    }

    public function getStatisticsAttribute()
    {
        return $this->getStatistics();
    }

    public function getLastSubmission($userId)
    {
        return ProblemService::getLastSubmission($this, $userId);
    }

    public function getPreferableCompiler($userId)
    {
        return ProblemService::getPreferableCompiler($this, $userId);
    }

    public function getStatistics(int $currentContestId = 0)
    {
        return ProblemService::getStatistics($this, $currentContestId);
    }

    public function getProblemStatus($userID = null, $contestID = null, Carbon $till = null)
    {
        return ProblemService::getProblemStatus($this, $userID, $contestID, $till);
    }

    public function getDialect(int $dialectId = 0): array
    {
        if ($dialectId != 0) {
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
            }
        }
        if (!isset($dialect)) {
            $dialect = [
                'title' => $this->title,
                'description' => $this->description,
                'input' => $this->input,
                'output' => $this->output,
                'note' => $this->note,
            ];
            $parseMarkdown = $this->parse_markdown;
        }
        if ($parseMarkdown) {
            foreach (['description', 'input', 'output', 'note'] as $field) {
                $dialect[$field] = clean(convertMarkdownToHtml($dialect[$field]));
            }
        }
        $dialect['is_blank'] = blank($dialect['description']) && blank($dialect['input']) && blank($dialect['output']) && blank($dialect['note']);
        return $dialect;
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
