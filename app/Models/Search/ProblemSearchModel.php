<?php

namespace App\Models\Search;

use App\Models\ProblemModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProblemSearchModel extends Model
{
    protected $table='problem';
    protected $primaryKey='pid';

    public function search($key)
    {
        $result = [];
        //problem code find
        $ret = self::where('pcode', $key)
            ->select('pcode', 'title')
            ->first();
        if(!empty($ret)){
            array_push($result,$ret);
        }
        //problem name find
        if(strlen($key) >= 2){
            $ret = self::whereRaw('MATCH(`title`) AGAINST (?)',[$key])
                ->select('pcode', 'title')
                ->get()->all();
            if(!empty($ret)){
                $result += $ret;
            }
        }
        $problemModel = new ProblemModel();
        foreach ($result as $p_index => $p) {
            if($problemModel->isBlocked($p['pid'])){
                unset($result[$p_index]);
            }
        }
        return $result;
    }
}
