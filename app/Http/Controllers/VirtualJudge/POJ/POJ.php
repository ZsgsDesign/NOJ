<?php
namespace App\Http\Controllers\VirtualJudge\POJ;

use App\Http\Controllers\VirtualJudge\Curl;
use App\Models\CompilerModel;
use App\Models\JudgerModel;
use Illuminate\Support\Facades\Validator;
use Requests;

class POJ extends Curl
{
    protected $sub;
    public $post_data=[];
    protected $selectedJudger;

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
        $judger=new JudgerModel();
        $judger_list=$judger->list(4);
        $this->selectedJudger=$judger_list[array_rand($judger_list)];
    }

    private function pojLogin()
    {
        $response=$this->grab_page('http://poj.org', 'poj', [], $this->selectedJudger["handle"]);
        if (strpos($response, 'Log Out')===false) {
            $params=[
                'user_id1' => $this->selectedJudger["handle"],
                'password1' => $this->selectedJudger["password"],
                'B1' => 'login',
            ];
            $this->login('http://poj.org/login', http_build_query($params), 'poj', true, $this->selectedJudger["handle"]);
        }
    }

    private function pojSubmit()
    {
        $params=[
            'problem_id' => $this->post_data['iid'],
            'language' => $this->post_data['lang'],
            'source' => base64_encode($this->post_data["solution"]),
            'encoded' => 1, // Optional, but sometimes base64 seems smaller than url encode
        ];

        $response=$this->post_data("http://poj.org/submit", http_build_query($params), "poj", true, false, true, false, [], $this->selectedJudger["handle"]);

        if (!preg_match('/Location: .*\/status/', $response, $match)) {
            $this->sub['verdict']='Submission Error';
        } else {
            $res=Requests::get('http://poj.org/status?problem_id='.$this->post_data['iid'].'&user_id='.urlencode($this->selectedJudger["handle"]));
            if (!preg_match('/<tr align=center><td>(\d+)<\/td>/', $res->body, $match)) {
                $this->sub['verdict']='Submission Error';
            } else {
                $this->sub['remote_id']=$match[1];
            }
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

        $this->pojLogin();
        $this->pojSubmit();
    }
}
