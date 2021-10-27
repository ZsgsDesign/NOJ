<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\Problem;

class ProblemSolution extends Model
{
    protected $table='problem_solution';
    protected $primaryKey='psoid';

    public function problem() {
        return $this->belongTo('App\Models\Eloquent\Problem', 'pid', 'pid');
    }

    public static function boot()
    {
        parent::boot();
        static::updating(function($model) {
            $problem = Problem::findOrFail($model->pid);
            if($model->original['audit'] != $model->audit) {
                if($model->audit == 1) {
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
                } elseif($model->audit == 2) {
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
