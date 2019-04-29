<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Tool\PastebinModel;

class SubmissionModel extends Model
{
    protected $tableName='submission';
    protected $table='submission';
    protected $primaryKey = 'sid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    public $colorScheme=[
        "Waiting"                => "wemd-blue-text",
        "Judge Error"            => "wemd-black-text",
        "System Error"           => "wemd-black-text",
        "Compile Error"          => "wemd-orange-text",
        "Runtime Error"          => "wemd-red-text",
        "Wrong Answer"           => "wemd-red-text",
        "Time Limit Exceed"      => "wemd-deep-purple-text",
        "Real Time Limit Exceed" => "wemd-deep-purple-text",
        "Accepted"               => "wemd-green-text",
        "Memory Limit Exceed"    => "wemd-deep-purple-text",
        "Presentation Error"     => "wemd-red-text",
        "Submitted"              => "wemd-blue-text",
        "Pending"                => "wemd-blue-text",
        "Judging"                => "wemd-blue-text",
        "Partially Accepted"     => "wemd-cyan-text",
        'Submission Error'       => 'wemd-black-text',
        'Output Limit Exceeded'  => 'wemd-deep-purple-text',
        "Idleness Limit Exceed"  => 'wemd-deep-purple-text'
    ];
    public $langConfig=[];

    public function __construct()
    {
        $tempLangConfig=[[
            "id" => "plaintext",
            "extensions" => [".txt", ".gitignore"],
            "aliases" => ["Plain Text", "text"],
            "mimetypes" => ["text/plain"]
        ], [
            "id" => "json",
            "extensions" => [".json", ".bowerrc", ".jshintrc", ".jscsrc", ".eslintrc", ".babelrc"],
            "aliases" => ["JSON", "json"],
            "mimetypes" => ["application/json"]
        ], [
            "id" => "bat",
            "extensions" => [".bat", ".cmd"],
            "aliases" => ["Batch", "bat"]
        ], [
            "id" => "coffeescript",
            "extensions" => [".coffee"],
            "aliases" => ["CoffeeScript", "coffeescript", "coffee"],
            "mimetypes" => ["text/x-coffeescript", "text/coffeescript"]
        ], [
            "id" => "c",
            "extensions" => [".c", ".h"],
            "aliases" => ["C", "c"]
        ], [
            "id" => "cpp",
            "extensions" => [".cpp", ".cc", ".cxx", ".hpp", ".hh", ".hxx"],
            "aliases" => ["C++", "Cpp", "cpp"]
        ], [
            "id" => "csharp",
            "extensions" => [".cs", ".csx", ".cake"],
            "aliases" => ["C#", "csharp"]
        ], [
            "id" => "csp",
            "extensions" => [],
            "aliases" => ["CSP", "csp"]
        ], [
            "id" => "css",
            "extensions" => [".css"],
            "aliases" => ["CSS", "css"],
            "mimetypes" => ["text/css"]
        ], [
            "id" => "dockerfile",
            "extensions" => [".dockerfile"],
            "filenames" => ["Dockerfile"],
            "aliases" => ["Dockerfile"]
        ], [
            "id" => "fsharp",
            "extensions" => [".fs", ".fsi", ".ml", ".mli", ".fsx", ".fsscript"],
            "aliases" => ["F#", "FSharp", "fsharp"]
        ], [
            "id" => "go",
            "extensions" => [".go"],
            "aliases" => ["Go"]
        ], [
            "id" => "handlebars",
            "extensions" => [".handlebars", ".hbs"],
            "aliases" => ["Handlebars", "handlebars"],
            "mimetypes" => ["text/x-handlebars-template"]
        ], [
            "id" => "html",
            "extensions" => [".html", ".htm", ".shtml", ".xhtml", ".mdoc", ".jsp", ".asp", ".aspx", ".jshtm"],
            "aliases" => ["HTML", "htm", "html", "xhtml"],
            "mimetypes" => ["text/html", "text/x-jshtm", "text/template", "text/ng-template"]
        ], [
            "id" => "ini",
            "extensions" => [".ini", ".properties", ".gitconfig"],
            "filenames" => ["config", ".gitattributes", ".gitconfig", ".editorconfig"],
            "aliases" => ["Ini", "ini"]
        ], [
            "id" => "java",
            "extensions" => [".java", ".jav"],
            "aliases" => ["Java", "java"],
            "mimetypes" => ["text/x-java-source", "text/x-java"]
        ], [
            "id" => "javascript",
            "extensions" => [".js", ".es6", ".jsx"],
            "firstLine" => "^#!.*\\bnode",
            "filenames" => ["jakefile"],
            "aliases" => ["JavaScript", "javascript", "js"],
            "mimetypes" => ["text/javascript"]
        ], [
            "id" => "less",
            "extensions" => [".less"],
            "aliases" => ["Less", "less"],
            "mimetypes" => ["text/x-less", "text/less"]
        ], [
            "id" => "lua",
            "extensions" => [".lua"],
            "aliases" => ["Lua", "lua"]
        ], [
            "id" => "markdown",
            "extensions" => [".md", ".markdown", ".mdown", ".mkdn", ".mkd", ".mdwn", ".mdtxt", ".mdtext"],
            "aliases" => ["Markdown", "markdown"]
        ], [
            "id" => "msdax",
            "extensions" => [".dax", ".msdax"],
            "aliases" => ["DAX", "MSDAX"]
        ], [
            "id" => "mysql",
            "extensions" => [],
            "aliases" => ["MySQL", "mysql"]
        ], [
            "id" => "objective-c",
            "extensions" => [".m"],
            "aliases" => ["Objective-C"]
        ], [
            "id" => "pgsql",
            "extensions" => [],
            "aliases" => ["PostgreSQL", "postgres", "pg", "postgre"]
        ], [
            "id" => "php",
            "extensions" => [".php", ".php4", ".php5", ".phtml", ".ctp"],
            "aliases" => ["PHP", "php"],
            "mimetypes" => ["application/x-php"]
        ], [
            "id" => "postiats",
            "extensions" => [".dats", ".sats", ".hats"],
            "aliases" => ["ATS", "ATS/Postiats"]
        ], [
            "id" => "powerquery",
            "extensions" => [".pq", ".pqm"],
            "aliases" => ["PQ", "M", "Power Query", "Power Query M"]
        ], [
            "id" => "powershell",
            "extensions" => [".ps1", ".psm1", ".psd1"],
            "aliases" => ["PowerShell", "powershell", "ps", "ps1"]
        ], [
            "id" => "pug",
            "extensions" => [".jade", ".pug"],
            "aliases" => ["Pug", "Jade", "jade"]
        ], [
            "id" => "python",
            "extensions" => [".py", ".rpy", ".pyw", ".cpy", ".gyp", ".gypi"],
            "aliases" => ["Python", "py"],
            "firstLine" => "^#!/.*\\bpython[0-9.-]*\\b"
        ], [
            "id" => "r",
            "extensions" => [".r", ".rhistory", ".rprofile", ".rt"],
            "aliases" => ["R", "r"]
        ], [
            "id" => "razor",
            "extensions" => [".cshtml"],
            "aliases" => ["Razor", "razor"],
            "mimetypes" => ["text/x-cshtml"]
        ], [
            "id" => "redis",
            "extensions" => [".redis"],
            "aliases" => ["redis"]
        ], [
            "id" => "redshift",
            "extensions" => [],
            "aliases" => ["Redshift", "redshift"]
        ], [
            "id" => "ruby",
            "extensions" => [".rb", ".rbx", ".rjs", ".gemspec", ".pp"],
            "filenames" => ["rakefile"],
            "aliases" => ["Ruby", "rb"]
        ], [
            "id" => "rust",
            "extensions" => [".rs", ".rlib"],
            "aliases" => ["Rust", "rust"]
        ], [
            "id" => "sb",
            "extensions" => [".sb"],
            "aliases" => ["Small Basic", "sb"]
        ], [
            "id" => "scss",
            "extensions" => [".scss"],
            "aliases" => ["Sass", "sass", "scss"],
            "mimetypes" => ["text/x-scss", "text/scss"]
        ], [
            "id" => "sol",
            "extensions" => [".sol"],
            "aliases" => ["sol", "solidity", "Solidity"]
        ], [
            "id" => "sql",
            "extensions" => [".sql"],
            "aliases" => ["SQL"]
        ], [
            "id" => "st",
            "extensions" => [".st", ".iecst", ".iecplc", ".lc3lib"],
            "aliases" => ["StructuredText", "scl", "stl"]
        ], [
            "id" => "swift",
            "aliases" => ["Swift", "swift"],
            "extensions" => [".swift"],
            "mimetypes" => ["text/swift"]
        ], [
            "id" => "typescript",
            "extensions" => [".ts", ".tsx"],
            "aliases" => ["TypeScript", "ts", "typescript"],
            "mimetypes" => ["text/typescript"]
        ], [
            "id" => "vb",
            "extensions" => [".vb"],
            "aliases" => ["Visual Basic", "vb"]
        ], [
            "id" => "xml",
            "extensions" => [".xml", ".dtd", ".ascx", ".csproj", ".config", ".wxi", ".wxl", ".wxs", ".xaml", ".svg", ".svgz"],
            "firstLine" => "(\\<\\?xml.*)|(\\<svg)|(\\<\\!doctype\\s+svg)",
            "aliases" => ["XML", "xml"],
            "mimetypes" => ["text/xml", "application/xml", "application/xaml+xml", "application/xml-dtd"]
        ], [
            "id" => "yaml",
            "extensions" => [".yaml", ".yml"],
            "aliases" => ["YAML", "yaml", "YML", "yml"],
            "mimetypes" => ["application/x-yaml"]
        ], [
            "id" => "scheme",
            "extensions" => [".scm", ".ss", ".sch", ".rkt"],
            "aliases" => ["scheme", "Scheme"]
        ], [
            "id" => "clojure",
            "extensions" => [".clj", ".clojure"],
            "aliases" => ["clojure", "Clojure"]
        ], [
            "id" => "shell",
            "extensions" => [".sh", ".bash"],
            "aliases" => ["Shell", "sh"]
        ], [
            "id" => "perl",
            "extensions" => [".pl"],
            "aliases" => ["Perl", "pl"]
        ], [
            "id" => "azcli",
            "extensions" => [".azcli"],
            "aliases" => ["Azure CLI", "azcli"]
        ], [
            "id" => "apex",
            "extensions" => [".cls"],
            "aliases" => ["Apex", "apex"],
            "mimetypes" => ["text/x-apex-source", "text/x-apex"]
        ]];
        foreach ($tempLangConfig as $t) {
            $this->langConfig[$t["id"]]=$t;
        }
    }

    public function insert($sub)
    {
        if (strlen($sub['verdict'])==0) {
            $sub['verdict']="Judge Error";
        }

        $sid=DB::table($this->tableName)->insertGetId([
            'time' => $sub['time'],
            'verdict' => $sub['verdict'],
            'solution' => $sub['solution'],
            'language' => $sub['language'],
            'submission_date' => $sub['submission_date'],
            'memory' => $sub['memory'],
            'uid' => $sub['uid'],
            'pid' => $sub['pid'],
            'cid' => $sub['cid'],
            'color' => $this->colorScheme[$sub['verdict']],
            'remote_id'=>$sub['remote_id'],
            'compile_info'=>"",
            'coid'=>$sub['coid'],
            'score'=>$sub['score']
        ]);

        return $sid;
    }

    public function getJudgeStatus($sid, $uid)
    {
        $status=DB::table($this->tableName)->where(['sid'=>$sid])->first();
        if (empty($status)) {
            return [];
        }
        if ($status["share"]==1 && $status["cid"]) {
            $end_time=strtotime(DB::table("contest")->where(["cid"=>$status["cid"]])->select("end_time")->first()["end_time"]);
            if(time()<$end_time) $status["solution"]=null;
        }
        if ($status["share"]==0 && $status["uid"]!=$uid) {
            $status["solution"]=null;
        }
        $compilerModel=new CompilerModel();
        $status["lang"]=$compilerModel->detail($status["coid"])["lang"];
        $status["owner"]=$uid==$status["uid"];
        return $status;
    }

    public function downloadCode($sid, $uid)
    {
        $status=DB::table($this->tableName)->where(['sid'=>$sid])->first();
        if (empty($status) || ($status["share"]==0 && $status["uid"]!=$uid)) {
            return [];
        }
        $lang=DB::table("compiler")->where(['coid'=>$status["coid"]])->first()["lang"];
        $curLang=isset($this->langConfig[$lang]) ? $this->langConfig[$lang] : $this->langConfig["plaintext"];
        return [
            "content"=>$status["solution"],
            "name"=>$status["submission_date"].$curLang["extensions"][0],
        ];
    }

    public function getProblemStatus($pid, $uid, $cid=null)
    {
        if ($cid) {
            $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);
            // Get the very first AC record
            $ac=DB::table($this->tableName)->where([
                'pid'=>$pid,
                'uid'=>$uid,
                'cid'=>$cid,
                'verdict'=>'Accepted'
            ])->where("submission_date", "<", $end_time)->orderBy('submission_date', 'desc')->first();
            if (empty($ac)) {
                $pac=DB::table($this->tableName)->where([
                    'pid'=>$pid,
                    'uid'=>$uid,
                    'cid'=>$cid,
                    'verdict'=>'Partially Accepted'
                ])->where("submission_date", "<", $end_time)->orderBy('submission_date', 'desc')->first();
                return empty($pac) ? DB::table($this->tableName)->where(['pid'=>$pid, 'uid'=>$uid, 'cid'=>$cid])->where("submission_date", "<", $end_time)->orderBy('submission_date', 'desc')->first() : $pac;
            } else {
                return $ac;
            }
        } else {
            $ac=DB::table($this->tableName)->where([
                'pid'=>$pid,
                'uid'=>$uid,
                'cid'=>$cid,
                'verdict'=>'Accepted'
            ])->orderBy('submission_date', 'desc')->first();
            return empty($ac) ? DB::table($this->tableName)->where(['pid'=>$pid, 'uid'=>$uid, 'cid'=>$cid])->orderBy('submission_date', 'desc')->first() : $ac;
        }
    }

    public function getProblemSubmission($pid, $uid, $cid=null)
    {
        $statusList=DB::table($this->tableName)->where(['pid'=>$pid, 'uid'=>$uid, 'cid'=>$cid])->orderBy('submission_date', 'desc')->limit(10)->get()->all();
        return $statusList;
    }

    public function countSolution($s)
    {
        return DB::table($this->tableName)->where(['solution'=>$s])->count();
    }

    public function getEarliestSubmission($oid)
    {
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->select("sid", "OJ as oid", "remote_id", "cid")
                                            ->where(['verdict'=>'Waiting', 'OJ'=>$oid])
                                            ->orderBy("sid", "asc")
                                            ->first();
    }

    public function countEarliestWaitingSubmission($oid)
    {
        $early_sid=$this->getEarliestSubmission($oid);
        if ($early_sid==null) {
            return 0;
        }
        $early_sid=$early_sid["sid"];
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->where(['OJ'=>$oid])
                                            ->where("sid", ">=", $early_sid)
                                            ->count();
    }


    public function getWaitingSubmission()
    {
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->select("sid", "OJ as oid", "remote_id", "cid", "jid")
                                            ->where(['verdict'=>'Waiting'])
                                            ->get();
    }

    public function countWaitingSubmission($oid)
    {
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->where(['verdict'=>'Waiting', 'OJ'=>$oid])
                                            ->count();
    }

    public function updateSubmission($sid, $sub)
    {
        if (isset($sub['verdict'])) {
            $sub["color"]=$this->colorScheme[$sub['verdict']];
        }
        return DB::table($this->tableName)->where(['sid'=>$sid])->update($sub);
    }

    public function formatSubmitTime($date)
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

    public function getRecord()
    {
        $paginator=DB::table("submission")->where([
            'cid'=>null
        ])->join(
            "users",
            "users.id",
            "=",
            "submission.uid"
        )->join(
            "problem",
            "problem.pid",
            "=",
            "submission.pid"
        )->select(
            "sid",
            "uid",
            "problem.pid as pid",
            "pcode",
            "name",
            "color",
            "verdict",
            "time",
            "memory",
            "language",
            "score",
            "submission_date",
            "share"
        )->orderBy(
            'submission_date',
            'desc'
        )->paginate(50);


        $records=$paginator->all();
        foreach ($records as &$r) {
            $r["submission_date_parsed"]=$this->formatSubmitTime(date('Y-m-d H:i:s', $r["submission_date"]));
            $r["submission_date"]=date('Y-m-d H:i:s', $r["submission_date"]);
            $r["nick_name"]="";
        }
        return [
            "paginator"=>$paginator,
            "records"=>$records
        ];
    }

    public function share($sid,$uid)
    {
        $basic=DB::table($this->tableName)->where(['sid'=>$sid,'uid'=>$uid])->first();
        if(empty($basic)) return [];
        DB::table($this->tableName)->where(['sid'=>$sid])->update([
            "share"=>$basic["share"]?0:1
        ]);
        return [
            "share"=>$basic["share"]?0:1
        ];
    }

    public function sharePB($sid,$uid)
    {
        $basic=DB::table($this->tableName)->where(['sid'=>$sid,'uid'=>$uid])->first();
        $problem=DB::table("problem")->where(['pid'=>$basic["pid"]])->first();
        $compiler=DB::table("compiler")->where(['coid'=>$basic["coid"]])->first();
        if(empty($basic)) return [];
        $pastebinModel=new PastebinModel();
        $ret=$pastebinModel->generate([
            "syntax"=>$compiler["lang"],
            "expiration"=>0,
            "content"=>$basic["solution"],
            "title"=>$problem["pcode"]." - ".$basic["verdict"],
            "uid"=>$uid
        ]);

        if(is_null($ret)){
            return ResponseModel::err(1001);
        }else{
            return ResponseModel::success(200, null, [
                "code" => $ret
            ]);
        }
    }
}
