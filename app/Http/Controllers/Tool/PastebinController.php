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
}
