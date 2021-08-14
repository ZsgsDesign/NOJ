<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ResponseModel;
use App\Models\Eloquent\Abuse;
use App\Models\Eloquent\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Auth;
use Str;
use Arr;
use Throwable;

class AbuseController extends Controller
{
    protected static $cause=[];

    public static function findCause($causeDesc) {
        if (empty($cause)) {
            self::$cause=array_flip(Abuse::$cause);
        }
        $causeID=Arr::get(self::$cause, $causeDesc, 0);
        $causeDesc=Arr::get(Abuse::$cause, $causeID, 'General');
        return [$causeID, $causeDesc];
    }

    public function report(Request $request)
    {
        $request->validate([
            "supplement" => "required|string",
            "category" => ['required', Rule::in(Abuse::$supportCategory)],
            "subject_id" => "required|integer"
        ]);
        $category2link=[
            'group'=>function($id) {
                return route('group.detail', ['gcode'=>Group::findOrFail($id)->gcode]);
            },
            'user'=>function($id) {
                return route('user.view', ['uid' => $id]);
            }
        ];
        $supplement=$request->input('supplement');
        $category=$request->input('category');
        $subject_id=$request->input('subject_id');
        try {
            $link=$category2link[$category]($subject_id);
        } catch (Throwable $e) {
            return ResponseModel::err(9001);
        }
        $uid=Auth::user()->id;
        [$causeID, $causeDesc]=self::findCause('General');
        $abuseRecord=Abuse::create([
            'title' => Str::title($category)." #$subject_id Abused - $causeDesc",
            'category' => array_search($category, Abuse::$supportCategory),
            'cause' => $causeID,
            'supplement' => $supplement,
            'link' => $link,
            'user_id' => $uid,
        ]);
        $abuseRecord->save();
        return ResponseModel::success(200, null, [
            'id' => $abuseRecord->id
        ]);
    }
}
