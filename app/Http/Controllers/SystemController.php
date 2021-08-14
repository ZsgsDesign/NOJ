<?php

namespace App\Http\Controllers;

use App\Models\Submission\SubmissionModel;
use App\Models\JudgerModel;
use App\Http\Controllers\Controller;
use Auth;

class SystemController extends Controller
{
    /**
     * Show the System Info Page.
     *
     * @return Response
     */
    public function info()
    {
        $judgerModel=new JudgerModel();
        $judgeServer=$judgerModel->fetchServer(0);
        return view('system.info', [
            'page_title' => "System Info",
            'site_title' => config("app.name"),
            'navigation' => "System",
            'judgeServer' => $judgeServer
        ]);
    }
}
