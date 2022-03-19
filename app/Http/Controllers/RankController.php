<?php

namespace App\Http\Controllers;

use App\Utils\SiteRankUtil;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RankController extends Controller
{
    /**
     * Show the Rank Page.
     *
     * @param Request $request your web request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $rankingList = SiteRankUtil::list(100);
        return view('rank.index', [
            'page_title' => __('navigation.rank'),
            'site_title' => config("app.name"),
            'navigation' => "Rank",
            'rankingList' => $rankingList
        ]);
    }
}
