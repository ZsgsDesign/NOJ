<?php

namespace App\Http\Controllers;

use App\Models\Services\OJService;
use App\Models\Eloquent\Announcement;
use App\Models\Eloquent\Carousel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;

class MainController extends Controller
{
    /**
     * Show the Home Page.
     *
     * @param Request $request your web request
     *
     * @return Response
     */
    public function home(Request $request)
    {
        $onlineJudges = OJService::list();
        return view('home', [
            'page_title' => "Home",
            'site_title' => config("app.name"),
            'navigation' => "Home",
            'announcements' => Announcement::orderBy('created_at', 'desc')->get(),
            'onlineJudges' => $onlineJudges,
            'carousel' => Carousel::where('available', 1)->orderBy('updated_at', 'desc')->get()
        ]);
    }

    public function legacyRedirect(Request $request)
    {
        $method = $request->method ?? null;
        $id = $request->id ?? null;
        if ($method == "showdetail" && filled($id)) {
            return (Problem::where('pcode', "NOJ$id")->count() > 0) ? Redirect::route('problem.detail', ['pcode' => "NOJ$id"]) : Redirect::route('problem.index');
        }
        return Redirect::route('home');
    }
}
