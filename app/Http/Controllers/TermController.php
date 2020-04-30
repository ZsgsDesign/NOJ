<?php

namespace App\Http\Controllers;

use App\Models\ProblemModel;
use App\Models\Submission\SubmissionModel;
use App\Models\CompilerModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JavaScript;
use Auth;

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
