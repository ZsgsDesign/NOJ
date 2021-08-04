<?php

namespace App\Http\Controllers\Tool;

use App\Models\Eloquent\Tool\ImageHosting;
use App\Models\Eloquent\User;
use App\Http\Controllers\Controller;
use Auth;

class ImageHostingController extends Controller
{
    /**
     * Show the Image Hosting Create Page.
     *
     * @return Response
     */
    public function create()
    {
        return view('tool.imagehosting.create', [
            'page_title' => "Create",
            'site_title' => "Image Hosting",
            'navigation' => "Image Hosting",
            'permission' => Auth::user()->hasPermission(26)
        ]);
    }
}
