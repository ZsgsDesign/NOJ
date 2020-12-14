<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Submission\SubmissionModel as OutdatedSubmissionModel;
use Auth;

class Problem extends Model
{
    protected $table='problem';
    protected $primaryKey='pid';
    const DELETED_AT=null;
    const UPDATED_AT="update_date";
    const CREATED_AT=null;

    public function submissions()
    {
        return $this->hasMany('App\Models\Eloquent\Submission','pid','pid');
    }

    public function problemSamples()
    {
        return $this->hasMany('App\Models\Eloquent\ProblemSample','pid','pid');
    }

    public function getProblemStatusAttribute()
    {
        if(Auth::check()){
            $prob_status=(new OutdatedSubmissionModel())->getProblemStatus($this->pid, Auth::user()->id);
            if (empty($prob_status)) {
                return [
                    "icon"=>"checkbox-blank-circle-outline",
                    "color"=>"wemd-grey-text"
                ];
            } else {
                return [
                    "icon"=>$prob_status["verdict"]=="Accepted" ? "checkbox-blank-circle" : "cisco-webex",
                    "color"=>$prob_status["color"]
                ];
            }
        } else {
            return [
                "icon"=>"checkbox-blank-circle-outline",
                "color"=>"wemd-grey-text"
            ];
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
