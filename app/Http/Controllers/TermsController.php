<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function user(Request $request)
    {
        return view('terms.user', [
            'page_title' => __('navigation.terms'),
            'site_title' => config("app.name"),
            'navigation' => "Terms"
        ]);
    }
}
