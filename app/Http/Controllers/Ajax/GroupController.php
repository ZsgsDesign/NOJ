<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Requests;
use App\Models\SubmissionModel;
use App\Http\Controllers\VirtualJudge\Submit;
use App\Http\Controllers\VirtualJudge\Judge;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\VirtualCrawler\Crawler;
use Sunra\PhpSimple\HtmlDomParser;
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
        $all_data = $request->all();

        return response()->json(
            [
                "ret"=>200
            ]
        );
    }
}
