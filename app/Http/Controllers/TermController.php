<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function user(Request $request)
    {
        return view('term.user', [
            'page_title' => "Terms ans Conditions",
            'site_title' => config("app.name"),
            'navigation' => "Term"
        ]);
    }
}
