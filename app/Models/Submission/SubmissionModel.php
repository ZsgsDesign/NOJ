<?php

namespace App\Models\Submission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ContestModel;
use App\Models\CompilerModel;

class SubmissionModel extends Model
{
    protected $tableName='submission';
    protected $table='submission';
    protected $primaryKey='sid';
    protected $extractModels=[
        "ShareModel"=>null,
        "StatusModel"=>null
    ];
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
        $this->extractModels["ShareNodel"]=new ShareModel($this);
        $this->extractModels["StatusModel"]=new StatusModel($this);
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
            "id" => "haskell",
            "extensions" => [".hs"],
            "aliases" => ["Haskell", "haskell"]
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

    public function basic($sid)
    {
        return DB::table($this->tableName)->where(['sid'=>$sid])->first();
    }

    public function getJudgeStatus($sid, $uid)
    {
        return $this->extractModels["StatusModel"]->getJudgeStatus($sid, $uid);
    }

    public function downloadCode($sid, $uid)
    {
        return $this->extractModels["StatusModel"]->downloadCode($sid, $uid);
    }

    public function getProblemStatus($pid, $uid, $cid=null)
    {
        return $this->extractModels["StatusModel"]->getProblemStatus($pid, $uid, $cid);
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
                                            ->select("sid", "OJ as oid", "remote_id", "cid", "jid")
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
        $ret=DB::table($this->tableName)    ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->select("sid", "OJ as oid", "remote_id", "cid", "jid", "vcid", "problem.pid as pid")
                                            ->where(['verdict'=>'Waiting'])
                                            ->get()
                                            ->all();
        foreach ($ret as &$r) {
            $r["ocode"]=DB::table("oj")->where(["oid"=>$r["oid"]])->first()["ocode"];
        }
        return $ret;
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
        $result=DB::table($this->tableName)->where(['sid'=>$sid])->update($sub);
        $contestModel=new ContestModel();
        $submission_info=DB::table($this->tableName) -> where(['sid'=>$sid]) -> get() -> first();
        if ($result==1 && $submission_info['cid'] && $contestModel->isContestRunning($submission_info['cid'])) {
            $sub['pid']=$submission_info['pid'];
            $sub['uid']=$submission_info['uid'];
            $sub['cid']=$submission_info['cid'];
            $sub['sid']=$sid;
            // $contestModel->updateContestRankTable($submission_info['cid'],$sub);
        }
        return $result;
    }

    public function getRecord($filter)
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
        );

        if ($filter["pcode"]) {
            $paginator=$paginator->where(["pcode"=>$filter["pcode"]]);
        }

        if ($filter["result"]) {
            $paginator=$paginator->where(["verdict"=>$filter["result"]]);
        }

        if ($filter["account"]) {
            $paginator=$paginator->where(["name"=>$filter["account"]]);
        }

        $paginator=$paginator->paginate(50);


        $records=$paginator->all();
        foreach ($records as &$r) {
            $r["submission_date_parsed"]=formatHumanReadableTime(date('Y-m-d H:i:s', $r["submission_date"]));
            $r["submission_date"]=date('Y-m-d H:i:s', $r["submission_date"]);
            $r["nick_name"]="";
        }
        return [
            "paginator"=>$paginator,
            "records"=>$records
        ];
    }

    public function share($sid, $uid)
    {
        return $this->extractModels["ShareNodel"]->share($sid, $uid);
    }

    public function sharePB($sid, $uid)
    {
        return $this->extractModels["ShareNodel"]->sharePB($sid, $uid);
    }
}
