<?php
namespace App\Http\Controllers\VirtualJudge;

use App\Models\SubmissionModel;
use App\Models\JudgerModel;
use App\Http\Controllers\VirtualJudge\Core;

class Judge extends Core
{
    private $MODEL;
    public $ret=[];
    public function __construct()
    {
        $this->MODEL=new SubmissionModel();
        $ret=[];

        $uva_v=[
            'Wrong answer'=>"Wrong Answer",
            'Accepted'=>"Accepted",
            'Runtime error'=>"Runtime Error",
            'Time limit exceeded'=>"Time Limit Exceed",
            'Presentation error'=>"Presentation Error",
            'Submission error'=>'Submission Error',
            'Compilation error'=>"Compile Error",
            'Output Limit Exceeded'=>"Output limit Exceeded",
        ];

        $codeforces_v=[
            "COMPILATION_ERROR"=>"Compile Error",
            "RUNTIME_ERROR"=> "Runtime Error",
            "WRONG_ANSWER"=> "Wrong Answer",
            "TIME_LIMIT_EXCEEDED"=>"Time Limit Exceed" ,
            "OK"=>"Accepted" ,
            "MEMORY_LIMIT_EXCEEDED"=>"Memory Limit Exceed",
            "PRESENTATION_ERROR"=>"Presentation Error"];

        $spoj_v=[
            "compilation error"=>"Compile Error",
            "runtime error"=> "Runtime Error",
            "wrong answer"=> "Wrong Answer",
            "time limit exceeded"=>"Time Limit Exceed",
            "accepted"=>"Accepted"
        ];

        $result=$this->MODEL->get_wating_submission();

        $cf=$this->get_last_codeforces($this->MODEL->count_wating_submission(2));
        // $uva=$this->get_last_uva($this->MODEL->count_wating_submission('Uva'));
        // $uval=$this->get_last_uvalive($this->MODEL->count_wating_submission('UvaLive'));
        // $sj=$this->get_last_spoj($this->MODEL->count_wating_submission('Spoj'));

        $i=0;
        $j=0;
        $k=0;
        $l=0;

        foreach ($result as $row) {
            if ($row['oid']==2) {
                if (isset($codeforces_v[$cf[$i][2]])) {

                    $sub['verdict'] = $codeforces_v[$cf[$i][2]];
                    $sub['time'] = $cf[$i][0];
                    $sub['memory'] = $cf[$i][1];
                    $sub['remote_id'] = $cf[$i][3];

                    $ret[$row['sid']] = [
                        "verdict"=>$sub['verdict']
                    ];

                    $this->MODEL->update_submission($row['sid'], $sub);
                }
                $i++;
            }
            // if ($row['oid']=='Spoj') {
            //     if (isset($spoj_v[$sj[$j][2]])) {
            //         $sub['verdict']=$spoj_v[$sj[$j][2]];
            //         $sub['time']=$sj[$j][0];
            //         $sub['memory']=$sj[$j][1];
            //         $v=$sub['verdict'];
            //         $ret[$row['sid']]="<div style='color:{$color[$v]};'>"  .$sub['Verdict']. "</div>";
            //         $this->MODEL->update_submission($row['sid'], $sub);
            //     }
            //     $j++;
            // }
            // if ($row['oid']=='Uva') {
            //     if (isset($uva_v[$uva[$k][2]])) {
            //         $sub['verdict']=$uva_v[$uva[$k][2]];
            //         $sub['time']=$uva[$k][0];
            //         $sub['memory']=$uva[$k][1];
            //         $v=$sub['verdict'];
            //         $ret[$row['sid']]="<div style='color:{$color[$v]};'>"  .$sub['Verdict']. "</div>";
            //         $this->MODEL->update_submission($row['sid'], $sub);
            //     }
            //     $k++;
            // }
            // if ($row['oid']=='UvaLive') {
            //     if (isset($uva_v[$uval[$l][2]])) {
            //         $sub['verdict']=$uva_v[$uval[$l][2]];
            //         $sub['time']=$uval[$l][0];
            //         $sub['memory']=$uval[$l][1];
            //         $v=$sub['verdict'];
            //         $ret[$row['sid']]="<div style='color:{$color[$v]};'>"  .$sub['Verdict']. "</div>";
            //         $this->MODEL->update_submission($row['sid'], $sub);
            //     }
            //     $l++;
            // }
        }
        $this->ret=$ret;
    }
    /**
     * [Not Finished] Get last time UVa submission by using API.
     *
     * @param integer $num
     *
     * @return void
     */
    private function get_last_uva($num)
    {
        $ret=array();
        if ($num==0) {
            return $ret;
        }
        $response=$this->grab_page('https://uva.onlinejudge.org', 'uva');
        if (!(strpos($response, 'UVa Online Judge - Offline') !== false)) {
            $this->uva_live_login('https://uva.onlinejudge.org', 'https://uva.onlinejudge.org/index.php?option=com_comprofiler&task=login', 'uva');
        } else {
            return $ret;
        }

        $i=0;
        while (true) {
            $response=$this->grab_page("https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=9&limit=50&limitstart={$i}", 'uva');

            $exploded = explode('<table cellpadding="4" cellspacing="0" border="0" width="100%">', $response);
            $table = explode('</table>', $exploded[1])[0];

            $table = explode('<tr class="sectiontableentry', $table);

            for ($j=1;$j<count($table);$j++) {
                $num--;
                $sub=$table[$j];

                $sub = explode('<td>', $sub);
                $verdict=explode('</td>', $sub[3])[0];
                $time=explode('</td>', $sub[5])[0];

                if ((strpos($verdict, '<a href=') !== false)) {
                    $verdict=explode('</a', explode('>', explode('<a href=', $verdict)[1])[1])[0];
                }

                array_push($ret, array($time*1000,-1,$verdict));

                if ($num==0) {
                    return array_reverse($ret);
                }
            }
            $i+=50;
        }
    }

