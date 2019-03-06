<?php
namespace App\Http\Controllers\VirtualJudge\POJ;

use App\Http\Controllers\VirtualJudge\Curl;
use App\Models\CompilerModel;
use App\Models\JudgerModel;
use Illuminate\Support\Facades\Validator;

class POJ extends Curl
{
    protected $sub;
    public $post_data=[];

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
    }

    private function pojLogin()
    {
        $response=$this->grab_page('http://poj.org', 'poj');
        if (strpos($response, 'Log Out') === false) {

            $judger=new JudgerModel();
            $judger_list=$judger->list(4);
            $params = [
                'user_id1' => $judger_list[0]["handle"],
                'password1' => $judger_list[0]["password"],
                'B1' => 'login',
            ];
            $this->login('http://poj.org/login', http_build_query($params), 'poj');
        }
    }

    private function pojSubmit()
    {
        $compilerModel = new CompilerModel();
        $lang = $compilerModel->detail($this->post_data["coid"]);
        $this->sub['language']=$lang['display_name'];
        $this->sub['solution']=$this->post_data["solution"];
        $this->sub['pid']=$this->post_data["pid"];
        $this->sub['coid']=$this->post_data["coid"];
        if (isset($this->post_data["contest"])) {
            $this->sub['cid']=$this->post_data["contest"];
        } else {
            $this->sub['cid']=null;
        }

        $params = [
            'problem_id' => $this->post_data['iid'],
            'language' => $lang['lcode'],
            'source' => base64_encode($this->post_data["solution"]),
            'encoded' => 1, // Optional, but sometimes base64 seems smaller than url encode
        ];
        $response=$this->post_data("http://poj.org/submit", http_build_query($params), "poj", true, false);
        if (!preg_match('/Location: .*\/status/', $response, $match)) {
            $this->sub['verdict'] = 'Submission Error';
        }
    }

    public function submit()
    {
        Validator::make($this->post_data, [
            'pid' => 'required|integer',
            'coid' => 'required|integer',
            'iid' => 'required|integer',
            'solution' => 'required',
        ])->validate();

        $this->pojLogin();
        $this->pojSubmit();
    }
}
