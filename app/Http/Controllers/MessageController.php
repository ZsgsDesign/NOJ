<?php

namespace App\Http\Controllers;

use App\Models\Eloquent\MessageModel;
use Auth;

class MessageController extends Controller
{
    public function home()
    {
        $uid = Auth::user()->id;
        $messages = MessageModel::list($uid);
        return view('message.index', [
            'page_title'=>"Message",
            'site_title'=>config("app.name"),
            'navigation'=>"Home",
            'messages'=>$messages,
        ]);
    }

    public function details()
    {
        return view('message.detail', [
            'page_title'=>"Message",
            'site_title'=>config("app.name"),
            'navigation'=>"Home",
        ]);
    }
}
