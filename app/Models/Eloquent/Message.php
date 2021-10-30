<?php

namespace App\Models\Eloquent;

use App\Models\Eloquent\Messager\GlobalRankMessager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Eloquent\Messager\UniversalMessager;
use App\Models\Eloquent\Messager\GroupMemberMessager;
use App\Models\Eloquent\Messager\SolutionStatusMessager;
use App\Models\Eloquent\Messager\HomeworkMessager;

class Message extends Model
{
    use SoftDeletes;

    protected $table = 'message';

    protected $with = ['sender_user', 'receiver_user'];

    public $levelMapping = [
        0 => 'default',
        1 => 'info',
        2 => 'warning',
        3 => 'danger',
        4 => 'question',
        5 => 'success',
    ];

    /**
     * @access public
     * @param array $config the config of the message. must including sender,receiver,title,content. //TODO:Can contain reply.
     * @return boolean the result.
     */
    public static function send($config)
    {
        if (filled($config['type'])) {
            switch (intval($config['type'])) {
                case 1:
                    // to a leader that member apply to join the group
                    return GroupMemberMessager::sendApplyJoinMessageToLeader($config);
                    break;

                case 2:
                    // to a leader that member agree to join the group
                    return GroupMemberMessager::sendAgreedJoinMessageToLeader($config);
                    break;

                case 3:
                    // to a person that solution was passed
                    return SolutionStatusMessager::sendSolutionPassedMessageToUser($config);
                    break;

                case 4:
                    // to a person that solution was rejected
                    return SolutionStatusMessager::sendSolutionRejectedMessageToUser($config);
                    break;

                case 5:
                    // to a person that received new homework
                    return HomeworkMessager::sendNewHomeworkMessageToUser($config);
                    break;

                case 6:
                    // to a person that global rank in or out top 100
                    return GlobalRankMessager::sendRankInOutOneHundredMessageToUser($config);
                    break;

                case 7:
                    // to a person that got invited to a group
                    return GroupMemberMessager::sendInvitedMessageToUser($config);
                    break;

                default:
                    // unregistered type falls back to universal message sender
                    return UniversalMessager::sendUniversalMessage($config);
                    break;
            }
        }
        return UniversalMessager::sendUniversalMessage($config);
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
            'unread' => true,
        ])->orderByDesc('created_at')->get()->all();
    }

    public static function listAll($userID)
    {
        return Message::where('receiver', $userID)->orderBy('unread', 'desc')->orderBy('updated_at', 'desc')->paginate(15);
    }

    public function read()
    {
        if ($this->unread) {
            $this->unread = false;
            $this->save();
        }
        return $this;
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
        return static::where('receiver', $uid)->update([
            'unread' => false
        ]);
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
            'unread' => false
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
        if (is_array($messages)) {
            foreach ($messages as $mid) {
                $message = static::find($mid);
                if (filled($message)) {
                    $message->delete();
                    $del_count++;
                }
            }
        } else {
            $message = static::find($messages);
            if (filled($message)) {
                $message->delete();
                $del_count++;
            }
        }
        return $del_count;
    }

    public function getLevelStringAttribute()
    {
        if (isset($this->levelMapping[$this->level])) {
            return $this->levelMapping[$this->level];
        } else {
            return $this->levelMapping[0];
        }
    }

    public function getContentAttribute($value)
    {
        if (filled($this->type)) {
            $data = json_decode($this->data, true);
            $data['receiver'] = $this->receiver_user;
            $data['sender'] = $this->sender_user;

            switch (intval($this->type)) {
                case 1:
                    // to a leader that member apply to join the group
                    return GroupMemberMessager::formatApplyJoinMessageToLeader($data);
                    break;

                case 2:
                    // to a leader that member agree to join the group
                    return GroupMemberMessager::formatAgreedJoinMessageToLeader($data);
                    break;

                case 3:
                    // to a person that solution was passed
                    return SolutionStatusMessager::formatSolutionPassedMessageToUser($data);
                    break;

                case 4:
                    // to a person that solution was rejected
                    return SolutionStatusMessager::formatSolutionRejectedMessageToUser($data);
                    break;

                case 5:
                    // to a person that received new homework
                    return HomeworkMessager::formatNewHomeworkMessageToUser($data);
                    break;

                case 6:
                    // to a person that global rank in or out top 100
                    return GlobalRankMessager::formatRankInOutOneHundredMessageToUser($data);
                    break;

                case 7:
                    // to a person that got invited to a group
                    return GroupMemberMessager::formatInvitedMessageToUser($data);
                    break;

                default:
                    // unregistered type falls back to universal message formatter
                    return $value;
                    break;
            }

        } else {
            return $value;
        }
    }

    public function sender_user()
    {
        return $this->belongsTo('App\Models\Eloquent\User', 'sender', 'id');
    }

    public function receiver_user()
    {
        return $this->belongsTo('App\Models\Eloquent\User', 'receiver', 'id');
    }
}
