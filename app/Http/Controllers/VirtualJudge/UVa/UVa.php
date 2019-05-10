<?php
namespace App\Http\Controllers\VirtualJudge\UVa;

use App\Http\Controllers\VirtualJudge\Curl;
use App\Models\JudgerModel;
use App\Models\ProblemModel;
use App\Models\ResponseModel;
use App\Models\SubmissionModel;
use App\Models\CompilerModel;
use Illuminate\Support\Facades\Validator;
use Requests;

class UVa extends Curl
{
    protected $sub;
    public $post_data=[];
    protected $selectedJudger;

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
        $judger=new JudgerModel();
        $judger_list=$judger->list(7);
        $this->selectedJudger=$judger_list[array_rand($judger_list)];
    }

    private function ojLogin()
    {
        $response=$this->grab_page("https://uva.onlinejudge.org/", 'uva', [], $this->selectedJudger['handle']);
        if (strpos($response, 'Logout')===false) {
            $post_data=[
                'username' => $this->selectedJudger["handle"],
                'passwd' => $this->selectedJudger["password"],
                'remember' => 'yes',
            ];
            $inputs=preg_match_all('/<input type="\w*" name="(op2|lang|force_session|return|message|loginfrom|cbsecuritym3|\w[0-9a-z]{32})" value="(.*?)" \/>/', $response, $matches);
            for ($i=0; $i<$inputs; ++$i) {
                $post_data[$matches[1][$i]]=$matches[2][$i];
            }
            $this->post_data('https://uva.onlinejudge.org/index.php?option=com_comprofiler&task=login', $post_data, 'uva', false, false, false, false, [], $this->selectedJudger['handle']);
        }
    }

    private function submitSolution()
    {
        $params=[
            'problemid'=>$this->post_data['iid'],
            'language'=>$this->post_data['lang'],
            'code'=>$this->post_data['solution'],
        ];

        $response=$this->post_data("https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=25&page=save_submission", $params, 'uva', true, false, true, false, [], $this->selectedJudger['handle']);
        $this->sub['jid']=$this->selectedJudger["jid"];
        if (preg_match('/Submission\+received\+with\+ID\+(\d+)/', $response, $match)) {
            $this->sub['remote_id']=$match[1];
        } else {
            $this->sub['verdict']='Submission Error';
        }
    }

    public function submit()
    {
        $validator=Validator::make($this->post_data, [
            'pid' => 'required|integer',
            'coid' => 'required|integer',
            'iid' => 'required|integer',
            'solution' => 'required',
        ]);

        if ($validator->fails()) {
            $this->sub['verdict']="System Error";
            return;
        }

        $this->ojLogin();
        $this->submitSolution();
    }
}
