<?php

namespace App\Models;

use App\Models\Rating\GroupRatingCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Cache;
use Auth;
use App\Models\ContestModel;

class GroupModel extends Model
{
    protected $tableName='group';
    protected $table='group';
    protected $primaryKey='gid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    /*
        join_policy:
            0:a user can join this group by both invitation and application // old version default value
            1:a user can join this group only by invitation
            2:a user can join this group only by application
            3:a user can join this group by both invitation and application
    */
    public $role=[
        "-3"=>"None",
        "-1"=>"Invited",
        "0"=>"Pending",
        "1"=>"Member",
        "2"=>"Manager",
        "3"=>"Leader"
    ];
    public $role_color=[
        "-3"=>"wemd-black",
        "-1"=>"wemd-deep-purple",
        "0"=>"wemd-red",
        "1"=>"wemd-grey",
        "2"=>"wemd-light-blue",
        "3"=>"wemd-amber"
    ];

    public function trendingGroups()
    {
        return Cache::tags(['group'])->get('trending');
    }

    public function gid($gcode)
    {
        return DB::table($this->tableName)->where(["gcode"=>$gcode])->first()["gid"];
    }

    public function cacheTrendingGroups()
    {
        $trending_groups=DB::table($this->tableName)->where(["public"=>1])->orderBy('created_at', 'desc')->select("gid", "gcode", "img", "name", "verified")->get()->all();
        foreach ($trending_groups as &$t) {
            $t["members"]=$this->countGroupMembers($t["gid"]);
        }
        usort($trending_groups, function($a, $b) {
            return $b["members"]<=>$a["members"];
        });
        Cache::tags(['group'])->put('trending', array_slice($trending_groups, 0, 12), 3600 * 24);
    }

    public function userGroups($uid)
    {
        $user_groups=DB::table("group_member")->join("group", "group_member.gid", "=", "group.gid")->where(["uid"=>$uid])->select("group.gid as gid", "gcode", "img", "name", "verified")->get()->all();
        foreach ($user_groups as &$m) {
            $m["members"]=$this->countGroupMembers($m["gid"]);
        }
        return $user_groups;
    }

    public function countGroupMembers($gid)
    {
        return DB::table("group_member")->where(["gid"=>$gid])->count();
    }

    public function getGroupTags($gid)
    {
        return DB::table("group_tag")->where(["gid"=>$gid])->select("tag")->get()->all();
    }

    public function countGroupContest($gid)
    {
        return [
            "contest_ahead" => DB::table("contest")->where(["gid"=>$gid])->where("begin_time", ">", DB::raw("now()"))->count(),
            "contest_going" => DB::table("contest")->where(["gid"=>$gid])->where("begin_time", "<=", DB::raw("now()"))->where("end_time", ">=", DB::raw("now()"))->count(),
            "contest_end" => DB::table("contest")->where(["gid"=>$gid])->where("end_time", "<", DB::raw("now()"))->count()
        ];
    }

    public function changeNickName($gid, $uid, $nickName)
    {
        return DB::table("group_member")->where(["gid"=>$gid, "uid"=>$uid])->update([
            "nick_name"=>$nickName
        ]);
    }

    public function changeGroupName($gid, $GroupName)
    {
        return DB::table("group")->where('gid', $gid)->update([
            "name"=>$GroupName
        ]);
    }

    public function changeJoinPolicy($gid, $JoinPolicy) {
        return DB::table("group")->where('gid', $gid)->update([
            "join_policy"=>$JoinPolicy
        ]);
    }

    public function basic($gid)
    {
        $basic_info=DB::table($this->tableName)->where(["gid"=>$gid])->first();
        return $basic_info;
    }

    public function details($gcode)
    {
        $basic_info=DB::table($this->tableName)->where(["gcode"=>$gcode])->first();
        if (empty($basic_info)) {
            return [];
        }
        $basic_info["members"]=$this->countGroupMembers($basic_info["gid"]);
        $basic_info["tags"]=$this->getGroupTags($basic_info["gid"]);
        $basic_info["create_time_foramt"]=date_format(date_create($basic_info["created_at"]), 'M jS, Y');
        $basic_info["contest_stat"]=$this->countGroupContest($basic_info["gid"]);
        return $basic_info;
    }

    public function joinPolicy($gid)
    {
        $ret=DB::table($this->tableName)->where(["gid"=>$gid])->first();
        return empty($ret) ? null : $ret["join_policy"];
    }

