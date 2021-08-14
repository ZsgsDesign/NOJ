<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Show the Status Page.
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return view('search.index', [
            'page_title' => "Search",
            'site_title' => config("app.name"),
            'navigation' => null,
            'search_key' => $request->input('q'),
            'search_category' => $request->input('tab', 'problems'),
            'page' => $request->input('page', 1)
        ]);
    }
}
