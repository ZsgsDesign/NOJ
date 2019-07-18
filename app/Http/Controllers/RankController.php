<?php
namespace App\Http\Controllers;

use App\Models\RankModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class RankController extends Controller
{
    /**
     * Show the Rank Page.
     *
     * @param Request $request your web request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $rankModel=new RankModel();
        $rankingList=$rankModel->list(100);
        return view('rank.index', [
                'page_title'=>"Rank",
                'site_title'=>config("app.name"),
                'navigation' => "Rank",
                'rankingList' => $rankingList
            ]);
    }
}