    public function userProfile($uid, $gid)
    {
        $info=DB::table("group_member")
        ->join('users', 'users.id', '=', 'group_member.uid')
        ->where(["gid"=>$gid, "uid"=>$uid])
        ->where("role", ">", 0)
        ->select('avatar', 'describes', 'email', 'gid', 'uid', 'name', 'nick_name', 'professional_rate', 'role', 'sub_group')
        ->first();
        if (!empty($info)) {
            $info["role_parsed"]=$this->role[$info["role"]];
            $info["role_color"]=$this->role_color[$info["role"]];
        }
        return $info;
    }

    public function userList($gid)
    {
        $user_list=DB::table("group_member")->join(
            "users",
            "users.id",
            "=",
            "group_member.uid"
        )->where(["gid"=>$gid])->orderBy('role', 'desc')->select(
            "role",
            "uid",
            "name",
            "nick_name",
            "avatar",
            "sub_group",
            "ranking"
        )->get()->all();
        foreach ($user_list as &$u) {
            $u["role_parsed"]=$this->role[$u["role"]];
            $u["role_color"]=$this->role_color[$u["role"]];
            if (is_null($u["sub_group"])) {
                $u["sub_group"]="None";
            }
        }
        return $user_list;
    }

    public function groupNotice($gid)
    {
        $notice_item=DB::table("group_notice")->where(["gid"=>$gid])->first();
        if (empty($notice_item)) {
            return [];
        }
        $notice_author=DB::table("users")->where(["id"=>$notice_item["uid"]])->first();
        $notice_item["name"]=$notice_author["name"];
        $notice_item["avatar"]=$notice_author["avatar"];
        $notice_item["post_date_parsed"]=formatHumanReadableTime($notice_item["created_at"]);
        $notice_item["content_parsed"]=clean(convertMarkdownToHtml($notice_item["content"]));
        return $notice_item;
    }

    public function judgeClearance($gid, $uid)
    {
        $ret=DB::table("group_member")->where(["gid"=>$gid, "uid"=>$uid])->first();
        return empty($ret) ? -3 : $ret["role"];
    }

    public function changeClearance($uid, $gid, $clearance)
    {
        return DB::table("group_member")->where([
            "uid"=>$uid,
            "gid"=>$gid
        ])->update([
            "role"=>$clearance
        ]);
    }

    public function removeClearance($uid, $gid)
    {
        return DB::table("group_member")->where([
            "uid"=>$uid,
            "gid"=>$gid
        ])->delete();
    }

    public function addClearance($uid, $gid, $clearance)
    {
        return DB::table("group_member")->insert([
            "uid"=>$uid,
            "gid"=>$gid,
            "role"=>$clearance,
            "created_at"=>date("Y-m-d H:i:s")
        ]);
    }

    public function isMember($gid, $uid)
    {
        return DB::table("group_member")->where([
            "gid"=> $gid,
            "uid"=> $uid
        ])->where("role", ">", 0)->count();
    }

    public function problemTags($gid, $pid=-1)
    {
        if ($pid==-1) {
            $tags=DB::table('group_problem_tag')
            ->select('tag')
            ->where('gid', $gid)
            ->distinct()
            ->get()->all();
        } else {
            $tags=DB::table('group_problem_tag')
            ->select('tag')
            ->where('gid', $gid)
            ->where('pid', $pid)
            ->distinct()
            ->get()->all();
        }

        $tags_arr=[];
        if (!empty($tags)) {
            foreach ($tags as $value) {
                array_push($tags_arr, $value['tag']);
            }
        }
        return $tags_arr;
    }

    public function problems($gid)
    {
        $contestModel=new ContestModel();
        $problems=DB::table('contest_problem')
        ->join('contest', 'contest_problem.cid', '=', 'contest.cid')
        ->join('problem', 'contest_problem.pid', '=', 'problem.pid')
        ->select('contest_problem.cid as cid', 'problem.pid as pid', 'pcode', 'title')
        ->where('contest.gid', $gid)
        ->where('contest.practice', 1)
        ->orderBy('contest.created_at', 'desc')
        ->distinct()
        ->get()->all();
        $user_id=Auth::user()->id;
        foreach ($problems as $key => $value) {
            if ($contestModel->judgeClearance($value['cid'], $user_id)!=3) {
                unset($problems[$key]);
            } else {
                $problems[$key]['tags']=$this->problemTags($gid, $value['pid']);
            }
        }
        return $problems;
    }

    public function problemAddTag($gid, $pid, $tag)
    {
        return DB::table("group_problem_tag")->insert([
            "gid"=>$gid,
            "pid"=>$pid,
            "tag"=>$tag,
        ]);
    }

    public function problemRemoveTag($gid, $pid, $tag)
    {
        return DB::table("group_problem_tag")->where([
            "gid"=>$gid,
            "pid"=>$pid,
            "tag"=>$tag
        ])->delete();
    }

