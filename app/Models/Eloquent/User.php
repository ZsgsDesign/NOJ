<?php

namespace App\Models\Eloquent;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Models\Eloquent\UserExtra;
use PDO;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

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
        'password', 'remember_token', 'tokens'
    ];

    public function banneds() {
        return $this->hasMany('App\Models\Eloquent\UserBanned');
    }

    public function announcements() {
        return $this->hasMany('App\Models\Eloquent\Announcement');
    }

    public function permissions() {
        return $this->hasMany('App\Models\Eloquent\UserPermission');
    }

    public function imagehostings() {
        return $this->hasMany('App\Models\Eloquent\Tool\ImageHosting');
    }

    public function extras() {
        return $this->hasMany('App\Models\Eloquent\UserExtra', 'uid');
    }

    public function hasPermission($permissionID){
        return ($this->permissions()->where(['permission_id'=>$permissionID])->count())>0;
    }

    public function hasIndependentPassword(){
        return filled($this->password);
    }

    public function hasIndependentEmail(){
        return !in_array(explode('@', $this->email)[1], ['temporarily.email']) && !$this->contest_account;
    }

    public function isIndependent(){
        return $this->hasIndependentPassword() && $this->hasIndependentEmail();
    }

    /**
     * To get some extra info of a user.
     *
     * @param string|array $need An array is returned when an array is passed in, Only one value is returned when a string is passed in.
     * @param int|null $secretLevel the secret level this query currently running on
     * @return string|array $result
     */
    public function getExtra($need, $secretLevel = 0){
        $ret = $this->extras()->orderBy('key')->get()->toArray();
        $result = [];
        if(!empty($ret)){
            if(is_string($need)){
                foreach ($ret as $value) {
                    if(empty($value['secret_level']) || $value['secret_level'] <= $secretLevel){
                        $keyName = UserExtra::$extraMapping[$value['key']] ?? 'unknown';
                        if($keyName == $need){
                            return $value['value'];
                        }
                    }
                }
                return null;
            }else{
                foreach ($ret as $value) {
                    if(empty($value['secret_level']) || $value['secret_level'] <= $secretLevel){
                        $keyName = UserExtra::$extraMapping[$value['key']] ?? 'unknown';
                        if(in_array($keyName, $need)){
                            $result[$keyName] = $value['value'];
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * To set some extra info of a user.
     *
     * @param string $keyName insert when key not found or update when key exists. Only values declared in UserExtra Model are accepted
     * @param string|null $value the extra info will be delete when value is null
     * @param int|null $secretLevel the secret level this query currently running on
     * @return mixed $result
     */
    public function setExtra($keyName, $value = null, $secretLevel = -1){
        $key = array_search($keyName, UserExtra::$extraMapping);
        if($key === false){
            return false;
        }
        $ret = $this->extras()->where('key', $key)->limit(1)->get()->toArray();
        if(!empty($ret)){
            unset($ret['id']);
            if(!is_null($value)){
                $ret['value'] = $value;
            }else{
                $this->extras()->where('key', $key)->delete();
                return true;
            }
            if($secretLevel != -1){
                $ret['secret_level'] = $secretLevel;
            }
            return $this->extras()->where('key', $key)->update($ret);
        }else{
            if($value === null){
                return true;
            }
            return $this->extras()->create([
                'key' => $key,
                'value' => $value,
                'secret_level' => $secretLevel == -1 ? 0 : $secretLevel,
            ])->id;
        }
    }

    public function getSocialiteInfo($secretLevel = -1)
    {
        $socialites = [];
        foreach (UserExtra::$socialite_support as $key => $value) {
            $id_keyname = $key.'_id';
            $id = $this->getExtra($id_keyname);
            if(!empty($id)){
                $info = [
                    'id' => $id,
                ];
                foreach ($value as $info_name) {
                    $info_temp = $this->getExtra($key.'_'.$info_name);
                    if($info_temp !== null){
                        $info[$info_name] = $info_temp;
                    }
                }
                $socialites[$key] = $info;
            }
        }

        return $socialites;
    }
}
