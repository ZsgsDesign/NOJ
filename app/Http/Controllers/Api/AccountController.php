<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function login(Request $request) {
        $credentials=[
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($credentials)) {
            $user=auth()->user();
            if (!empty($user->tokens)) {
                foreach ($user->tokens as $token) {
                    $token->delete();
                }
            }
            $token=$user->createToken('NOJ Password Grant Client')->accessToken;
            return response()->json([
                'success' => true,
                'message' => 'Successfully Login',
                'ret' => [
                    "token" => $token,
                    "user" => $user,
                ],
                'err' => []
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email/Password Wrong',
                'ret' => [],
                'err' => [
                    'code' => 1100,
                    'msg' => 'Email/Password Wrong',
                    'data'=>[]
                ]
            ]);
        }

    }
}
