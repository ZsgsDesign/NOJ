<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Http\Controllers\Controller;

class ContestController extends Controller
{
    /**
     * Show the Contest Page.
     *
     * @return Response
     */
    public function index()
    {
        return view('contest.index', [
            'page_title'=>"Contest",
            'site_title'=>"CodeMaster"
        ]);
    }
}
