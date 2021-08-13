<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function info() {
        return response()->json([
            'success' => true,
            'message' => 'To Boldly Go',
            'ret' => [
                'product' => "NOJ",
                'version' => version()
            ],
            'err' => []
        ]);
    }
}
