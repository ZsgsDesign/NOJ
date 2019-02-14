<?php

namespace App\Http\Controllers;

use App\Models\GroupModel;
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
        $groupModel=new GroupModel();
        $tending_list=$groupModel->tending_list();
        $mine_list=$groupModel->mine_list();
        return view('group.index', [
            'page_title' => "Group",
            'site_title' => "CodeMaster",
            'tending' => $tending_list,
            'mine' => $mine_list
        ]);
    }

    /**
     * Show the Group Detail Page.
     *
     * @return Response
     */
    public function detail()
    {
        $groupModel=new GroupModel();
        return view('group.detail', [
            'page_title'=>"Group Detail",
            'site_title'=>"CodeMaster"
        ]);
    }
}
