<?php

namespace App\Http\Controllers\Tool;

use App\Models\SubmissionModel;
use App\Http\Controllers\Controller;
use Auth;

class PastebinController extends Controller
{
    /**
     * Show the Pastebin Detail Page.
     *
     * @return Response
     */
    public function view()
    {
        return view('tool.pastebin.view', [
            'page_title' => "Detail",
            'site_title' => "PasteBin",
            'navigation' => "PasteBin"
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
