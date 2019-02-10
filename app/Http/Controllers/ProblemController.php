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
        return is_null($prob_detail) ?  redirect("/problem") :
                                        view('problem.detail', [
                                            'page_title'=>$prob_detail["title"],
                                            'site_title'=>"CodeMaster",
                                            'detail' => $prob_detail
                                        ]);
    }
}
