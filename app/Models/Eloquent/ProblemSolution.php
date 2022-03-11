<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\Problem;

class ProblemSolution extends Model
{
    protected $table = 'problem_solution';
    protected $primaryKey = 'psoid';

    protected $fillable = [
        'uid', 'pid', 'content'
    ];
    protected $casts = [
        'audit' => 'integer',
    ];

    public function problem()
    {
        return $this->belongsTo(Problem::class, 'pid', 'pid');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function inteliAudit(): int
    {
        if (strpos($this->content, '```') !== false) {
            $latestSolution = $this->author->solutions()->whereNotIn('audit', [0])->orderByDesc('updated_at')->first();
            if (filled($latestSolution) && $latestSolution->audit == 1) {
                return $this->audit = 1;
            }
        }
        return $this->audit = 0;
    }

    public static function boot()
    {
        parent::boot();
        static::updating(function ($model) {
            $problem = Problem::findOrFail($model->pid);
            if ($model->original['audit'] != $model->audit) {
                if ($model->audit == 1) {
                    // passed
                    sendMessage([
                        'sender'   => config('app.official_sender'),
                        'receiver' => $model->uid,
                        'title'    => __('message.solution.accepted.title'),
                        'type'     => 3,
                        'level'    => 5,
                        'data'     => [
                            'problem' => [[
                                'pcode' => $problem->pcode,
                                'title' => $problem->title
                            ]]
                        ]
                    ]);
                } elseif ($model->audit == 2) {
                    // declined
                    sendMessage([
                        'sender'   => config('app.official_sender'),
                        'receiver' => $model->uid,
                        'title'    => __('message.solution.declined.title'),
                        'type'     => 4,
                        'level'    => 2,
                        'data'     => [
                            'problem' => [[
                                'pcode' => $problem->pcode,
                                'title' => $problem->title
                            ]]
                        ]
                    ]);
                }
            }
        });
    }
}
