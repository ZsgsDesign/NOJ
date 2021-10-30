<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ContestModel;
use App\Models\GroupModel;
use App\Models\ResponseModel;
use App\Models\Eloquent\User;
use App\Models\Eloquent\Group;
use App\Models\Eloquent\Problem;
use App\Models\Eloquent\Messager\UniversalMessager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;
use Carbon;
use Exception;
use Validator;

class GroupManageController extends Controller
{
    /**
     * The Ajax Contest Arrange.
     *
     * @param Request $request web request
     *
     * @return JsonResponse
     */
    public function arrangeContest(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'problems' => 'required|max:2550',
            'begin_time' => 'required|date',
            'status_visibility' => 'required|integer',
            'end_time' => 'required|date|after:begin_time',
            'gid' => 'required|integer',
            'description' => 'string',
            'public' => 'integer',
            'practice' => 'integer'
        ]);

        $all_data=$request->all();

        if (($all_data['public'] ?? 0) && ($all_data['practice'] ?? 0)) {
            return ResponseModel::err(4007);
        }

        $contestModel=new ContestModel();
        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance<2) {
            return ResponseModel::err(2001);
        }
        $problems=explode(",", $all_data["problems"]);
        if (count($problems)>26) {
            return ResponseModel::err(4002);
        }
        $i=0;
        $problemSet=[];
        foreach ($problems as $p) {
            if (!empty($p)) {
                $i++;
                $problemSet[]=[
                    "number"=>$i,
                    "pcode"=>$p,
                    "points"=>100
                ];
            }
        }

        if (empty($problemSet)) {
            return ResponseModel::err(1003);
        }

        $cid=$contestModel->arrangeContest($all_data["gid"], [
            "assign_uid"=>Auth::user()->id,
            "name"=>$all_data["name"],
            "description"=>$all_data["description"],
            "status_visibility"=>$all_data["status_visibility"],
            "begin_time"=>$all_data["begin_time"],
            "end_time"=>$all_data["end_time"],
            "practice"=>$all_data["practice"] ?? 0,
            "public"=>$all_data["public"] ?? 0,
        ], $problemSet);

        return ResponseModel::success(200, 'Successful!', $cid);
    }

    public function changeGroupName(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'group_name' => 'max:50',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance<2) {
            return ResponseModel::err(2001);
        }

        $groupModel->changeGroupName($all_data["gid"], $all_data["group_name"]);
        return ResponseModel::success(200);
    }

    public function changeJoinPolicy(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'join_policy' => 'required|integer',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance<2) {
            return ResponseModel::err(2001);
        }

        if ($all_data["join_policy"]<1 || $all_data["join_policy"]>3) {
            return ResponseModel::err(1007);
        }

        $groupModel->changeJoinPolicy($all_data["gid"], $all_data["join_policy"]);
        return ResponseModel::success(200);
    }

    public function changeGroupImage(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
        ]);

        $all_data=$request->all();

        if (!empty($request->file('img')) && $request->file('img')->isValid()) {
            $extension=$request->file('img')->extension();
        } else {
            return ResponseModel::err(1005);
        }

        $allow_extension=['jpg', 'png', 'jpeg', 'gif', 'bmp'];

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance<2) {
            return ResponseModel::err(2001);
        }

        if (!in_array($extension, $allow_extension)) {
            return ResponseModel::err(1005);
        }

        $path=$request->file('img')->store('/static/img/group', 'NOJPublic');

        $group=GroupModel::find($all_data["gid"]);
        $old_path=$group->img;
        if ($old_path!='/static/img/group/default.png' && $old_path!='/static/img/group/noj.png' && $old_path!='/static/img/group/icpc.png') {
            Storage::disk('NOJPublic')->delete($old_path);
        }

        $group->img='/'.$path;
        $group->save();

        return ResponseModel::success(200, null, '/'.$path);

    }

    public function changeMemberClearance(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'uid' => 'required|integer',
            'permission' => 'required|integer|max:3|min:1',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();

        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        $target_clearance=$groupModel->judgeClearance($all_data["gid"], $all_data['uid']);

        if ($target_clearance==-3) {
            return ResponseModel::err(7004);
        }

        if ($target_clearance>=$clearance || $clearance<2 || $all_data['permission']>=$clearance) {
            return ResponseModel::err(2001);
        }

        $groupModel->changeClearance($all_data['uid'], $all_data["gid"], $all_data['permission']);

        $result_info=$groupModel->userProfile($all_data['uid'], $all_data["gid"]);
        return ResponseModel::success(200, null, $result_info);
    }

    public function approveMember(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'uid' => 'required|integer',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        $targetClearance=$groupModel->judgeClearance($all_data["gid"], $all_data["uid"]);
        if ($clearance>1) {
            if ($targetClearance!=0) {
                return ResponseModel::err(7003);
            }
            $groupModel->changeClearance($all_data["uid"], $all_data["gid"], 1);
            return ResponseModel::success(200);
        }
        return ResponseModel::err(7002);
    }

    public function removeMember(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'uid' => 'required|integer',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        $targetClearance=$groupModel->judgeClearance($all_data["gid"], $all_data["uid"]);
        if ($clearance<=1 || $clearance<=$targetClearance) {
            return ResponseModel::err(7002);
        }

        $groupModel->removeClearance($all_data["uid"], $all_data["gid"]);
        $groupModel->refreshElo($all_data["gid"]);
        return ResponseModel::success(200);
    }

    public function inviteMember(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'email' => 'required|email',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $is_user=$groupModel->isUser($all_data["email"]);
        if (!$is_user) {
            return ResponseModel::err(2006);
        }
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance<2) {
            return ResponseModel::err(7002);
        }
        $targetClearance=$groupModel->judgeEmailClearance($all_data["gid"], $all_data["email"]);
        if ($targetClearance!=-3) {
            return ResponseModel::err(7003);
        }
        $groupModel->inviteMember($all_data["gid"], $all_data["email"]);
        $basic=$groupModel->basic($all_data['gid']);
        $url=route('group.detail', ['gcode' => $basic['gcode']]);
        $receiverInfo=User::where('email', $all_data['email'])->first();
        $senderName=Auth::user()->name;
        sendMessage([
            'receiver' => $receiverInfo["id"],
            'sender' => config('app.official_sender'),
            'type' => 7,
            'level' => 4,
            'title' => __('message.group.invited.title', ['senderName' => $senderName, 'groupName' => $basic['name']]),
            'data' => [
                'group' => [
                    'gcode' => $basic['gcode'],
                    'name'  => $basic['name'],
                ],
            ]
        ]);
        return ResponseModel::success(200);
    }

    public function changeSubGroup(Request $request)
    {
        $request->validate([
            'gid'=>'required|integer',
            'uid'=>'required|integer',
            'sub'=>'nullable|max:60000'
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        $targetClearance=$groupModel->judgeClearance($all_data["gid"], $all_data["uid"]);
        if ($clearance>1 && $clearance>=$targetClearance) {
            $groupModel->changeGroup($all_data["uid"], $all_data["gid"], $all_data["sub"]);
            return ResponseModel::success(200);
        }
        return ResponseModel::err(7002);
    }

    public function createNotice(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'title' => 'required|min:3|max:50',
            'content' => 'required|min:3|max:60000',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance<2) {
            return ResponseModel::err(2001);
        }
        $groupModel->createNotice($all_data["gid"], Auth::user()->id, $all_data["title"], $all_data["content"]);
        return ResponseModel::success(200);
    }

    public function createHomework(Request $request)
    {
        try {
            $all = $request->all();
            $all['currently_at'] = strtotime('now');
            $validator = Validator::make($all, [
                'title'         => 'required|string|min:1|max:100',
                'description'   => 'required|string|min:1|max:65535',
                'ended_at'      => 'required|date|after:currently_at',
                'gid'           => 'required|integer|gte:1',
                'problems'      => 'required|array',
            ], [], [
                'title'         => 'Title',
                'description'   => 'Description',
                'ended_at'      => 'Ended Time',
                'currently_at'  => 'Current Time',
                'gid'           => 'Group ID',
                'problems'      => 'Problems',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            if (count($request->problems) > 26) {
                throw new Exception('Please include no more than 26 problems.');
            }

            if (count($request->problems) < 1) {
                throw new Exception('Please include at least one problem.');
            }

            $proceedProblems = $request->problems;
            $proceedProblemCodes = [];

            foreach ($proceedProblems as &$problem) {
                if (!is_array($problem)) {
                    throw new Exception('Each problem object must be an array.');
                }

                $problem['pcode'] = mb_strtoupper(trim($problem['pcode']));

                if(array_search($problem['pcode'], $proceedProblemCodes) !== false) {
                    throw new Exception("Duplicate Problem");
                }

                $validator = Validator::make($problem, [
                    'pcode'         => 'required|string|min:1|max:100',
                    // 'alias'         => 'required|string|min:0|max:100|nullable',
                    // 'points'        => 'required|integer|gte:1',
                ], [], [
                    'pcode'         => 'Problem Code',
                    // 'alias'         => 'Alias',
                    // 'points'        => 'Points',
                ]);

                if ($validator->fails()) {
                    throw new Exception($validator->errors()->first());
                }

                $proceedProblemCodes[] = $problem['pcode'];
            }

            unset($problem);

            $problemsDict = Problem::whereIn('pcode', $proceedProblemCodes)->select('pid', 'pcode')->get()->pluck('pid', 'pcode');

            try {
                foreach($proceedProblems as &$proceedProblem) {
                    $proceedProblem['pid'] = $problemsDict[$proceedProblem['pcode']];
                    if(blank($proceedProblem['pid'])) {
                        throw new Exception();
                    }
                }
                unset($proceedProblem);
            } catch (Exception $e) {
                throw new Exception('Problem Not Found');
            }
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'description' => [
                        $e->getMessage()
                    ]
                ],
                'message' => "The given data was invalid."
            ], 422);
        }

        $groupModel = new GroupModel();
        $clearance = $groupModel->judgeClearance($request->gid, Auth::user()->id);
        if ($clearance < 2) {
            return ResponseModel::err(2001);
        }

        try {
            $homeworkInstance = Group::find($request->gid)->addHomework($request->title, $request->description, Carbon::parse($request->ended_at), $proceedProblems);
        } catch (Exception $e) {
            return ResponseModel::err(7009);
        }

        $homeworkInstance->sendMessage();

        return ResponseModel::success(200);
    }
}