    public function judgeEmailClearance($gid, $email)
    {
        $user=DB::table("users")->where(["email"=>$email])->first();
        if (empty($user)) {
            return -4;
        }
        $ret=DB::table("group_member")->where([
            "gid"=>$gid,
            "uid"=>$user["id"],
        ])->first();
        return empty($ret) ? -3 : $ret["role"];
    }

    public function inviteMember($gid, $email)
    {
        $uid=DB::table("users")->where(["email"=>$email])->first();
        return DB::table("group_member")->insert([
            "uid"=>$uid["id"],
            "gid"=>$gid,
            "role"=>-1,
            "created_at"=>date("Y-m-d H:i:s")
        ]);
    }

    public function changeGroup($uid, $gid, $sub)
    {
        return DB::table("group_member")->where([
            "uid"=>$uid,
            "gid"=>$gid
        ])->update([
            "sub_group"=>$sub
        ]);
    }

    public function isUser($email)
    {
        return DB::table("users")->where([
            "email"=>$email
        ])->count();
    }

    public function isGroup($gcode)
    {
        return DB::table("group")->where([
            "gcode"=>$gcode,
        ])->count();
    }

    public function createGroup($uid, $gcode, $img, $name, $public, $description, $join_policy)
    {
        $gid=DB::table("group")->insertGetId([
            "gcode"=>$gcode,
            "img"=>$img,
            "name"=>$name,
            "public"=>$public,
            "verified"=>0,
            "description"=>$description,
            "join_policy"=>$join_policy,
            "custom_icon"=>null,
            "custom_title"=>null,
            "created_at"=>date("Y-m-d H:i:s")
        ]);
        return DB::table("group_member")->insert([
            "uid"=>$uid,
            "gid"=>$gid,
            "role"=>3,
            "created_at"=>date("Y-m-d H:i:s")
        ]);
    }

    public function detailNotice($gcode)
    {
        $group=DB::table("group")->where([
            "gcode"=>$gcode,
        ])->first();
        return $group_notice=DB::table("group_notice")->where([
            "gid"=>$group["gid"],
        ])->first();
    }

    public function createNotice($gid, $uid, $title, $content)
    {
        return DB::table("group_notice")->updateOrInsert(
            [
                "gid"=>$gid
            ],
            [
                "uid"=>$uid,
                "title"=>$title,
                "content"=>$content,
                "created_at"=>date("Y-m-d H:i:s"),
            ]);
    }

    public function groupMemberPracticeContestStat($gid)
    {
        $contestModel=new ContestModel();

        $allPracticeContest=DB::table('contest')
            ->where([
                'gid' => $gid,
                'practice' => 1,
            ])
            ->select('cid', 'name')
            ->get()->all();
        $user_list=$this->userList($gid);

        $memberData=[];
        foreach ($user_list as $u) {
            $memberData[$u['uid']]=[
                'name' => $u['name'],
                'nick_name' => $u['nick_name'],
                'elo' => $u['ranking'],
                'solved_all' => 0,
                'problem_all' => 0,
                'penalty' => 0,
                'contest_detial' => []
            ];
        }
        foreach ($allPracticeContest as $c) {
            $contestRankRaw=$contestModel->contestRank($c['cid']);
            foreach ($contestRankRaw as $key => $contestRank) {
                if (isset($contestRank['remote']) && $contestRank['remote']) {
                    unset($contestRankRaw[$key]);
                }
            }
            $contestRank=array_values($contestRankRaw);
            $problemsCount=DB::table('contest_problem')
                ->where('cid', $c['cid'])
                ->count();
            $index=1;
            $rank=1;
            $last_cr=[];
            $last_rank=1;
            foreach ($contestRank as $cr) {
                $last_rank=$index;
                if (!empty($last_cr)) {
                    if ($cr['solved']==$last_cr['solved'] && $cr['penalty']==$last_cr['penalty']) {
                        $rank=$last_rank;
                    } else {
                        $rank=$index;
                        $last_rank=$rank;
                    }
                }
                if (in_array($cr['uid'], array_keys($memberData))) {
                    $memberData[$cr['uid']]['solved_all']+=$cr['solved'];
                    $memberData[$cr['uid']]['problem_all']+=$problemsCount;
                    $memberData[$cr['uid']]['penalty']+=$cr['penalty'];
                    $memberData[$cr['uid']]['contest_detial'][$c['cid']]=[
                        'rank' => $rank,
                        'solved' => $cr['solved'],
                        'problems' => $problemsCount,
                        'penalty' => $cr['penalty']
                    ];
                }
                $last_cr=$cr;
                $index++;
            }
        }
        $new_memberData=[];
        foreach ($memberData as $uid => $data) {
            $contest_count=0;
            $rank_sum=0;
            foreach ($data['contest_detial'] as $cid => $c) {
                $rank_sum+=$c['rank'];
                $contest_count+=1;
            }
            $temp=$data;
            $temp['uid']=$uid;
            if ($contest_count!=0) {
                $temp['rank_ave']=$rank_sum / $contest_count;
            }
            array_push($new_memberData, $temp);
        }
        $ret=[
            'contest_list' => $allPracticeContest,
            'member_data' => $new_memberData
        ];
        return $ret;
    }

