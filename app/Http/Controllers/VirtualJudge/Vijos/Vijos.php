<?php
namespace App\Http\Controllers\VirtualJudge\Vijos;

use App\Http\Controllers\VirtualJudge\Curl;
use App\Models\CompilerModel;
use App\Models\JudgerModel;
use Illuminate\Support\Facades\Validator;
use Requests;

class Vijos extends Curl
{
    protected $sub;
    public $post_data=[];

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
    }

    private function ojLogin()
    {
        $response=$this->grab_page('https://vijos.org', 'vijos');
        if (strpos($response, '登出') === false) {
            $judger=new JudgerModel();
            $judger_list=$judger->list(5);
            $params = [
                'uname' => $judger_list[0]["handle"],
                'password' => $judger_list[0]["password"],
                'rememberme' => 'on',
            ];
            $this->login('https://vijos.org/login', http_build_query($params), 'vijos');
        }
    }

    private function submitSolution()
    {
        $compilerModel=new CompilerModel();
        $lang=$compilerModel->detail($this->post_data["coid"]);
        $pid=$this->post_data['iid'];
        $this->sub['language']=$lang['display_name'];

        $response=$this->grab_page("https://vijos.org/p/{$pid}/submit", 'vijos');
        preg_match('/"csrf_token":"([0-9a-f]{64})"/', $response, $match);

        $params=[
            'lang' => $lang['lcode'],
            'code' => $this->post_data["solution"],
            'csrf_token' => $match[1],
        ];
        $response=$this->post_data("https://vijos.org/p/{$pid}/submit", http_build_query($params), "vijos", true, false);
        if (preg_match('/\nLocation: \/records\/(.+)/i', $response, $match)) {
            $this->sub['remote_id']=$match[1];
        } else {
            $this->sub['verdict']='Submission Error';
        }
    }

    public function submit()
    {
        $validator = Validator::make($this->post_data, [
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
