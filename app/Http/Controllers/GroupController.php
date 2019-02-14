<?php

namespace App\Http\Controllers;

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

    /**
     * Show the Group Detail Page.
     *
     * @return Response
     */
    public function detail()
    {
        return view('group.detail', [
            'page_title'=>"Group Detail",
            'site_title'=>"CodeMaster"
        ]);
    }
}
