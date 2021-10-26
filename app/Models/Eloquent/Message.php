<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Eloquent\Messager\UniversalMessager;
use App\Models\Eloquent\Messager\GroupMemberMessager;
use App\Models\Eloquent\Messager\SolutionStatusMessager;

class Message extends Model
{
    use SoftDeletes;
    protected $table = 'message';
    public $levelMapping = [
        0 => 'default',
        1 => 'info',
        2 => 'warning',
        3 => 'danger',
        4 => 'question',
        5 => 'announcement',
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
                    // to a person that solution was blocked
                    return SolutionStatusMessager::sendSolutionRejectedMessageToUser($config);
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
            ->where('receiver', $uid)
            ->orderBy('unread', 'desc')
            ->orderBy('updated_at', 'desc')
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
        if (!empty($message)) {
            $message->unread = false;
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
                if (!empty($message)) {
                    $message->delete();
                    $del_count++;
                }
            }
        } else {
            $message = static::find($messages);
            if (!empty($message)) {
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
        if (!empty($this->type)) {
            $data = json_decode($this->data, true);
            $content = '';
            if ($this->type == 1 || $this->type == 2) {
                $userList = [];
                foreach ($data['user'] as $user) {
                    $uid = $user['uid'];
                    $name = $user['name'];
                    $url = route('user.view', ['uid' => $uid]);
                    $userList[] = "[$name]($url)";
                }
                $userString = implode(__('message.delimiter'), $userList);
                $groupName = $data['group']['name'];
                $groupURL = route('group.detail', ['gcode' => $data['group']['gcode']]);
                if ($this->type == 1) {
                    $content .= __('message.group.applied.desc', [
                        'userList' => $userString,
                        'groupInfo' => "[$groupName]($groupURL)",
                    ]);
                } else {
                    $content .= __('message.group.agreed.desc', [
                        'userList' => $userString,
                        'groupInfo' => "[$groupName]($groupURL)",
                    ]);
                }
                return $content;
            } elseif ($this->type == 3 || $this->type == 4) {
                $problemList = [];
                foreach ($data['problem'] as $problem) {
                    $pcode = $problem['pcode'];
                    $title = $problem['title'];
                    $url = route('problem.detail', ['pcode' => $pcode]);
                    $problemList[] = "[$pcode $title]($url)";
                }
                $problemString = implode(__('message.delimiter'), $problemList);
                if ($this->type == 3) {
                    $content .= __('message.solution.accepted.desc', ['problemList' => $problemString]);
                } else {
                    $content .= __('message.solution.declined.desc', ['problemList' => $problemString]);
                }
                return $content;
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
