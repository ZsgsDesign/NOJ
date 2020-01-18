<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;
    protected $table='message';

    /**
     * @access public
     * @param array $config the config of the message. must including sender,receiver,title,content. //TODO:Can contain reply.
     * @return boolean the result.
     */
    public static function send($config)
    {
        if(!empty($config['type'])){
            if($config['type'] == 1) { //to a leader that member apply to join the group
                $messages = Message::where([
                    'receiver' => $config['receiver'],
                    'type'     => $config['type'],
                    'unread'   => 1
                ])->get();
                if(!empty($messages)) {
                    foreach($messages as $message) {
                        $data = json_decode($message->data,true);
                        if($data['group']['gcode'] == $config['data']['group']['gcode']) {
                            array_push($data['user'],$config['data']['user'][0]);
                            $message->data = json_encode($data);
                            $message->save();
                            return true;
                        }
                    }
                }
            }elseif ($config['type'] == 2) { //to a leader that member agree to join the group
                $messages = Message::where([
                    'receiver' => $config['receiver'],
                    'type'     => $config['type'],
                    'unread'   => 1
                ])->get();
                if(!empty($messages)) {
                    foreach($messages as $message) {
                        $data = json_decode($message->data,true);
                        if($data['group'] == $config['data']['group']) {
                            array_push($data['user'],$config['data']['user'][0]);
                            $message->data = json_encode($data);
                            $message->save();
                            return true;
                        }
                    }
                }
            }elseif ($config['type'] == 3) { //to a person that solution was passed
                $message = Message::where([
                    'receiver' => $config['receiver'],
                    'type'     => $config['type'],
                    'unread'   => 1
                ])->first();
                if(!empty($message)) {
                    $data = json_decode($message->data,true);
                    array_push($data,$config['data']);
                    $message->data = json_encode($data);
                    $message->save();
                    return true;
                }
            }elseif ($config['type'] == 4) { //to a person that solution was blocked
                $message = Message::where([
                    'receiver' => $config['receiver'],
                    'type'     => $config['type'],
                    'unread'   => 1
                ])->first();
                if(!empty($message)) {
                    $data = json_decode($message->data,true);
                    array_push($data,$config['data']);
                    $message->data = json_encode($data);
                    $message->save();
                    return true;
                }
            }
        }
        $message = new Message;
        $message->sender = $config['sender'];
        $message->receiver = $config['receiver'];
        $message->title = $config['title'];
        if(isset($config['data']) && isset($config['type'])){
            $message->type = $config['type'] ?? null;
            $message->data = json_encode($config['data']);
        }else{
            $message->content = $config['content'];
        }
        /*
        if(isset($config['reply'])){
            $message->reply = $config['reply'];
        }
        if(isset($config['allow_reply'])){
            $message->reply = $config['allow_reply'];
        }
        */
        $message->official = 1;
        $message->save();
        return true;
    }

    /**
     * to get a unread message liist of a user
     *
     * @access public
     * @param integer message receiver id
     * @return array the result.
     */
    public static function unread($uid)
    {
        return static::where([
            'receiver' => $uid,
            'unread' => 1,
        ])
        ->orderByDesc('created_at')
        ->get()->all();
    }


    /**
     * to get a user's all messages' pagnition
     *
     * @access public
     * @param integer id
     * @return array result.
     */
    public static function list($uid)
    {

        return static::with('sender_user')
            ->where('receiver',$uid)
            ->orderBy('unread','desc')
            ->orderBy('updated_at','desc')
            ->paginate(15);
    }

    /**
     * to get a message's detail.
     *
     * @access public
     * @param integer id
     * @return array result.
     */
    public static function read($mid)
    {
        $message = static::with('sender_user')->find($mid);
        if(!empty($message)){
            $message->unread = 0;
            $message->save();
        }
        return $message;
    }

    /**
     * to get a message's detail.
     *
     * @access public
     * @param integer uid
     * @return integer result.
     */
    public static function allRead($uid)
    {
        return static::where('receiver',$uid)
            ->update(['unread' => 0]);
    }

    /**
     * to remove a user's all read-ed message.
     *
     * @access public
     * @param integer uid
     * @return integer result.
     */
    public static function removeAllRead($uid)
    {
        return static::where([
            'receiver' => $uid,
            'unread' => 0
        ])->delete();
    }

    /**
     * to soft delete the message
     *
     * @access public
     * @param integer|array the id of the message or the array with ids of the messages.
     * @return bool result.
     */
    public static function remove($messages)
    {
        $del_count = 0;
        if(is_array($messages)){
            foreach ($messages as $mid) {
                $message = static::find($mid);
                if(!empty($message)){
                    $message->delete();
                    $del_count ++;
                }
            }
        }else{
            $message = static::find($messages);
            if(!empty($message)){
                $message->delete();
                $del_count ++;
            }
        }
        return $del_count;
    }

    public function getContentAttribute($value)
    {
        if(!empty($this->type)){
            $data = json_decode($this->data,true);
            $content = '';
            if($this->type == 1) {
                foreach($data['user'] as $user) {
                    $content .= "[{$user['name']}]({$user['url']}), ";
                }
                $content = substr($content,0,strlen($content)-2);
                $content .= " want to join your group [{$data['group']['name']}]({$data['group']['url']})";
                return $content;
            }elseif($this->type == 2) {
                foreach($data['user'] as $user) {
                    $content .= "[{$user['name']}]({$user['url']}), ";
                }
                $content = substr($content,0,strlen($content)-2);
                $content .= " have agreed to join your group [{$data['group']['name']}]({$data['group']['url']})";
                return $content;
            } //todo
        }else{
            return $value;
        }
    }

    public function sender_user()
    {
        return $this->belongsTo('App\Models\Eloquent\UserModel','sender','id');
    }

    public function receiver_user()
    {
        return $this->belongsTo('App\Models\Eloquent\UserModel','receiver','id');
    }
}
