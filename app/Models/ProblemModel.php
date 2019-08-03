<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Cache;

class ProblemModel extends Model
{
    protected $table='problem';
    protected $primaryKey='pid';
    const UPDATED_AT="update_date";

    public function detail($pcode, $cid=null)
    {
        $prob_detail=DB::table($this->table)->where("pcode", $pcode)->first();
        // [Depreciated] Joint Query was depreciated here for code maintenance reasons
        if (!is_null($prob_detail)) {
            if ($prob_detail["force_raw"]) {
                $prob_detail["parsed"]=[
                    "description"=>$prob_detail["description"],
                    "input"=>$prob_detail["input"],
                    "output"=>$prob_detail["output"],
                    "note"=>$prob_detail["note"]
                ];
            } elseif ($prob_detail["markdown"]) {
                $prob_detail["parsed"]=[
                    "description"=>clean(Markdown::convertToHtml($prob_detail["description"])),
                    "input"=>clean(Markdown::convertToHtml($prob_detail["input"])),
                    "output"=>clean(Markdown::convertToHtml($prob_detail["output"])),
                    "note"=>clean(Markdown::convertToHtml($prob_detail["note"]))
                ];
            } else {
                $prob_detail["parsed"]=[
                    "description"=>$prob_detail["description"],
                    "input"=>$prob_detail["input"],
                    "output"=>$prob_detail["output"],
                    "note"=>$prob_detail["note"]
                ];
            }
            $prob_detail["update_date"]=date_format(date_create($prob_detail["update_date"]), 'm/d/Y H:i:s');
            $prob_detail["oj_detail"]=DB::table("oj")->where("oid", $prob_detail["OJ"])->first();
            $prob_detail["samples"]=DB::table("problem_sample")->where("pid", $prob_detail["pid"])->get()->all();
            $prob_detail["tags"]=DB::table("problem_tag")->where("pid", $prob_detail["pid"])->get()->all();
            if ($cid) {
                $frozen_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);
                $prob_stat=DB::table("submission")->select(
                    DB::raw("count(sid) as submission_count"),
                    DB::raw("sum(verdict='accepted') as passed_count"),
                    DB::raw("sum(verdict='accepted')/count(sid)*100 as ac_rate")
                )->where([
                    "pid"=>$prob_detail["pid"],
                    "cid"=>$cid,
                ])->where("submission_date", "<", $frozen_time)->first();
                $prob_detail['vcid']=DB::table("contest")->where(["cid"=>$cid])->select("vcid")->first()['vcid'];
                $prob_detail["points"]=DB::table("contest_problem")->where(["cid"=>$cid, "pid"=>$prob_detail["pid"]])->select("points")->first()["points"];
            } else {
                $prob_stat=DB::table("submission")->select(
                    DB::raw("count(sid) as submission_count"),
                    DB::raw("sum(verdict='accepted') as passed_count"),
                    DB::raw("sum(verdict='accepted')/count(sid)*100 as ac_rate")
                )->where(["pid"=>$prob_detail["pid"]])->first();
                $prob_detail['vcid']=null;
                $prob_detail["points"]=0;
            }
            if ($prob_stat["submission_count"]==0) {
                $prob_detail["submission_count"]=0;
                $prob_detail["passed_count"]=0;
                $prob_detail["ac_rate"]=0;
            } else {
                $prob_detail["submission_count"]=$prob_stat["submission_count"];
                $prob_detail["passed_count"]=$prob_stat["passed_count"];
                $prob_detail["ac_rate"]=round($prob_stat["ac_rate"], 2);
            }
        }
        return $prob_detail;
    }

    public function basic($pid)
    {
        return DB::table($this->table)->where("pid", $pid)->first();
    }

    public function tags()
    {
        return DB::table("problem_tag")->groupBy('tag')->select("tag", DB::raw('count(*) as tag_count'))->orderBy('tag_count', 'desc')->limit(12)->get()->all();
    }

    public function ojs()
    {
        return DB::table("oj")->orderBy('oid', 'asc')->limit(12)->get()->all();
    }

    public function ojdetail($oid)
    {
        return DB::table("oj")->where('oid', $oid)->first();
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
            $d["content_parsed"]=clean(Markdown::convertToHtml($d["content"]));
        }
        return $details;
    }

    public function solution($pid, $uid)
    {
        $details=DB::table("problem_solution")->join("users", "id", "=", "uid")->where(['pid'=>$pid, 'uid'=>$uid])->first();
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
                "audit"=>0,
                "created_at"=>date("Y-m-d H:i:s"),
                "updated_at"=>date("Y-m-d H:i:s"),
            ]);
            return true;
        }
        return false;
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
            "audit"=>0,
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

    public function list($filter, $uid=null)
    {
        // $prob_list = DB::table($this->table)->select("pid","pcode","title")->get()->all(); // return a array
        $submissionModel=new SubmissionModel();
        $preQuery=DB::table($this->table);
        if ($filter['oj']) {
            $preQuery=$preQuery->where(["OJ"=>$filter['oj']]);
        }
        if ($filter['tag']) {
            $preQuery=$preQuery->join("problem_tag", "problem.pid", "=", "problem_tag.pid")->where(["tag"=>$filter['tag']]);
        }
        $paginator=$preQuery->select("problem.pid as pid", "pcode", "title")->orderBy(
            "OJ",
            "ASC"
        )->orderBy(
            DB::raw("length(contest_id)"),
            "ASC"
        )->orderBy(
            "contest_id",
            "ASC"
        )->orderBy(
            DB::raw("length(index_id)"),
            "ASC"
        )->orderBy(
            "index_id",
            "ASC"
        )->orderBy(
            "pcode",
            "ASC"
        )->paginate(20);
        $prob_list=$paginator->all();

        if (empty($prob_list)) {
            return null;
        }
        foreach ($prob_list as &$p) {
            $prob_stat=DB::table("submission")->select(
                DB::raw("count(sid) as submission_count"),
                DB::raw("sum(verdict='accepted') as passed_count"),
                DB::raw("sum(verdict='accepted')/count(sid)*100 as ac_rate")
            )->where(["pid"=>$p["pid"]])->first();
            if ($prob_stat["submission_count"]==0) {
                $p["submission_count"]=0;
                $p["passed_count"]=0;
                $p["ac_rate"]=0;
            } else {
                $p["submission_count"]=$prob_stat["submission_count"];
                $p["passed_count"]=$prob_stat["passed_count"];
                $p["ac_rate"]=round($prob_stat["ac_rate"], 2);
            }
            if (!is_null($uid)) {
                $prob_status=$submissionModel->getProblemStatus($p["pid"], $uid);
                if (empty($prob_status)) {
                    $p["prob_status"]=[
                        "icon"=>"checkbox-blank-circle-outline",
                        "color"=>"wemd-grey-text"
                    ];
                } else {
                    $p["prob_status"]=[
                        "icon"=>$prob_status["verdict"]=="Accepted" ? "checkbox-blank-circle" : "cisco-webex",
                        "color"=>$prob_status["color"]
                    ];
                }
            } else {
                $p["prob_status"]=[
                    "icon"=>"checkbox-blank-circle-outline",
                    "color"=>"wemd-grey-text"
                ];
            }
        }
        return [
            'paginator' => $paginator,
            'problems' => $prob_list,
        ];
    }

    public function existPCode($pcode)
    {
        $temp=DB::table($this->table)->where(["pcode"=>$pcode])->select("pcode")->first();
        return empty($temp) ? null : $temp["pcode"];
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
        $pid=DB::table($this->table)->insertGetId([
            'difficulty'=>-1,
            'file'=>$data['file'],
            'title'=>$data['title'],
            'time_limit'=>$data['time_limit'],
            'memory_limit'=>$data['memory_limit'],
            'OJ'=>$data['OJ'],
            'description'=>$data['description'],
            'input'=>$data['input'],
            'output'=>$data['output'],
            'note'=>$data['note'],
            'input_type'=>$data['input_type'],
            'output_type'=>$data['output_type'],
            'pcode'=>$data['pcode'],
            'contest_id'=>$data['contest_id'],
            'index_id'=>$data['index_id'],
            'origin'=>$data['origin'],
            'source'=>$data['source'],
            'solved_count'=>$data['solved_count'],
            'update_date'=>date("Y-m-d H:i:s"),
            'tot_score'=>$data['tot_score'],
            'partial'=>$data['partial'],
            'markdown'=>$data['markdown'],
            'special_compiler'=>$data['special_compiler'],
        ]);

        if (!empty($data["sample"])) {
            foreach ($data["sample"] as $d) {
                DB::table("problem_sample")->insert([
                    'pid'=>$pid,
                    'sample_input'=>$d['sample_input'],
                    'sample_output'=>$d['sample_output'],
                ]);
            }
        }

        return $pid;
    }

    public function updateProblem($data)
    {
        DB::table($this->table)->where(["pcode"=>$data['pcode']])->update([
            'difficulty'=>-1,
            'file'=>$data['file'],
            'title'=>$data['title'],
            'time_limit'=>$data['time_limit'],
            'memory_limit'=>$data['memory_limit'],
            'OJ'=>$data['OJ'],
            'description'=>$data['description'],
            'input'=>$data['input'],
            'output'=>$data['output'],
            'note'=>$data['note'],
            'input_type'=>$data['input_type'],
            'output_type'=>$data['output_type'],
            'contest_id'=>$data['contest_id'],
            'index_id'=>$data['index_id'],
            'origin'=>$data['origin'],
            'source'=>$data['source'],
            'solved_count'=>$data['solved_count'],
            'update_date'=>date("Y-m-d H:i:s"),
            'tot_score'=>$data['tot_score'],
            'partial'=>$data['partial'],
            'markdown'=>$data['markdown'],
            'special_compiler'=>$data['special_compiler'],
        ]);

        $pid=$this->pid($data['pcode']);

        DB::table("problem_sample")->where(["pid"=>$pid])->delete();

        if (!empty($data["sample"])) {
            foreach ($data["sample"] as $d) {
                DB::table("problem_sample")->insert([
                    'pid'=>$pid,
                    'sample_input'=>$d['sample_input'],
                    'sample_output'=>$d['sample_output'],
                ]);
            }
        }

        return $pid;
    }

    public function discussionList($pid)
    {
        $paginator = DB::table('problem_discussion')->join(
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
        $list = $paginator->all();
        foreach($list as &$l){
            $l['updated_at'] = $this->formatTime($l['updated_at']);
            $l['comment_count'] = DB::table('problem_discussion_comment')->where('pdid','=',$l['pdid'])->count();
        }
        return [
            'paginator' => $paginator,
            'list' => $list,
        ];
    }

    public function formatTime($date)
    {
        $periods=["second", "minute", "hour", "day", "week", "month", "year", "decade"];
        $lengths=["60", "60", "24", "7", "4.35", "12", "10"];

        $now=time();
        $unix_date=strtotime($date);

        if (empty($unix_date)) {
            return "Bad date";
        }

        if ($now>$unix_date) {
            $difference=$now-$unix_date;
            $tense="ago";
        } else {
            $difference=$unix_date-$now;
            $tense="from now";
        }

        for ($j=0; $difference>=$lengths[$j] && $j<count($lengths)-1; $j++) {
            $difference/=$lengths[$j];
        }

        $difference=round($difference);

        if ($difference!=1) {
            $periods[$j].="s";
        }

        return "$difference $periods[$j] {$tense}";
    }

    public function discussionDetail($pdid)
    {
        $main = DB::table('problem_discussion')->join(
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
        $main['created_at'] = $this->formatTime($main['created_at']);

        $paginator = DB::table('problem_discussion_comment')->join(
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
        $comment = $paginator->all();
        foreach($comment as &$c){
            $c['created_at'] = $this->formatTime($c['created_at']);
            $c['reply'] = DB::table('problem_discussion_comment')->join(
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
                'problem_discussion.audit',
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
            foreach($c['reply'] as &$cr){
                $cr['reply_uid'] = DB::table('problem_discussion_comment')->where(
                    'pdcid',
                    '=',
                    $cr['reply_id']
                )->get()->first()['uid'];
                $cr['reply_name'] = DB::table('users')->where(
                    'id',
                    '=',
                    $cr['reply_uid']
                )->get()->first()['name'];
                $cr['created_at'] = $this->formatTime($cr['created_at']);
            }
        }


        return [
            'main' => $main,
            'paginator' => $paginator,
            'comment' => $comment
        ];
    }

    public function pcodeByPdid($dcode)
    {
        $pid = DB::table('problem_discussion')->where('pdid','=',$dcode)->get()->first()['pid'];
        $pcode = $this->pcode($pid);
        return $pcode;
    }
}
