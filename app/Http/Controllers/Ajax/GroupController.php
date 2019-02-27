<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Requests;
use App\Models\ContestModel;
use App\Models\GroupModel;
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
            'begin_time' => 'required|date',
            'end_time' => 'required|date|after:begin_time',
            'gid' => 'required|integer',
            'description' => 'string'
        ]);

        $all_data = $request->all();

        $contestModel=new ContestModel();
        $groupModel=new GroupModel();
        $clearance = $groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance<2) {
            return response()->json([
                "ret"=>1001,
                "desc"=>"Permission Denied",
                "data"=>null
            ]);
        }
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

        if (empty($problemSet)) {
            return response()->json([
                "ret"=>1002,
                "desc"=>"Missing Param",
                "data"=>null
            ]);
        }

        $contestModel->arrangeContest($all_data["gid"], [
            "name"=>$all_data["name"],
            "description"=>$all_data["description"],
            "begin_time"=>$all_data["begin_time"],
            "end_time"=>$all_data["end_time"],
        ], $problemSet);

        return response()->json([
            "ret"=>200,
            "desc"=>"Successful",
            "data"=>null
        ]);
    }
}
