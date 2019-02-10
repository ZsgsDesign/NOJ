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
        return view('problem', ['prob_list' => $prob_list]);
    }
}
