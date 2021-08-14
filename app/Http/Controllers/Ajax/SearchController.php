<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ResponseModel;
use App\Models\ProblemModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    /**
     * The Ajax to Search Problem using Problem code.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        if (!$request->has('search_key')) {
            return ResponseModel::err(1003);
        }
        $key=$request->input('search_key');
        $all_result=[];
        $search_from=[
            'users'         => \App\Models\Search\UserSearchModel::class,
            'problems'      => \App\Models\Search\ProblemSearchModel::class,
            'contests'      => \App\Models\Search\ContestSearchModel::class,
            'groups'        => \App\Models\Search\GroupSearchModel::class,
        ];
        foreach ($search_from as $name => $model_class) {
            if (class_exists($model_class)) {
                $model=new $model_class();
                if (!method_exists($model, 'search')) {
                    $all_result[$name]=[
                        'code' => -1,
                        'msg' => 'cannot find search method in '.$model_class
                    ];
                    continue;
                }
                $result=$model->search($key);
                $all_result[$name]=$result;
            } else {
                $all_result[$name]=[
                    'code' => -1,
                    'msg' => 'cannot find class named '.$model_class
                ]; ;
                continue;
            }
        }
        return ResponseModel::success(200, 'Successful', $all_result);
    }
}
