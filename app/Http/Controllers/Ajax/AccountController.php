<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ResponseModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Auth;

class AccountController extends Controller
{

    public function updateAvatar(Request $request) {
        $isValid=$request->file('avatar')->isValid();
        if ($isValid) {
            $extension=$request->file('avatar')->extension();
        } else {
            return ResponseModel::err(1005);
        }

        $allow_extension=['jpg', 'png', 'jpeg', 'gif', 'bmp'];
        if ($isValid && in_array($extension, $allow_extension)) {
            $path=$request->file('avatar')->store('/static/img/avatar', 'NOJPublic');

            $user=Auth::user();
            $old_path=$user->avatar;
            if ($old_path!='/static/img/avatar/default.png' && $old_path!='/static/img/avatar/noj.png') {
                Storage::disk('NOJPublic')->delete($old_path);
            }

            $user->avatar='/'.$path;
            $user->save();

            return ResponseModel::success(200, null, '/'.$path);
        } else {
            return ResponseModel::err(1005);
        }
    }

    public function changeBasicInfo(Request $request) {
        $request->validate([
            "username" => [
                "required",
                "string",
                "max:16",
                "min:1",
                Rule::unique('users', 'name')->ignore(Auth::user()->id)
            ],
            "describes" => "required|string|max:255"
        ]);
        $username=$request->input('username');
        $describes=$request->input('describes');
        $user=Auth::user();
        if (!Auth::user()->contest_account) {
            $user->name=$username;
        }
        $user->describes=$describes;
        $user->save();
        return ResponseModel::success();
    }

    public function changePassword(Request $request) {
        if (!$request->has('old_password') || !$request->has('new_password') || !$request->has('confirm_password')) {
            return ResponseModel::err(1003);
        }
        $old_password=$request->input('old_password');
        $new_password=$request->input('new_password');
        $confirm_password=$request->input('confirm_password');
        if ($new_password!=$confirm_password) {
            return ResponseModel::err(2004);
        }
        if (strlen($new_password)<8 || strlen($old_password)<8) {
            return ResponseModel::err(1006);
        }
        $user=Auth::user();
        if ($user->hasIndependentPassword() && !Hash::check($old_password, $user->password)) {
            return ResponseModel::err(2005);
        }
        $user->password=Hash::make($new_password);
        $user->save();
        return ResponseModel::success();
    }

    public function checkEmailCooldown(Request $request) {
        $last_send=$request->session()->get('last_email_send');
        if (empty($last_send) || time()-$last_send>=300) {
            $request->session()->put('last_email_send', time());
            return ResponseModel::success(200, null, 0);
        } else {
            $cooldown=300-(time()-$last_send);
            return ResponseModel::success(200, null, $cooldown);
        }
    }

    public function changeExtraInfo(Request $request) {
        $input=$request->input();
        $allow_change=['gender', 'contact', 'school', 'country', 'location'];
        foreach ($input as $key => $value) {
            if (!in_array($key, $allow_change)) {
                return ResponseModel::error(1007);
            }
        }
        foreach ($input as $key => $value) {
            if (strlen($value)!=0) {
                Auth::user()->setExtra($key, $value, 0);
            } else {
                Auth::user()->setExtra($key, null);
            }
        }
        return ResponseModel::success();
    }

    public function saveEditorWidth(Request $request) {
        $input=$request->input();
        $allow_change=['editor_left_width'];
        foreach ($input as $key => $value) {
            if (!in_array($key, $allow_change)) {
                return ResponseModel::error(1007);
            }
        }
        foreach ($input as $key => $value) {
            if (strlen($value)!=0) {
                Auth::user()->setExtra($key, $value, 0);
            } else {
                Auth::user()->setExtra($key, null);
            }
        }
        return ResponseModel::success();
    }

    public function saveEditorTheme(Request $request) {
        $input=$request->input();
        $allow_change=['editor_theme'];
        foreach ($input as $key => $value) {
            if (!in_array($key, $allow_change)) {
                return ResponseModel::error(1007);
            }
        }
        foreach ($input as $key => $value) {
            if (strlen($value)!=0) {
                Auth::user()->setExtra($key, $value, 0);
            } else {
                Auth::user()->setExtra($key, null);
            }
        }
        return ResponseModel::success();
    }
}
