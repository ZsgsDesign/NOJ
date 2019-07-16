<?php

namespace App\Http\Controllers;

use App\Models\SubmissionModel;
use App\Http\Controllers\Controller;
use Auth;

class SystemController extends Controller
{
    /**
     * Show the System Info Page.
     *
     * @return Response
     */
    public function info()
    {
        return view('system.info', [
            'page_title' => "System Info",
            'site_title' => "NOJ",
            'navigation' => "System"
        ]);
    }
}
