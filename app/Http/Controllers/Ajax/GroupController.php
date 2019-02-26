<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Requests;
use App\Models\ContestModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;

class GroupController extends Controller
{
    /**
     * The Ajax Contest Arrange.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function arrangeContest(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'problems' => 'required|max:2550',
            'begin_time' => 'date',
            'end_time' => 'date',
            'gid' => 'integer',
        ]);
        $contestModel=new ContestModel();
        $contestModel->clearance("arrange");
        $all_data = $request->all();
        $problems = explode(",", $all_data["problems"]);
        $i=0;
        $problemSet=[];
        foreach ($problems as $p) {
            if (!empty($p)) {
                $i++;
                $problemSet[]=[
                    "number"=>$i,
                    "pcode"=>$p
                ];
            }
        }
        $contestModel->arrangeContest($all_data["gid"], [
            "name"=>$all_data["name"],
            "description"=>$all_data["description"],
            "begin_time"=>$all_data["begin_time"],
            "end_time"=>$all_data["end_time"],
        ], $problemSet);

        return response()->json([
            "ret"=>200
        ]);
    }
}
