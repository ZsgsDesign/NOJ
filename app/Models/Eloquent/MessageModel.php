<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageModel extends Model
{
    use SoftDeletes;
    protected $table='message';

    /**
     * @access public
     * @param array $config the config of the message. must including sender,receiver,title,content. //TODO:Can contain reply.
     * @return array the result. has 'result' ['ret' => 'success/faild', 'message' => null/'faild reason'].
     */
    public static function send($config)
    {
        $message = new MessageModel;
        $message->sender = $config['sender'];
        $message->receiver = $config['receiver'];
        $message->title = $config['title'];
        $message->content = $config['content'];
        if(isset($config['reply'])){
            $message->reply = $config['reply'];
        }
        if(isset($config['allow_reply'])){
            $message->reply = $config['allow_reply'];
        }
        if(isset($config['official'])){
            $message->official = $config['official'];
        }
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
     * to check if a message allows replies
     *
     * @access public
     * @param integer id
     * @return array result.
     */
    public static function allowReply($id)
    {
        $message = static::where('id',$id)->first();
        if(empty($message)){
            return false;
        }
        return $message['allow_reply'] ? true : false;
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
        return static::join('users','message.sender','=','users.id')
            ->where('receiver',$uid)
            ->select(
                'message.id as id',
                'users.name as sender_name',
                'users.avatar as sender_avatar',
                'message.title as title',
                'message.created_at as time',
                'message.official as official',
                'message.unread as unread'
            )
            ->orderByDesc('message.created_at')
            ->orderByDesc('message.unread')
            ->paginate(30);
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
        $message = static::find($mid);
        if(!empty($message)){
            $message->unread = 0;
            $message->save();
        }
        return static::join('users','message.sender','=','users.id')
            ->where('message.id',$mid)
            ->select(
                'message.id as id',
                'users.name as sender_name',
                'users.avatar as sender_avatar',
                'message.title as title',
                'message.content as content',
                'message.created_at as time',
                'message.official as official',
                'message.unread as unread',
                'message.allow_reply as allow_reply'
            )->first();
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
}
