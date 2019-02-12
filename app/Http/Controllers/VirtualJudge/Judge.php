<?php
namespace App\Http\Controllers\VirtualJudge;

use App\Models\Submission;
use App\Models\Judger;
use App\Http\Controllers\VirtualJudge\Core;

class Judge extends Core
{
    private $MODEL;
    public function __construct()
    {
        $this->MODEL=new Judger();
        $ret=array();

        $uva_v=array(
            'Wrong answer'=>"Wrong Answer",
            'Accepted'=>"Accepted",
            'Runtime error'=>"Runtime Error",
            'Time limit exceeded'=>"Time Limit Exceed",
            'Presentation error'=>"Presentation Error",
            'Submission error'=>'Submission Error',
            'Compilation error'=>"Compile Error",
            'Output Limit Exceeded'=>"Output limit exceeded",
            );

        $codeforces_v=array("COMPILATION_ERROR"=>"Compile Error","RUNTIME_ERROR"=> "Runtime Error","WRONG_ANSWER"=> "Wrong Answer",
            "TIME_LIMIT_EXCEEDED"=>"Time Limit Exceed" ,"OK"=>"Accepted" ,"MEMORY_LIMIT_EXCEEDED"=>"Memory Limit Exceed","PRESENTATION_ERROR"=>"Presentation Error");

        $spoj_v=array("compilation error"=>"Compile Error","runtime error"=> "Runtime Error","wrong answer"=> "Wrong Answer",
            "time limit exceeded"=>"Time Limit Exceed" ,"accepted"=>"Accepted");

        $result=$this->MODEL->get_wating_submission();

        $cf=$this->get_last_codeforces($this->MODEL->count_wating_submission('CodeForces'));
        $uva=$this->get_last_uva($this->MODEL->count_wating_submission('Uva'));
        $uval=$this->get_last_uvalive($this->MODEL->count_wating_submission('UvaLive'));
        $sj=$this->get_last_spoj($this->MODEL->count_wating_submission('Spoj'));

        $color=array("Waiting"=>"teal","Judge Error"=>"BLACK","Compile Error"=>"darkorange","Runtime Error"=>"darkred","Wrong Answer"=>"darkred",
                "Time Limit Exceed"=>"peru","Accepted"=>"darkgreen","Memory Limit Exceed"=>"peru","Presentation Error"=>"darkred","Judging"=>"darkred"
                ,'Submission Error'=>'BLUE','Output limit exceeded'=>'peru'
                );



        $i=0;
        $j=0;
        $k=0;
        $l=0;
        while ($row=mysqli_fetch_assoc($result)) {
            if ($row['from_oj']=='CodeForces') {
                if (isset($codeforces_v[$cf[$i][2]])) {
                    $sub['Verdict']=$codeforces_v[$cf[$i][2]];
                    $sub['TIME']=$cf[$i][0];
                    $sub['memory']=$cf[$i][1];
                    //array_push($ret,$row['submission_id']=>$sub['Verdict']);
                    $v=$sub['Verdict'];
                    $ret[$row['submission_id']]="<div style='color:{$color[$v]};'>"  .$sub['Verdict']. "</div>";
                    $this->MODEL->update_submission($row['submission_id'], $sub);
                }
                $i++;
            }
            if ($row['from_oj']=='Spoj') {
                if (isset($spoj_v[$sj[$j][2]])) {
                    $sub['Verdict']=$spoj_v[$sj[$j][2]];
                    $sub['TIME']=$sj[$j][0];
                    $sub['memory']=$sj[$j][1];
                    //array_push($ret,$row['submission_id']=>$sub['Verdict']);
                    $v=$sub['Verdict'];
                    $ret[$row['submission_id']]="<div style='color:{$color[$v]};'>"  .$sub['Verdict']. "</div>";
                    $this->MODEL->update_submission($row['submission_id'], $sub);
                }
                $j++;
            }
            if ($row['from_oj']=='Uva') {
                if (isset($uva_v[$uva[$k][2]])) {
                    $sub['Verdict']=$uva_v[$uva[$k][2]];
                    $sub['TIME']=$uva[$k][0];
                    $sub['memory']=$uva[$k][1];
                    //array_push($ret,$row['submission_id']=>$sub['Verdict']);
                    $v=$sub['Verdict'];
                    $ret[$row['submission_id']]="<div style='color:{$color[$v]};'>"  .$sub['Verdict']. "</div>";
                    $this->MODEL->update_submission($row['submission_id'], $sub);
                }
                $k++;
            }
            if ($row['from_oj']=='UvaLive') {
                if (isset($uva_v[$uval[$l][2]])) {
                    $sub['Verdict']=$uva_v[$uval[$l][2]];
                    $sub['TIME']=$uval[$l][0];
                    $sub['memory']=$uval[$l][1];
                    //array_push($ret,$row['submission_id']=>$sub['Verdict']);
                    $v=$sub['Verdict'];
                    $ret[$row['submission_id']]="<div style='color:{$color[$v]};'>"  .$sub['Verdict']. "</div>";
                    $this->MODEL->update_submission($row['submission_id'], $sub);
                }
                $l++;
            }
        }
        echo json_encode($ret);
    }
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
    private function get_last_codeforces($num)
    {
        $ret=array();
        if ($num==0) {
            return $ret;
        }

        $ch=curl_init();
        $url="http://codeforces.com/api/user.status?handle=Our_Judge&from=1&count={$num}";
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
                array_push($ret, array($result["result"][$i]["timeConsumedMillis"],$result["result"][$i]["memoryConsumedBytes"]/1000,$result["result"][$i]["verdict"]));
            }
        }
        return array_reverse($ret);
    }
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
    $controller=new statusController;
