<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Submission\SubmissionModel;
use App\Models\Eloquent\OJ;

class ProblemModel extends Model
{
    protected $table='problem';
    protected $primaryKey='pid';
    const UPDATED_AT="update_date";

    public function basic($pid)
    {
        return DB::table($this->table)->where("pid", $pid)->first();
    }

    public function solutionList($pid, $uid=null)
    {
        if (is_null($uid)) {
            $details=DB::table("problem_solution")->join(
                "users",
                "id",
                "=",
                "problem_solution.uid"
            )->where([
                'problem_solution.pid'=>$pid,
                'problem_solution.audit'=>1
            ])->orderBy(
                "problem_solution.votes",
                "desc"
            )->get()->all();
        } else {
            $votes=DB::table("problem_solution_vote")->where([
                "uid"=>$uid
            ])->get()->all();
            foreach ($votes as $v) {
                $userVotes[$v["psoid"]]=$v["type"];
            }
            $details=DB::table("problem_solution")->join(
                "users",
                "id",
                "=",
                "problem_solution.uid"
            )->where([
                'problem_solution.pid'=>$pid,
                'problem_solution.audit'=>1
            ])->select([
                "problem_solution.psoid as psoid",
                "problem_solution.uid as uid",
                "problem_solution.pid as pid",
                "problem_solution.content as content",
                "problem_solution.audit as audit",
                "problem_solution.votes as votes",
                "problem_solution.created_at as created_at",
                "problem_solution.updated_at as updated_at",
                "avatar",
                "name"
            ])->orderBy("problem_solution.votes", "desc")->get()->all();
            foreach ($details as &$d) {
                $d["type"]=isset($userVotes[$d["psoid"]]) ? $userVotes[$d["psoid"]] : null;
            }
            unset($d);
        }
        foreach ($details as &$d) {
            $d["content_parsed"]=clean(convertMarkdownToHtml($d["content"]));
        }
        return $details;
    }

    public function addSolution($pid, $uid, $content)
    {
        $details=DB::table("problem_solution")->where(['pid'=>$pid, 'uid'=>$uid])->first();
        if (empty($details)) {
            DB::table("problem_solution")->insert([
                "uid"=>$uid,
                "pid"=>$pid,
                "content"=>$content,
                "votes"=>0,
                "audit"=>$this->inteliAudit($uid, $content),
                "created_at"=>date("Y-m-d H:i:s"),
                "updated_at"=>date("Y-m-d H:i:s"),
            ]);
            return true;
        }
        return false;
    }

    private function inteliAudit($uid, $content)
    {
        if (strpos($content, '```')!==false) {
            $userSolutionHistory=DB::table("problem_solution")->where(['uid'=>$uid])->orderByDesc('updated_at')->first();
            if (!empty($userSolutionHistory) && $userSolutionHistory["audit"]==1) {
                return 1;
            }
        }
        return 0;
    }

    public function voteSolution($psoid, $uid, $type)
    {
        $val=$type ? 1 : -1;
        $details=DB::table("problem_solution")->where(['psoid'=>$psoid])->first();
        if (empty($details)) {
            return ["ret"=>false];
        }

        $userVote=DB::table("problem_solution_vote")->where(['uid'=>$uid, "psoid"=>$psoid])->first();

        if (!empty($userVote)) {
            DB::table("problem_solution_vote")->where(['uid'=>$uid, "psoid"=>$psoid])->delete();
            if ($userVote["type"]==$type) {
                DB::table("problem_solution")->where([
                    'psoid'=>$psoid
                ])->update([
                    "votes"=>$details["votes"]+($userVote["type"]==1 ?-1 : 1),
                ]);
                return ["ret"=>true, "votes"=>$details["votes"]+($userVote["type"]==1 ?-1 : 1), "select"=>-1]; //disvote
            } elseif ($userVote["type"]==1) {
                $val--;
            } else {
                $val++;
            }
        }

        DB::table("problem_solution")->where([
            'psoid'=>$psoid
        ])->update([
            "votes"=>$details["votes"]+$val,
        ]);

        DB::table("problem_solution_vote")->insert([
            "uid"=>$uid,
            "psoid"=>$psoid,
            "type"=>$type,
        ]);

        return ["ret"=>true, "votes"=>$details["votes"]+$val, "select"=>$type];
    }

    public function removeSolution($psoid, $uid)
    {
        if (empty(DB::table("problem_solution")->where(['psoid'=>$psoid, 'uid'=>$uid])->first())) {
            return false;
        }
        DB::table("problem_solution")->where(['psoid'=>$psoid, 'uid'=>$uid])->delete();
        return true;
    }

    public function updateSolution($psoid, $uid, $content)
    {
        if (empty(DB::table("problem_solution")->where(['psoid'=>$psoid, 'uid'=>$uid])->first())) {
            return false;
        }
        DB::table("problem_solution")->where(['psoid'=>$psoid, 'uid'=>$uid])->update([
            "content"=>$content,
            "audit"=>$this->inteliAudit($uid, $content),
            "updated_at"=>date("Y-m-d H:i:s"),
        ]);
        return true;
    }

