<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'system','as' => 'system.'], function () {
    Route::post('/info', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'To Boldly Go.',
            'ret' => [
                'product' => "NOJ",
                'version' => version()
            ],
            'err' => []
        ]);
    })->name("info");
});

Route::group(['prefix' => 'account','as' => 'account.'], function () {
    Route::post('/login', function (Request $request) {
        // {
        //     email: arg.email,
        //     password: arg.password
        // }
        if(rand(0,1)){
            return response()->json([
                'success' => false,
                'message' => 'Email/Password Wrong.',
                'ret' => [],
                'err' => [
                    'code' => 1100,
                    'msg' => 'Email/Password Wrong.'
                ]
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Successfully Login.',
                'ret' => [
                    "token" => md5(time()),
                    "user" => [
                        "id" => 1,
                        "name" => "admin",
                        "avatar" => url("/static/img/avatar/SDL0ohVXn85VzcpCfu5QQMVvdGpg2F0BFbriLQnI.jpeg"),
                        "email" => "admin@code.master",
                        "email_verified_at" => null,
                        "professional_rate" => 1628,
                        "contest_account" => 1,
                        "created_at" => "2019-02-10 10:53:04",
                        "updated_at" => "2019-07-19 00:43:26",
                        "describes" => "# 123",
                        "prefix" => null
                    ]
                ],
                'err' => []
            ]);
        }
    })->name("login");
});

Route::group(['prefix' => 'contest','as' => 'contest.'], function () {
    Route::post('/info', function (Request $request) {
        // {
        //     cid: 1
        // }
        return response()->json([
            'success' => true,
            'message' => 'Succeed.',
            'ret' => [
                "cid" => 1,
                "name" => "“扇贝杯”南京邮电大学第四届软件和信息技术专业人才大赛-现场赛",
                "img" => url("/static/img/contest/default.jpg"),
                "begin_time" => "2020-05-04 01:18:00",
                "end_time" => "2020-05-04 03:00:00",
                "problems" => "10",
                "organizer" => "NOJ Official",
                "description" => "#### “扇贝杯”南京邮电大学第四届软件和信息技术专业人才大赛-现场赛\n\n密码：www.njupt.edU.Cn",
                "badges" => [
                    "rule_parsed" => "ICPC",
                    "audit_status" => 1,
                    "public" => 1,
                    "verified" => 1,
                    "rated" => 0,
                    "anticheated" => 1,
                    "desktop" => 1,
                ]
            ],
            'err' => []
        ]);
    })->name("info");

    Route::post('/status', function (Request $request) {
        // {
        //     cid: 1,
        //     filter: {
        //         account: "",
        //         problem: "A",
        //         result: "Wrong Answer",
        //     },
        //     page: 1
        // }
        $submissionModel = new App\Models\Submission\SubmissionModel();
        $data=[];
        foreach(range(1,50) as $i){
            $data[]=[
                "sid" => rand(60000,65536),
                "name" => Arr::random(["admin","zsgsdesign","test001"]),
                "nickname" => null,
                "ncode" => Arr::random(["A","B","C"]),
                "color" => "",
                "verdict" => Arr::random(Arr::divide($submissionModel->colorScheme)[0]),
                "time" => rand(100,1000),
                "memory" => rand(10,10000),
                "language" => Arr::random(["PHP 7.2.13", "GNU GCC C11 5.1.0"]),
                "submission_date" => "2020-05-04 00:29:35",
                "submission_date_parsed" => "3 hours ago",
            ];
        }
        foreach($data as &$d){
            $d["color"]= $submissionModel->colorScheme[$d["verdict"]];
        }
        return response()->json([
            'success' => true,
            'message' => 'Succeed.',
            'ret' => [
                "pagination" => [
                    "current_page" => 1,
                    "has_next_page" => true,
                    "has_previous_page" => false,
                    "next_page" => 2,
                    "num_items" => 50,
                    "num_pages" => 10,
                    "previous_page" => null
                ],
                "data" => $data
            ],
            'err' => []
        ]);
    })->name("status");

    Route::post('/scoreboard', function (Request $request) {
        // {
        //     cid: 1
        // }
        // oi
        return response()->json([
            'success' => true,
            'message' => 'Succeed.',
            'ret' => [
                "header" => [
                    "rank" => "Rank",
                    "normal" => [
                        "Account",
                        "Score",
                        "Solved"
                    ],
                    "subHeader" => true,
                    "problems" => [
                        "A",
                        "B",
                        "C",
                        "D",
                        "E",
                        "F",
                        "G",
                        "H",
                    ],
                    "problemsSubHeader" => [
                        "1 / 114",
                        "1 / 114",
                        "1 / 114",
                        "0 / 0",
                        "0 / 0",
                        "0 / 0",
                        "0 / 0",
                        "0 / 0",
                    ]
                ],
                "body" => [[
                    "rank" => 1,
                    "normal" => [
                        "SHAN04276",
                        660,
                        6,
                    ],
                    "problems" => [
                        [
                            "mainColor" => "wemd-green-text",
                            "mainScore" => "0",
                            "subColor" => null,
                            "subScore" => null
                        ],[
                            "mainColor" => "wemd-teal-text",
                            "mainScore" => "100",
                            "subColor" => null,
                            "subScore" => null
                        ],[
                            "mainColor" => "wemd-green-text",
                            "mainScore" => "60",
                            "subColor" => null,
                            "subScore" => null
                        ],[

                        ],[

                        ],[

                        ],[

                        ],[

                        ]
                    ],
                    "extra" => [
                        "owner" => true,
                        "remote" => false
                    ]
                ],[
                    "rank" => 2,
                    "normal" => [
                        "SHAN04112",
                        620,
                        6,
                    ],
                    "problems" => [
                        [
                            "mainColor" => "wemd-green-text",
                            "mainScore" => "0",
                            "subColor" => null,
                            "subScore" => null
                        ],[
                            "mainColor" => "wemd-green-text",
                            "mainScore" => "80",
                            "subColor" => null,
                            "subScore" => null
                        ],[
                            "mainColor" => "wemd-green-text",
                            "mainScore" => "60",
                            "subColor" => null,
                            "subScore" => null
                        ],[

                        ],[

                        ],[

                        ],[

                        ],[

                        ]
                    ],
                    "extra" => [
                        "owner" => true,
                        "remote" => false
                    ]
                ]]
            ],
            'err' => []
        ]);
    })->name("scoreboard");
});
