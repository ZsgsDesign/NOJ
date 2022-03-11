<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @deprecated 0.18.0 No longer accepts new methods, will be removed in the future.
 */
class ProblemModel extends Model
{
    protected $table='problem';
    protected $primaryKey='pid';
    const UPDATED_AT="update_date";

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
    public function basic($pid)
    {
        return DB::table($this->table)->where("pid", $pid)->first();
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
    public function removeSolution($psoid, $uid)
    {
        if (empty(DB::table("problem_solution")->where(['psoid'=>$psoid, 'uid'=>$uid])->first())) {
            return false;
        }
        DB::table("problem_solution")->where(['psoid'=>$psoid, 'uid'=>$uid])->delete();
        return true;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
    public function pid($pcode)
    {
        $temp=DB::table($this->table)->where(["pcode"=>$pcode])->select("pid")->first();
        return empty($temp) ? 0 : $temp["pid"];
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
    public function clearTags($pid)
    {
        DB::table("problem_tag")->where(["pid"=>$pid])->delete();
        return true;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
    public function addTags($pid, $tag)
    {
        DB::table("problem_tag")->insert(["pid"=>$pid, "tag"=>$tag]);
        return true;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
    public function getSolvedCount($oid)
    {
        return DB::table($this->table)->select("pid", "solved_count")->where(["OJ"=>$oid])->get()->all();
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
    public function updateDifficulty($pid, $diff_level)
    {
        DB::table("problem_tag")->where(["pid"=>$pid])->update(["difficulty"=>$diff_level]);
        return true;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
    public function pidByPdid($pdid)
    {
        $pid=DB::table('problem_discussion')->where('pdid', '=', $pdid)->get()->first()['pid'];
        return $pid;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Problem::class` instead.
     */
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