    public function isBlocked($pid, $cid=null)
    {
        $conflictContests=DB::table("contest")
                            ->join("contest_problem", "contest.cid", "=", "contest_problem.cid")
                            ->where("end_time", ">", date("Y-m-d H:i:s"))
                            ->where(["verified"=>1, "pid"=>$pid])
                            ->select(["contest_problem.cid as cid"])
                            ->get()
                            ->all();
        if (empty($conflictContests)) {
            return false;
        }
        foreach ($conflictContests as $c) {
            if ($cid==$c["cid"]) {
                return false;
            }
        }
        // header("HTTP/1.1 403 Forbidden");
        // exit();
        return true;
    }

    public function pid($pcode)
    {
        $temp=DB::table($this->table)->where(["pcode"=>$pcode])->select("pid")->first();
        return empty($temp) ? 0 : $temp["pid"];
    }

    public function pcode($pid)
    {
        $temp=DB::table($this->table)->where(["pid"=>$pid])->select("pcode")->first();
        return empty($temp) ? 0 : $temp["pcode"];
    }

    public function ocode($pid)
    {
        $temp=DB::table($this->table)->where(["pid"=>$pid])->select("OJ as oid")->first();
        return empty($temp) ? null : DB::table("oj")->where(["oid"=>$temp["oid"]])->select("ocode")->first()["ocode"];
    }

    public function oid($pid)
    {
        return DB::table($this->table)->where(["pid"=>$pid])->select("OJ as oid")->first()["oid"];
    }

    public function clearTags($pid)
    {
        DB::table("problem_tag")->where(["pid"=>$pid])->delete();
        return true;
    }

    public function addTags($pid, $tag)
    {
        DB::table("problem_tag")->insert(["pid"=>$pid, "tag"=>$tag]);
        return true;
    }

    public function getSolvedCount($oid)
    {
        return DB::table($this->table)->select("pid", "solved_count")->where(["OJ"=>$oid])->get()->all();
    }

    public function updateDifficulty($pid, $diff_level)
    {
        DB::table("problem_tag")->where(["pid"=>$pid])->update(["difficulty"=>$diff_level]);
        return true;
    }

    public function insertProblem($data)
    {
        $info=array_merge([
            'difficulty'=>-1,
            'update_date'=>date("Y-m-d H:i:s"),
        ], collect($data)->only([
            'file',
            'file_url',
            'title',
            'time_limit',
            'memory_limit',
            'OJ',
            'description',
            'input',
            'output',
            'note',
            'input_type',
            'output_type',
            'pcode',
            'contest_id',
            'index_id',
            'origin',
            'source',
            'solved_count',
            'tot_score',
            'partial',
            'markdown',
            'special_compiler',
            'order_index',
        ])->toArray());

        $pid=DB::table($this->table)->insertGetId($info);

        if (!empty($data["sample"])) {
            foreach ($data["sample"] as $d) {
                if (!isset($d['sample_note'])) {
                    $d['sample_note']=null;
                }
                DB::table("problem_sample")->insert([
                    'pid'=>$pid,
                    'sample_input'=>$d['sample_input'],
                    'sample_output'=>$d['sample_output'],
                    'sample_note'=>$d['sample_note'],
                ]);
            }
        }

        return $pid;
    }

    public function updateProblem($data)
    {
        DB::table($this->table)->where(["pcode"=>$data['pcode']])->update(array_merge([
            'difficulty'=>-1,
            'update_date'=>date("Y-m-d H:i:s"),
        ], collect($data)->only([
            'file',
            'file_url',
            'title',
            'time_limit',
            'memory_limit',
            'OJ',
            'description',
            'input',
            'output',
            'note',
            'input_type',
            'output_type',
            'contest_id',
            'index_id',
            'origin',
            'source',
            'solved_count',
            'tot_score',
            'partial',
            'markdown',
            'special_compiler',
            'order_index',
        ])->toArray()));

        $pid=$this->pid($data['pcode']);

        DB::table("problem_sample")->where(["pid"=>$pid])->delete();

        if (!empty($data["sample"])) {
            foreach ($data["sample"] as $d) {
                if (!isset($d['sample_note'])) {
                    $d['sample_note']=null;
                }
                DB::table("problem_sample")->insert([
                    'pid'=>$pid,
                    'sample_input'=>$d['sample_input'],
                    'sample_output'=>$d['sample_output'],
                    'sample_note'=>$d['sample_note'],
                ]);
            }
        }

        return $pid;
    }

    public function discussionList($pid)
    {
        $paginator=DB::table('problem_discussion')->join(
            "users",
            "id",
            "=",
            "problem_discussion.uid"
        )->where([
            'problem_discussion.pid'=>$pid,
            'problem_discussion.audit'=>1
        ])->orderBy(
            'problem_discussion.created_at',
            'desc'
        )->select([
            'problem_discussion.pdid',
            'problem_discussion.title',
            'problem_discussion.updated_at',
            'users.avatar',
            'users.name',
            'users.id as uid'
        ])->paginate(15);
        $list=$paginator->all();
        foreach ($list as &$l) {
            $l['updated_at']=formatHumanReadableTime($l['updated_at']);
            $l['comment_count']=DB::table('problem_discussion_comment')->where('pdid', '=', $l['pdid'])->count();
        }
        return [
            'paginator' => $paginator,
            'list' => $list,
        ];
    }

