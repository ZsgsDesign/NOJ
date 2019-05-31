<?php
namespace App\Http\Controllers\VirtualJudge\HDU;

use App\Http\Controllers\VirtualJudge\Curl;
use App\Models\CompilerModel;
use App\Models\JudgerModel;
use Illuminate\Support\Facades\Validator;
use Requests;
use Log;

class HDU extends Curl
{
    protected $sub;
    public $post_data=[];

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
        $judger=new JudgerModel();
        $judger_list=$judger->list(8);
        $this->judgerAccount = $judger_list[array_rand($judger_list)];
    }

    private function hduLogin()
    {
        $response=$this->grab_page('http://acm.hdu.edu.cn','hdu', [], $this->judgerAccount["handle"]);
        if (strpos($response, 'Sign In')!==false) {
            $params=[
                'username' => $this->judgerAccount["handle"],
                'userpass' => $this->judgerAccount["password"],
                'login' => 'Sign In',
            ];
            $this->login('http://acm.hdu.edu.cn/userloginex.php?action=login', http_build_query($params), 'hdu', false, $this->judgerAccount["handle"]);
        }
    }

    private function hduSubmit()
    {
        $params=[
            'problemid' => $this->post_data['iid'],
            'language' => $this->post_data['lang'],
            'usercode' => $this->post_data["solution"],
            'submit' => 'Submit',
        ];

        $response=$this->post_data("http://acm.hdu.edu.cn/submit.php?action=submit", http_build_query($params), "hdu", true, false, true, false, [], $this->judgerAccount['handle']);
        // Log::debug($response);
        $this->sub['jid'] = $this->judgerAccount['jid'];
        $res=Requests::get('http://acm.hdu.edu.cn/status.php?user='.$this->judgerAccount['handle'].'&pid='.$this->post_data['iid']);
        if (!preg_match("/<td height=22px>([\s\S]*?)<\/td>/", $res->body, $match)) {
                $this->sub['verdict']='Submission Error';
        } else {
                $this->sub['remote_id']=$match[1];
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

        $this->hduLogin();
        $this->hduSubmit();
    }
}
