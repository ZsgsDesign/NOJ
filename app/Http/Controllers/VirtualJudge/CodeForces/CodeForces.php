<?php
namespace App\Http\Controllers\VirtualJudge\CodeForces;

use App\Http\Controllers\VirtualJudge\Curl;
use App\Models\JudgerModel;
use App\Models\ProblemModel;
use App\Models\ResponseModel;
use App\Models\SubmissionModel;
use Illuminate\Support\Facades\Validator;

class CodeForces extends Curl
{
    protected $sub;
    public $post_data=[];

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
    }

    private function codeForcesLogin()
    {
        $response=$this->grab_page('http://codeforces.com', 'codeforces');
        if (!(strpos($response, 'Logout')!==false)) {
            $response=$this->grab_page('http://codeforces.com/enter', 'codeforces');

            $exploded=explode("name='csrf_token' value='", $response);
            $token=explode("'/>", $exploded[2])[0];

            $judger=new JudgerModel();
            $judger_list=$judger->list(2);
            $params=[
                'csrf_token' => $token,
                'action' => 'enter',
                'ftaa' => '',
                'bfaa' => '',
                'handleOrEmail' => $judger_list[0]["handle"], //I wanna kill for handleOrEmail
                'password' => $judger_list[0]["password"],
                'remember' => true,
            ];
            $this->login('http://codeforces.com/enter', http_build_query($params), 'codeforces');
        }
    }

    private function codeForcesSubmit()
    {
        // $this->sub['language']=substr($this->post_data["lang"], 2, 50);

        $submissionModel=new SubmissionModel();
        $s_num=$submissionModel->countSolution($this->post_data["solution"]);
        $space='';
        for ($i=0; $i<$s_num; $i++) {
            $space.=' ';
        }
        $contestId=$this->post_data["cid"];
        $submittedProblemIndex=$this->post_data["iid"];
        $var=substr($this->post_data["lang"], 0, 2);
        $programTypeId=$var;
        if ($var[0]==0) {
            $programTypeId=$var[1];
        }
        $source=($space.chr(10).$this->post_data["solution"]);


        $response=$this->grab_page("codeforces.com/contest/{$this->post_data['cid']}/submit", "codeforces");

        $exploded=explode("name='csrf_token' value='", $response);
        $token=explode("'/>", $exploded[2])[0];

        $params=[
            'csrf_token' => $token,
            'action' => 'submitSolutionFormSubmitted',
            'ftaa' => '',
            'bfaa' => '',
            'submittedProblemIndex' => $submittedProblemIndex,
            'programTypeId' => $programTypeId,
            'source' => $source,
            'tabSize' => 4,
            'sourceFile' => '',
        ];
        $response=$this->post_data("codeforces.com/contest/{$this->post_data['cid']}/submit?csrf_token=".$token, http_build_query($params), "codeforces", true);
        if (substr_count($response, 'My Submissions')!=2) {
            // Forbidden?
            $exploded=explode('<span class="error for__source">', $response);
            $this->sub['compile_info']=(null != explode("</span>", $exploded[1])) ? explode("</span>", $exploded[1])[0] : null;
            $this->sub['verdict']="Submission Error";
        }
    }

    public function submit()
    {
        $validator=Validator::make($this->post_data, [
            'pid' => 'required|integer',
            'cid' => 'required|integer',
            'coid' => 'required|integer',
            'iid' => 'required',
            'solution' => 'required',
        ]);

        if ($validator->fails()) {
            $this->sub['verdict']="Submission Error";
            return;
        }

        $this->codeForcesLogin();
        $this->codeForcesSubmit();
    }
}
