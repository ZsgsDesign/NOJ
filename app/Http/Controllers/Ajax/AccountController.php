<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ResponseModel;
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
            return ResponseModel::err(1005);
        }

        $allow_extension = ['jpg','png','jpeg','gif','bmp'];
        if($isValid && in_array($extension,$allow_extension)){
            $path = $request->file('avatar')->store('/static/img/avatar','NOJPublic');

            $user = Auth::user();
            $old_path = $user->avatar;
            if($old_path != '/static/img/avatar/default.png' && $old_path != '/static/img/avatar/noj.png'){
                Storage::disk('NOJPublic')->delete($old_path);
            }

            $user->avatar = '/'.$path;
            $user->save();

            return ResponseModel::success(200, null, '/'.$path);
        }else{
            return ResponseModel::err(1005);
        }

    }
}
