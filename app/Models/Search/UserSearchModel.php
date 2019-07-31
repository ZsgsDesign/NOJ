<?php

namespace App\Models\Search;

use Illuminate\Database\Eloquent\Model;

class UserSearchModel extends Model
{
    protected $table='users';

    public function search($key)
    {
        $result = [];
        //email find
        $ret = self::where('email',$key)
            ->select('id','avatar', 'name', 'describes', 'professional_rate')
            ->first();
        if(!empty($ret)){
            $result[] = $ret;
        }
        //user name find
        if(strlen($key) >= 2){
            $ret = self::whereRaw('MATCH(`name`) AGAINST (? IN BOOLEAN MODE)',[$key])
                ->select('id','avatar', 'name',  'describes', 'professional_rate')
                ->get()->all();
            if(!empty($ret)){
                $result += $ret;
            }
        }
        return $result;
    }
}
