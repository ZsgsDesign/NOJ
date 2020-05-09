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
}
