<?php

namespace App\Http\Controllers;

use App\Models\Eloquent\Message;
use Auth;
use Illuminate\Support\Facades\Redirect;

class MessageController extends Controller
{
    public function index()
    {
        $uid = Auth::user()->id;
        $messages = Message::list($uid);
        return view('message.index', [
            'page_title'=>"Message",
            'site_title'=>config("app.name"),
            'navigation'=>"Home",
            'messages'=>$messages,
        ]);
    }

    public function detail($id)
    {
        $message = Message::read($id);
        if(empty($message) || $message->receiver != Auth::user()->id){
            return Redirect::route('message.index');
        }
        return view('message.detail', [
            'page_title'=>"Message",
            'site_title'=>config("app.name"),
            'navigation'=>"Home",
            'message' => $message
        ]);
    }
}
