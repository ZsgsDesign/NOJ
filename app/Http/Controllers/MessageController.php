<?php

namespace App\Http\Controllers;

use App\Models\Eloquent\Message;
use Auth;
use Illuminate\Support\Facades\Redirect;

class MessageController extends Controller
{
    public function index()
    {
        return view('message.index', [
            'page_title' => "Message",
            'site_title' => config("app.name"),
            'navigation' => "Home",
            'messages' => Message::listAll(Auth::user()->id),
        ]);
    }

    public function detail($id)
    {
        $message = Message::find($id);

        if (blank($message) || $message->receiver != Auth::user()->id) {
            return Redirect::route('message.index');
        }

        $message->read();

        return view('message.detail', [
            'page_title'=>"Message",
            'site_title'=>config("app.name"),
            'navigation'=>"Home",
            'message' => $message
        ]);
    }
}
