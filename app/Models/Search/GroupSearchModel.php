<?php

namespace App\Models\Search;

use Illuminate\Database\Eloquent\Model;
use DB;

class GroupSearchModel extends Model
{
    protected $table='group';
    protected $primaryKey='gid';

    public function search($key)
    {
        $result=[];
        //group name or gcode find
        if (strlen($key)>=2) {
            $ret=self::where(function($query) use ($key){
                $query->whereRaw('MATCH(`name`) AGAINST (? IN BOOLEAN MODE)', [$key])
                    ->orWhere('gcode', $key);
                })
                ->where('public', 1)
                ->select('gid', 'gcode', 'img', 'name', 'description')
                ->limit(120)
                ->get()->all();
            if (!empty($ret)) {
                $result+=$ret;
            }
        }

        return $result;
    }
}
