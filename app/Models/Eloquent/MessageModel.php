<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class MessageModel extends Model
{
    protected $table='message';

    /**
     * @access public
     * @param array $config the config of the message. must including sender,receiver,content. //TODO:Can contain reply.
     * @return array the result. has 'result' ['ret' => 'success/faild', 'message' => null/'faild reason'].
     */
    public static function send($config)
    {
        $message = new MessageModel;
        $message->sender = $config['sender'];
        $message->receiver = $config['receiver'];
        $message->content = $config['content'];
        if(isset($config['reply'])){
            $message->reply = $config['reply'];
        }
        $message->save();
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
            ->ordeyByDesc('message.created_at')
            ->select(
                'users.name as sender_name',
                'users.avatar as sender_avatar',
                'message.title as title',
                'message.created_at as time')
            ->paginate(30);
    }
}
