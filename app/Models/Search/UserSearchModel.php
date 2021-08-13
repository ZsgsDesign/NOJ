<?php

namespace App\Models\Search;

use Illuminate\Database\Eloquent\Model;

class UserSearchModel extends Model
{
    protected $table='users';

    public function search($key)
    {
        $result=[];
        if (strlen($key)>=2) {
            $ret=self::where('email', $key)
                ->orWhereRaw('MATCH(`name`) AGAINST (? IN BOOLEAN MODE)', [$key])
                ->select('id', 'avatar', 'name', 'describes', 'professional_rate')
                ->orderBy('professional_rate', 'DESC')
                ->limit(120)
                ->get()->all();
            if (!empty($ret)) {
                $result+=$ret;
            }
        }
        return $result;
    }
}
