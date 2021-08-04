<?php

namespace App\Http\Controllers\Tool\Ajax;

use App\Models\Eloquent\Tool\ImageHosting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\ResponseModel;
use Auth;

class ImageHostingController extends Controller
{
    /**
     * Generate a new pastebin.
     *
     * @return Response
     */
    public function generate(Request $request)
    {
        $user=Auth::user();

        if(!$user->hasPermission(26)){
            return ResponseModel::err(2001);
        }

        $isValid=$request->file('image')->isValid();
        if ($isValid) {
            $extension=$request->file('image')->extension();
        } else {
            return ResponseModel::err(1005);
        }

        $allow_extension=['jpg', 'png', 'jpeg', 'gif', 'bmp'];
        if ($isValid && in_array($extension, $allow_extension)) {
            $path=$request->file('image')->store('/static/img/upload', 'NOJPublic');
            ImageHosting::create([
                'user_id' => $user->id,
                'relative_path' => "/$path"
            ]);
            return ResponseModel::success(200, null, [
                'relative_path' => "/$path",
                'path' => url($path),
            ]);
        } else {
            return ResponseModel::err(1005);
        }
    }
}
