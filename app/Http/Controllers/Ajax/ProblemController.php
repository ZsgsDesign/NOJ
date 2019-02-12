<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Requests;
use App\Http\Controllers\VirtualJudge\Submit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ProblemController extends Controller
{
    /**
     * The Ajax Problem Controller.
     *
     * @return Response
     */
    public function submitSolution(Request $request)
    {
        $all_data = $request->all();

        $vj_submit = new Submit($all_data);

        return response()->json($vj_submit->ret);
    }
}
