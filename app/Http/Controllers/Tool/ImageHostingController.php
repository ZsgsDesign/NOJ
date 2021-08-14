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

    /**
     * Show the Image Hosting Detail Page.
     *
     * @return Response
     */
    public function detail($id)
    {
        $image=ImageHosting::find($id);
        if (is_null($image)) {
            return abort('404');
        }
        if (Auth::user()->id!=$image->user_id) {
            return abort('403');
        }
        return view('tool.imagehosting.detail', [
            'page_title' => "Detail",
            'site_title' => "Image Hosting",
            'navigation' => "Image Hosting",
            'image' => $image,
        ]);
    }

    /**
     * Show the Image Hosting List Page.
     *
     * @return Response
     */
    public function list()
    {
        $images=Auth::user()->imagehostings()->orderBy('created_at', 'desc')->get();
        return view('tool.imagehosting.list', [
            'page_title' => "List",
            'site_title' => "Image Hosting",
            'navigation' => "Image Hosting",
            'images' => $images,
        ]);
    }
}
