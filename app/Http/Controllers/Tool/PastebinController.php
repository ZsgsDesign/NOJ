<?php

namespace App\Http\Controllers\Tool;

use App\Models\Tool\PastebinModel;
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
        $pastebinModel=new PastebinModel();
        $detail=$pastebinModel->detail($code);
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
