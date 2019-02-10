<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    /**
     * Show the Problem General Page.
     *
     * @return Response
     */
    public function problem()
    {
        $problem=new Problem();
        $prob_list=$problem->list();
        return view('problem', [
            'page_title'=>"Problem",
            'site_title'=>"CodeMaster",
            'prob_list' => $prob_list
        ]);
    }
    /**
     * Show the Account Login and Register Page.
     *
     * @return Response
     */
    public function account()
    {
        return view('account', [
            'page_title'=>"Account",
            'site_title'=>"CodeMaster"
        ]);
    }
}
