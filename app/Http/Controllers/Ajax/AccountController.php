<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Auth;

class AccountController extends Controller
{
    /**
     * The Ajax Update Avatar.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function updateAvatar(Request $request)
    {
        $isValid = $request->file('avatar')->isValid();
        if($isValid){
            $extension = $request->file('avatar')->extension();
        }else{
            $output=[
                'ret' => '400',
                'desc' => 'Invalid file',
                'data' => null
            ];
            return response()->json($output);
        }
        $allow_extension = ['jpg','png','jpeg'];
        if($isValid && in_array($extension,$allow_extension)){
            $path = $request->file('avatar')->store('/static/img/avatar','NOJPublic');

            $user = Auth::user();
            $old_path = $user->avatar;
            if($old_path != '/static/img/avatar/default.png'){
                Storage::disk('NOJPublic')->delete($old_path);
            }

            $user->avatar = '/'.$path;
            $user->save();

            $output=[
                'ret' => '200',
                'desc' => 'success',
                'data' => [
                    'url' => '/'.$path
                ]
            ];
            return response()->json($output);
        }else{
            $output=[
                'ret' => '400',
                'desc' => 'Invalid file',
                'data' => null
            ];
            return response()->json($output);
        }

    }
}
