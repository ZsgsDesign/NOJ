<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    /**
     * Show the Group Page.
     *
     * @return Response
     */
    public function index()
    {
        return view('group.index', [
            'page_title'=>"Group",
            'site_title'=>"CodeMaster"
        ]);
    }
}
