<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Requests;
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

        return response()->json([
            'ret' => 200,
            'desc' => 'successful',
            'data' => [
                "pcode" => $all_data["pcode"]
            ]
        ]);
    }
}
