<?php
namespace App\Http\Controllers\VirtualJudge\ContestHunter;

use App\Http\Controllers\VirtualJudge\Curl;
use App\Models\JudgerModel;
use App\Models\ProblemModel;
use App\Models\ResponseModel;
use App\Models\SubmissionModel;
use Illuminate\Support\Facades\Validator;

class ContestHunter extends Curl
{
    protected $sub;
    public $post_data=[];

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
    }

    private function contestHunterLogin()
    {
        $response=$this->grab_page('http://contest-hunter.org:83', 'contesthunter');
        if (strpos($response, '登录') !== false) {
            preg_match('/<input name="CSRFToken" type="hidden" value="([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})"\/>/', $response, $match);
            $token = $match[1];

            $judger=new JudgerModel();
            $judger_list=$judger->list(3);
            $params = [
                'CSRFToken' => $token,
                'username' => $judger_list[0]["handle"],
                'password' => $judger_list[0]["password"],
                'keepOnline' => 'on',
            ];
            $this->login('http://contest-hunter.org:83/login', http_build_query($params), 'contesthunter');
        }
    }

    private function contestHunterSubmit()
    {
        $this->sub['language']=$this->post_data["lang"] == "CPP" ? "C++" : $this->post_data["lang"];
        $this->sub['solution']=$this->post_data["solution"];
        $this->sub['pid']=$this->post_data["pid"];
        $this->sub['coid']=$this->post_data["coid"];
        if (isset($this->post_data["contest"])) {
            $this->sub['cid']=$this->post_data["contest"];
        } else {
            $this->sub['cid']=null;
        }

        $response=$this->grab_page("http://contest-hunter.org:83/contest/{$this->post_data['cid']}/{$this->post_data['iid']}", "contesthunter");

        preg_match('/<input name="CSRFToken" type="hidden" value="([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})"\/>/', $response, $match);
        $token = $match[1];

        $params = [
            'CSRFToken' => $token,
            'language' => $this->post_data["lang"],
            'code' => base64_encode(mb_convert_encoding($this->post_data["solution"], 'utf-16', 'utf-8')),
        ];
        $response=$this->post_data("http://contest-hunter.org:83/contest/{$this->post_data['cid']}/{$this->post_data['iid']}?submit", http_build_query($params), "contesthunter", true, false);
        if (preg_match('/\nLocation: \/record\/(\d+)/i', $response, $match)) {
            $this->sub['remote_id'] = $match[1];
        } else {
            $this->sub['verdict'] = 'Submission Error';
        }
    }

    public function submit()
    {
        Validator::make($this->post_data, [
            'pid' => 'required|integer',
            'cid' => 'required',
            'coid' => 'required|integer',
            'iid' => 'required',
            'solution' => 'required',
        ])->validate();

        $this->contestHunterLogin();
        $this->contestHunterSubmit();
    }
}
