<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProblemModel as OutdatedProblemModel;
use Illuminate\Support\Facades\DB;

class ContestModel extends Model
{
    protected $table='contest';
    protected $primaryKey='cid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    public function problems()
    {
        return $this->hasMany('App\Models\Eloquent\ContestProblemModel', 'cid');
    }

    public function submissions()
    {
        return $this->hasMany('App\Models\Eloquent\SubmissionModel', 'cid');
    }

    public static function getProblemSet($cid, $renderLatex=false)
    {
        $ret=[];
        $problemset=ContestProblemModel::where('cid', $cid)->orderBy('number','asc')->get();
        foreach($problemset as $problem){
            $problemDetail=ProblemModel::find($problem->pid);
            $problemRet=(new OutdatedProblemModel())->detail($problemDetail->pcode);
            if ($renderLatex){
                foreach (['description','input','output','note'] as $section){
                    $problemRet['parsed'][$section]=latex2Image($problemRet['parsed'][$section]);
                }
            }
            $problemRet['index']=$problem->ncode;
            $problemRet['testcases']=$problemRet['samples'];
            unset($problemRet['samples']);
            $ret[]=$problemRet;
        }
        return $ret;
    }

    public function isJudgingComplete()
    {
        return $this->submissions->whereIn('verdict',['Waiting','Pending'])->count()==0;
    }
}
