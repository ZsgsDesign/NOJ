<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function info(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Succeed',
            'ret' => array_merge($request->submission->toArray(), [
                'owner' => $request->submission->user->id==auth()->user()->id,
                'lang' => $request->submission->compiler->lang
            ]),
            'err' => []
        ]);
    }
}
