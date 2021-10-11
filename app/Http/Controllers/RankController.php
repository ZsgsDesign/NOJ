<?php
namespace App\Http\Controllers;

use App\Models\Eloquent\Tool\SiteRank;
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
        $rankingList=SiteRank::list(100);
        return view('rank.index', [
                'page_title'=>"Rank",
                'site_title'=>config("app.name"),
                'navigation' => "Rank",
                'rankingList' => $rankingList
            ]);
    }
}
