<?php
namespace App\Babel\Submit;

use App\Models\SubmissionModel;
use App\Models\JudgerModel;
use App\Models\ProblemModel;
use App\Babel\Submit\Curl;
use App\Http\Controllers\VirtualJudge\NOJ\NOJ;
use App\Http\Controllers\VirtualJudge\CodeForces\CodeForces;
use App\Http\Controllers\VirtualJudge\ContestHunter\ContestHunter;
use App\Http\Controllers\VirtualJudge\POJ\POJ;
use App\Http\Controllers\VirtualJudge\Vijos\Vijos;
use App\Http\Controllers\VirtualJudge\PTA\PTA;
use App\Http\Controllers\VirtualJudge\UVa\UVa;
use Requests;

class Core extends Curl
{
    private $sub;
    public $post_data=[];

    public function __construct(& $sub, $oj, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;

        if ($oj=='noj') {
            $NOJ=new NOJ($sub, $all_data);
            $NOJ->submit();
        }

        if ($oj=='codeforces') {
            $CodeForces=new CodeForces($sub, $all_data);
            $CodeForces->submit();
        }

        if ($oj=='contesthunter') {
            $ContestHunter=new ContestHunter($sub, $all_data);
            $ContestHunter->submit();
        }

        if ($oj=='poj') {
            $POJ=new POJ($sub, $all_data);
            $POJ->submit();
        }

        if ($oj=='vijos') {
            $Vijos=new Vijos($sub, $all_data);
            $Vijos->submit();
        }

        if ($oj=='pta') {
            $PTA=new PTA($sub, $all_data);
            $PTA->submit();
        }

        if ($oj=='uva') {
            $UVa=new UVa($sub, $all_data);
            $UVa->submit();
        }
    }
}







    // protected function uva_live_login($url1, $url2, $oj)
    // {
    //     $response=$this->grab_page($url1, $oj);
    //     if (!(strpos($response, 'Logout') !== false)&&(strpos($response, 'Login') !== false)) {
    //         $exploded = explode('<input type="hidden" name="cbsecuritym3" value="', $response);
    //         $cbsecuritym3 = explode('"', $exploded[1])[0];

    //         $exploded = explode('<input type="hidden" name="return" value="', $response);
    //         $return = explode('"', $exploded[1])[0];

    //         $exploded = explode('<input type="hidden" name="cbsecuritym3" value="', $response);
    //         $exploded = explode('<input type="hidden" name="', $exploded[1]);
    //         $any = explode('"', $exploded[1])[0];


    //         $params = [
    //             'username' => 'codemaster_uva',
    //             'passwd' => '123456',
    //             'op2' => 'login',
    //             'lang' => 'english',
    //             'force_session' => '1',
    //             'return' => $return,
    //             'message' => '0',
    //             'loginfrom' => 'loginmodule',
    //             'cbsecuritym3' =>  $cbsecuritym3,
    //             $any => '1',
    //             'remember' => 'yes',
    //             'Submit' => 'Login',
    //         ];

    //         $data=http_build_query($params);
    //         $this->login($url2, http_build_query($params), $oj);
    //     }
    // }
    // public function uva_live_submit($url, $oj)
    // {
    //     $this->sub['language']=substr($this->post_data["lang"], 1, 50);
    //     $this->sub['solution']=$this->post_data["solution"];
    //     $this->sub['pid']=$this->post_data["pid"];

    //     $code=$this->post_data["solution"];
    //     $lang=substr($this->post_data["lang"], 0, 1);
    //     $pro_id=$this->post_data['iid'];

    //     $params = [
    //         'problemid' => $pro_id,
    //         'category' => '',
    //         'language' => $lang,
    //         'code' => $code,
    //         'codeupl' => '',
    //     ];
    //     $data=http_build_query($params);
    //     $response=$this->post_data($url, $data, $oj, true);
    //     if (substr_count($response, 'Submission+received+with+ID')==0) {
    //         $exploded = explode('mosmsg=', $response);
    //         $this->sub['verdict'] = urldecode(explode('"', $exploded[2])[0]);
    //     }
    // }
    // private function uva()
    // {
    //     if (!isset($this->post_data["pid"])||!isset($this->post_data["iid"])||!isset($_COOKIE["user_handle"])&&!isset($this->post_data["solution"])) {
    //         redirect("/");
    //     }
    //     $response=$this->grab_page('https://uva.onlinejudge.org', 'uva');
    //     if (!(strpos($response, 'UVa Online Judge - Offline') !== false)&&strlen($response)!=0) {
    //         $this->uva_live_login('https://uva.onlinejudge.org', 'https://uva.onlinejudge.org/index.php?option=com_comprofiler&task=login', 'uva');
    //         $this->uva_live_submit('https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=8&page=save_submission', 'uva');
    //     } else {
    //         $this->sub['language']=substr($this->post_data["lang"], 1, 50);
    //         $this->sub['solution']=$this->post_data["solution"];
    //         $this->sub['pid']=$this->post_data["pid"];
    //         $this->sub['verdict']="Judge Error";
    //     }
    // }
    // private function uvalive()
    // {
    //     if (!isset($this->post_data["pid"])||!isset($this->post_data["iid"])||!isset($_COOKIE["user_handle"])&&!isset($this->post_data["solution"])) {
    //         redirect("/");
    //     }
    //     $this->uva_live_login('https://icpcarchive.ecs.baylor.edu', 'https://icpcarchive.ecs.baylor.edu/index.php?option=com_comprofiler&task=login', 'uvalive');
    //     $this->uva_live_submit('https://icpcarchive.ecs.baylor.edu/index.php?option=com_onlinejudge&Itemid=8&page=save_submission', 'uvalive');
    // }
    // public function spoj_login()
    // {
    //     $response=$this->grab_page('http://www.spoj.com', 'spoj');
    //     if (!(strpos($response, 'sign-out') !== false)) {
    //         $params = [
    //             'next_raw' => "/",
    //             'autologin' => '1',
    //             'login_user' => 'codemaster_spoj',
    //             'password' => '123456'
    //         ];

    //         $data=http_build_query($params);
    //         $this->login('http://www.spoj.com/login', $data, 'spoj');
    //     }
    // }
    // public function multiexplode($delimiters, $string)
    // {
    //     $ready = str_replace($delimiters, $delimiters[0], $string);
    //     $launch = explode($delimiters[0], $ready);
    //     return  $launch;
    // }
    // public function spoj_submit()
    // {
    //     $x=0;
    //     for ($i=0;$i<strlen($this->post_data["lang"]);$i++) {
    //         if (is_numeric($this->post_data["lang"][$i])) {
    //             $x++;
    //         } else {
    //             break;
    //         }
    //     }
    //     $this->sub['language']=substr($this->post_data["lang"], $x, strlen($this->post_data["lang"]));
    //     $this->sub['solution']=$this->post_data["solution"];
    //     $this->sub['pid']=$this->post_data["pid"]; // 500A
    //     $lang=substr($this->post_data["lang"], 0, $x);

    //     $params = [
    //         'subm_file' => '',
    //         'file' => $this->post_data["solution"],
    //         'lang' => $lang,
    //         'problemcode' => $this->post_data['iid'],
    //         'submit' => 'Submit!',
    //     ];

    //     $data=http_build_query($params);
    //     $response=$this->post_data('http://www.spoj.com/submit/complete/', $data, 'spoj', true);
    //     if (substr_count($response, 'Solution submitted!')==0) {
    //         $exploded = explode('<p align="center">', $response);
    //         $this->sub['verdict'] = $this->multiexplode(["!","."], $exploded[1])[0];
    //     }
    // }

    // private function spoj()
    // {
    //     if (!isset($this->post_data["pid"])||!isset($this->post_data["iid"])||!isset($this->post_data["iid"])||!isset($_COOKIE["user_handle"])&&!isset($this->post_data["solution"])) {
    //         redirect("/");
    //     }
    //     $this->spoj_login();
    //     $this->spoj_submit();
    // }