    /**
     * [Not Finished] Get last time UVa Live submission by using API.
     *
     * @param integer $num
     *
     * @return void
     */
    private function get_last_uvalive($num)
    {
        $ret=array();
        if ($num==0) {
            return $ret;
        }

        $this->uva_live_login('https://icpcarchive.ecs.baylor.edu', 'https://icpcarchive.ecs.baylor.edu/index.php?option=com_comprofiler&task=login', 'uvalive');

        $i=0;
        while (true) {
            $response=$this->grab_page("https://icpcarchive.ecs.baylor.edu/index.php?option=com_onlinejudge&Itemid=9&limit=50&limitstart={$i}", 'uvalive');

            $exploded = explode('<table cellpadding="4" cellspacing="0" border="0" width="100%">', $response);
            $table = explode('</table>', $exploded[1])[0];

            $table = explode('<tr class="sectiontableentry', $table);

            for ($j=1;$j<count($table);$j++) {
                $num--;
                $sub=$table[$j];

                $sub = explode('<td>', $sub);
                $verdict=explode('</td>', $sub[3])[0];
                $time=explode('</td>', $sub[5])[0];

                if ((strpos($verdict, '<a href=') !== false)) {
                    $verdict=explode('</a', explode('>', explode('<a href=', $verdict)[1])[1])[0];
                }

                array_push($ret, array($time*1000,-1,$verdict));

                if ($num==0) {
                    return array_reverse($ret);
                }
            }
            $i+=50;
        }
    }


    /**
     * [Not Finished] Get last time CodeForces submission by using API.
     *
     * @param integer $num
     *
     * @return void
     */
    private function get_last_codeforces($num)
    {
        $ret=array();
        if ($num==0) {
            return $ret;
        }

        $ch=curl_init();
        $url="http://codeforces.com/api/user.status?handle=codemaster4&from=1&count={$num}";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response=curl_exec($ch);
        curl_close($ch);
        $result=json_decode($response, true);
        if ($result["status"]=="OK") {
            for ($i=0;$i<$num;$i++) {
                if (!isset($result["result"][$i]["verdict"])) {
                    return array_reverse($ret);
                }
                array_push($ret, array($result["result"][$i]["timeConsumedMillis"],$result["result"][$i]["memoryConsumedBytes"]/1000,$result["result"][$i]["verdict"],$result["result"][$i]["id"]));
            }
        }
        return array_reverse($ret);
    }

    /**
     * [Not Finished] Get last time SPOJ submission by using API.
     *
     * @param integer $num
     *
     * @return void
     */
    private function get_last_spoj($num)
    {
        $ret=array();
        if ($num==0) {
            return $ret;
        }

        $i=0;
        while (true) {
            $response=file_get_contents("http://www.spoj.com/status/our_judge/all/start={$i}");


            $exploded = explode('<table class="problems table newstatus">', $response);
            $table = explode('</table>', $exploded[1])[0];

            $table = explode('<td class="statustext text-center">', $table);

            for ($j=1;$j<count($table);$j++) {
                $num--;
                $sub=$table[$j];


                $verdict=explode('</td>', explode('manual="0">', explode('<td class="statusres text-center"', $sub)[1])[1])[0];
                if ((strpos($verdict, '<strong>') !== false)) {
                    $verdict=explode('</strong>', explode('<strong>', $verdict)[1])[0];
                }

                if ((strpos($verdict, '(') !== false)) {
                    $verdict=explode('(', $verdict)[0];
                }
                if (is_numeric(trim($verdict))) {
                    $verdict='accepted';
                }

                $time=explode('</a>', explode('title="See the best solutions">', $sub)[1])[0];
                $time=trim($time);
                if ($time=='-') {
                    $time=0;
                }

                $memory=explode('</td', explode('>', explode('<td class="smemory statustext text-center"', $sub)[1])[1])[0];
                $memory=trim($memory);
                if ($memory=='-') {
                    $memory=0;
                } else {
                    $memory=substr($memory, 0, strlen($memory)-1);
                }

                array_push($ret, array($time*1000,$memory*1000,trim($verdict)));
                if ($num==0) {
                    return array_reverse($ret);
                }
            }
            $i+=20;
        }
    }
}
