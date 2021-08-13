<?php

namespace App\Http\Controllers\Tool;

use App\Models\Eloquent\Tool\Pastebin;
use App\Http\Controllers\Controller;
use Auth;

class PastebinController extends Controller
{
    /**
     * Show the Pastebin Detail Page.
     *
     * @return Response
     */
    public function view($code)
    {
        $detail=Pastebin::where('code', $code)->first();
        if (is_null($detail)) {
            return abort('404');
        }
        if (!is_null($detail->expired_at) && strtotime($detail->expired_at)<strtotime(date("y-m-d h:i:s"))) {
            Pastebin::where('code', $code)->delete();
            return abort('404');
        }
        return view('tool.pastebin.view', [
            'page_title' => "Detail",
            'site_title' => "PasteBin",
            'navigation' => "PasteBin",
            'detail' => $detail
        ]);
    }

    /**
     * Show the Pastebin Create Page.
     *
     * @return Response
     */
    public function create()
    {
        return view('tool.pastebin.create', [
            'page_title' => "Create",
            'site_title' => "PasteBin",
            'navigation' => "PasteBin"
        ]);
    }
}