    public function groupMemberPracticeTagStat($gid)
    {
        $tags=$this->problemTags($gid);
        $tag_problems=[];

        $user_list=$this->userList($gid);
        foreach ($tags as $tag) {
            $tag_problems[$tag]=DB::table('problem')
                ->join('group_problem_tag', 'problem.pid', '=', 'group_problem_tag.pid')
                ->where([
                    'group_problem_tag.gid' => $gid,
                    'tag' => $tag
                ])
                ->select('group_problem_tag.pid as pid', 'pcode', 'title')
                ->get()->all();
        }
        $all_problems=[];
        foreach ($tag_problems as &$tag_problem_set) {
            foreach ($tag_problem_set as $problem) {
                $all_problems[$problem['pid']]=$problem;
            }
            $tag_problem_set=array_column($tag_problem_set, 'pid');
        }
        $submission_data=DB::table('submission')
            ->whereIn('pid', array_keys($all_problems))
            ->whereIn('uid', array_column($user_list, 'uid'))
            ->where('verdict', 'Accepted')
            ->select('pid', 'uid')
            ->get()->all();

        $memberData=[];
        foreach ($user_list as $member) {
            $completion=[];
            foreach ($tag_problems as $tag => $problems) {
                $completion[$tag]=[];
                foreach ($problems as $problem) {
                    $is_accepted=0;
                    foreach ($submission_data as $sd) {
                        if ($sd['pid']==$problem && $sd['uid']==$member['uid']) {
                            $is_accepted=1;
                            break;
                        }
                    }
                    $completion[$tag][$problem]=$is_accepted;
                }
            }
            array_push($memberData, [
                'uid' => $member['uid'],
                'name' => $member['name'],
                'nick_name' => $member['nick_name'],
                'completion' => $completion,
            ]);
        }
        $ret=[
            'all_problems' => $all_problems,
            'tag_problems' => $tag_problems,
            'member_data' => $memberData
        ];
        return $ret;
    }

    public function refreshAllElo()
    {
        $result=[];
        $gids=DB::table('group')->select('gid', 'name')->get()->all();
        foreach ($gids as $gid) {
            $result[$gid['gid']]=[
                'name' => $gid['name'],
                'result' => $this->refreshElo($gid['gid']),
            ];
        }
        return $result;
    }

    public function refreshElo($gid)
    {
        DB::table('group_rated_change_log')
            ->where('gid', $gid)
            ->delete();
        DB::table('group_member')
            ->where('gid', $gid)
            ->update([
                'ranking' => 1500
            ]);
        $contests=DB::table('contest')
            ->where([
                'gid' => $gid,
                'practice' => 1
            ])
            ->where('end_time', '<', date("Y-m-d H:i:s"))
            ->select('cid', 'name')
            ->orderBy('end_time')
            ->get()->all();

        if (empty($contests)) {
            return [];
        }
        $result=[];
        $contestModel=new ContestModel();
        foreach ($contests as $contest) {
            $judge_status=$contestModel->judgeOver($contest['cid']);
            if ($judge_status['result']==true) {
                $calc=new GroupRatingCalculator($contest['cid']);
                $calc->calculate();
                $calc->storage();
                $result[]=[
                    'ret' => 'success',
                    'cid' => $contest['cid'],
                    'name' => $contest['name']
                ];
            } else {
                $result[]=[
                    'ret' => 'judging',
                    'cid' => $contest['cid'],
                    'name' => $contest['name'],
                    'submissions' => $judge_status['sid']
                ];
            }
        }

        return $result;
    }

    public function getEloChangeLog($gid, $uid)
    {
        $ret=DB::table('group_rated_change_log')
            ->join('contest', 'group_rated_change_log.cid', '=', 'contest.cid')
            ->where([
                'group_rated_change_log.gid' => $gid,
                'group_rated_change_log.uid' => $uid
            ])->select('group_rated_change_log.cid as cid', 'contest.name as name', 'ranking', 'end_time')
            ->orderBy('contest.end_time')
            ->get()->all();
        $begin=[
            'cid' => -1,
            'name' => '',
            'ranking' => '1500',
            'end_time' => date("Y-m-d H:i:s", (strtotime($ret[0]['end_time'] ?? time())-3600 * 24)),
        ];
        array_unshift($ret, $begin);
        return $ret;
    }
}
