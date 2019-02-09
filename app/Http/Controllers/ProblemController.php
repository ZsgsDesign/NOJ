<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class MainController extends Controller
{
    /**
     * Show the Problem Page.
     *
     * @param  array  $filter
     * @return Response
     */
    public function problem()
    {
        return view('problem', ['version' => "1.0"]);
    }
}
