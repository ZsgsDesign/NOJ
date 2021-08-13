<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ResponseModel;
use App\Models\Eloquent\Dojo\DojoPass;
use App\Models\Eloquent\Dojo\Dojo;
use Illuminate\Http\Request;
use Auth;
use Throwable;

class DojoController extends Controller
{

    public function complete(Request $request)
    {
        $request->validate([
            "dojo_id" => "required|integer"
        ]);

        $dojo_id=$request->input('dojo_id');

        try {
            if (!Dojo::findOrFail($dojo_id)->canPass()) {
                return ResponseModel::err(10001);
            }
        } catch (Throwable $e) {
            return ResponseModel::err(10002);
        }

        $user_id=Auth::user()->id;
        $dojoRecord=DojoPass::firstOrCreate([
            'dojo_id' => $dojo_id,
            'user_id' => $user_id,
        ]);
        $dojoRecord->save();
        return ResponseModel::success(200, null, [
            'id' => $dojoRecord->id
        ]);
    }
}
