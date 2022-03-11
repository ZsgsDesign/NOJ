<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Eloquent\Message;
use App\Utils\ResponseUtil;
use Auth;

class MessageController extends Controller
{
    public function unread(Request $request)
    {
        $request->validate([
            'uid' => 'required|integer'
        ]);
        $uid = $request->input('uid');
        if (!Auth::check() || Auth::user()->id != $uid) {
            return ResponseUtil::err(2001);
        }
        $ret = Message::unread($uid);
        return ResponseUtil::success(200, null, $ret);
    }

    public function allRead()
    {
        $uid=Auth::user()->id;
        Message::allRead($uid);
        return ResponseUtil::success(200);
    }

    public function deleteAll()
    {
        $uid=Auth::user()->id;
        Message::removeAllRead($uid);
        return ResponseUtil::success(200);
    }
}
