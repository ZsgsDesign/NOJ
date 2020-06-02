<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table='users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable=[
        'name', 'email', 'password', 'avatar', 'contest_account'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden=[
        'password', 'remember_token',
    ];

    public function banneds() {
        return $this->hasMany('App\Models\Eloquent\UserBanned');
    }
}
