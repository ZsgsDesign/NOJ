<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class MainController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function problem()
    {
        return view('problem', ['version' => "1.0"]);
    }
}
