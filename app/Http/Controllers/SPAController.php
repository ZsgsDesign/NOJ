<?php

namespace App\Http\Controllers;

class SPAController extends Controller
{
    /**
     * Get the SPA view.
     */
    public function __invoke()
    {
        return view('index');
    }
}
