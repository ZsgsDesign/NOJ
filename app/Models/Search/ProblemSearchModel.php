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
        $result=[];
        if (strlen($key)>=2) {
            $ret=self::where('pcode', $key)
                ->orWhereRaw('MATCH(`title`) AGAINST (? IN BOOLEAN MODE)', [$key])
                ->select('pid', 'pcode', 'title')
                ->limit(120)
                ->get()->all();
            if (!empty($ret)) {
                $result+=$ret;
            }
        }
        $problemModel=new ProblemModel();
        foreach ($result as $p_index => $p) {
            if ($problemModel->isBlocked($p['pid']) || $problemModel->isHidden($p["pid"])) {
                unset($result[$p_index]);
            }
        }
        return $result;
    }
}
