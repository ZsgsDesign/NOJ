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
    protected $selectedJudger;

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
        $judger=new JudgerModel();
        $judger_list=$judger->list(2);
        $this->selectedJudger=$judger_list[array_rand($judger_list)];
    }

    private function codeForcesLogin()
    {
        $response=$this->grab_page('http://codeforces.com', 'codeforces', [], $this->selectedJudger["handle"]);
        if (!(strpos($response, 'Logout')!==false)) {
            $response=$this->grab_page('http://codeforces.com/enter', 'codeforces', [], $this->selectedJudger["handle"]);

            $exploded=explode("name='csrf_token' value='", $response);
            $token=explode("'/>", $exploded[2])[0];

            $params=[
                'csrf_token' => $token,
                'action' => 'enter',
                'ftaa' => '',
                'bfaa' => '',
                'handleOrEmail' => $this->selectedJudger["handle"], //I wanna kill for handleOrEmail
                'password' => $this->selectedJudger["password"],
                'remember' => true,
            ];
            $this->login('http://codeforces.com/enter', http_build_query($params), 'codeforces', false, $this->selectedJudger["handle"]);
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


        $response=$this->grab_page("codeforces.com/contest/{$this->post_data['cid']}/submit", "codeforces", [], $this->selectedJudger["handle"]);

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
        $response=$this->post_data("codeforces.com/contest/{$this->post_data['cid']}/submit?csrf_token=".$token, http_build_query($params), "codeforces", true, true, true, false, [], $this->selectedJudger["handle"]);
        $this->sub["jid"]=$this->selectedJudger["jid"];
        if (strpos($response, 'alert("Source code hasn\'t submitted because of warning, please read it.");')!==false) {
            $this->sub['verdict']='Compile Error';
            preg_match('/<div class="roundbox " style="font-size:1.2rem;margin:0.5em 0;padding:0.5em;text-align:left;background-color:#eca;">[\s\S]*?<div class="roundbox-rb">&nbsp;<\/div>([\s\S]*?)<div/', $response, $match);
            $warning=str_replace('Press button to submit the solution.', '', $match[1]);
            $this->sub['compile_info']=trim($warning);
        } elseif (substr_count($response, 'My Submissions')!=2) {
            file_put_contents(base_path('storage/logs/'.time().'.html'), $response);
            // Forbidden?
            $exploded=explode('<span class="error for__source">', $response);
            if (!isset($exploded[1])) {
                $this->sub['verdict']="Submission Error";
            } else {
                $this->sub['compile_info']=explode("</span>", $exploded[1])[0];
                $this->sub['verdict']="Submission Error";
            }
        } else {
            preg_match('/submissionId="(\d+)"/', $response, $match);
            $this->sub['remote_id']=$match[1];
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
