<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Http\Controllers\Controller;

class ProblemController extends Controller
{
    /**
     * Show the Problem Detail Page.
     *
     * @return Response
     */
    public function detail($pcode)
    {
        $problem=new Problem();
        $prob_detail=$problem->detail($pcode);
        return view('problem.detail', ['detail' => $prob_detail]);
    }
}