    public function discussionDetail($pdid)
    {
        $main=DB::table('problem_discussion')->join(
            "users",
            "id",
            "=",
            "problem_discussion.uid"
        )->where(
            'problem_discussion.pdid',
            '=',
            $pdid
        )->select([
            'problem_discussion.pdid',
            'problem_discussion.title',
            'problem_discussion.content',
            'problem_discussion.votes',
            'problem_discussion.created_at',
            'users.avatar',
            'users.name',
            'users.id as uid'
        ])->get()->first();
        $main['created_at']=formatHumanReadableTime($main['created_at']);
        $main['content']=clean(Markdown::convertToHtml($main["content"]));

        $comment_count=DB::table('problem_discussion_comment')->where('pdid', '=', $pdid)->count();

        $paginator=DB::table('problem_discussion_comment')->join(
            "users",
            "id",
            "=",
            "problem_discussion_comment.uid"
        )->where([
            'problem_discussion_comment.pdid'=>$pdid,
            'problem_discussion_comment.reply_id'=>null,
            'problem_discussion_comment.audit'=>1
        ])->select([
            'problem_discussion_comment.pdcid',
            'problem_discussion_comment.pdid',
            'problem_discussion_comment.content',
            'problem_discussion_comment.votes',
            'problem_discussion_comment.created_at',
            'users.avatar',
            'users.name',
            'users.id as uid'
        ])->paginate(10);
        $comment=$paginator->all();
        foreach ($comment as &$c) {
            $c['content']=clean(Markdown::convertToHtml($c["content"]));
            $c['created_at']=formatHumanReadableTime($c['created_at']);
            $c['reply']=DB::table('problem_discussion_comment')->join(
                "users",
                "id",
                "=",
                "problem_discussion_comment.uid"
            )->where(
                'problem_discussion_comment.pdid',
                '=',
                $pdid
            )->where(
                'problem_discussion_comment.reply_id',
                '!=',
                null
            )->where(
                'problem_discussion_comment.audit',
                '=',
                1
            )->select([
                'problem_discussion_comment.pdcid',
                'problem_discussion_comment.pdid',
                'problem_discussion_comment.content',
                'problem_discussion_comment.reply_id',
                'problem_discussion_comment.votes',
                'problem_discussion_comment.created_at',
                'users.avatar',
                'users.name',
                'users.id as uid'
            ])->get()->all();
            foreach ($c['reply'] as $k=>&$cr) {
                $cr['content']=clean(Markdown::convertToHtml($cr["content"]));
                $cr['reply_uid']=DB::table('problem_discussion_comment')->where(
                    'pdcid',
                    '=',
                    $cr['reply_id']
                )->get()->first()['uid'];
                $cr['reply_name']=DB::table('users')->where(
                    'id',
                    '=',
                    $cr['reply_uid']
                )->get()->first()['name'];
                $cr['created_at']=formatHumanReadableTime($cr['created_at']);
                if ($this->replyParent($cr['pdcid'])!=$c['pdcid']) {
                    unset($c['reply'][$k]);
                }
            }
        }
        return [
            'main' => $main,
            'comment_count' => $comment_count,
            'paginator' => $paginator,
            'comment' => $comment
        ];
    }

    public function replyParent($pdcid)
    {
        $reply_id=DB::table('problem_discussion_comment')->where('pdcid', '=', $pdcid)->get()->first()['reply_id'];
        $top=DB::table('problem_discussion_comment')->where('pdcid', '=', $reply_id)->get()->first()['reply_id'];
        if (isset($top)) {
            return $this->replyParent($reply_id);
        } else {
            return $reply_id;
        }
    }

    public function pcodeByPdid($dcode)
    {
        $pid=DB::table('problem_discussion')->where('pdid', '=', $dcode)->get()->first()['pid'];
        $pcode=$this->pcode($pid);
        return $pcode;
    }

    public function addDiscussion($uid, $pid, $title, $content)
    {
        $pdid=DB::table("problem_discussion")->insertGetId([
            "uid"=>$uid,
            "pid"=>$pid,
            "title"=>$title,
            "content"=>$content,
            "votes"=>0,
            "audit"=>1,
            "created_at"=>date("Y-m-d H:i:s"),
            "updated_at"=>date("Y-m-d H:i:s"),
        ]);
        return $pdid;
    }

    public function pidByPdid($pdid)
    {
        $pid=DB::table('problem_discussion')->where('pdid', '=', $pdid)->get()->first()['pid'];
        return $pid;
    }

    public function addComment($uid, $pdid, $content, $reply_id)
    {
        $pid=$this->pidByPdid($pdid);
        $pdcid=DB::table('problem_discussion_comment')->insertGetId([
            'pdid'=>$pdid,
            'uid'=>$uid,
            'pid'=>$pid,
            'content'=>$content,
            'reply_id'=>$reply_id,
            'votes'=>0,
            'audit'=>1,
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        return $pdcid;
    }
}
