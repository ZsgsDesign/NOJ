<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Eloquent\MessageModel;
use App\Models\ResponseModel;
use Auth;

class MessageController extends Controller
{
    public function unread(Request $request)
    {
        $request->validate([
            'uid' => 'required|integer'
        ]);
        $uid = $request->input('uid');
        if(!Auth::check() || Auth::user()->id != $uid){
            return ResponseModel::err(2001);
        }
        $ret = MessageModel::unread($uid);
        return ResponseModel::success(200,null,$ret);
    }

    public function allRead()
    {
        $uid = Auth::user()->id;
        MessageModel::allRead($uid);
        return ResponseModel::success(200);
    }

    public function deleteAll()
    {
        $uid = Auth::user()->id;
        MessageModel::removeAllRead($uid);
        return ResponseModel::success(200);
    }
}
