<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ResponseModel;
use App\Models\ProblemModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    /**
     * The Ajax to Search Problem using Problem code.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        if(!$request->has('search_key')){
            return ResponseModel::err(1003);
        }

        $search_key = strtoupper($request->input('search_key'));
        $problem=new ProblemModel();
        $prob_details = $problem->detail($search_key);
        if(!is_null($prob_details)){
            if ($problem->isBlocked($prob_details["pid"])) {
                return ResponseModel::err(403);
            }
            $problem_url = route('problem_detail',['pcode' => $search_key]);
            return ResponseModel::success(200, null, $problem_url);
        }else{
            return ResponseModel::err(3001);
        }
    }
}
