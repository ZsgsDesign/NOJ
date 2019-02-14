<?php

namespace App\Http\Controllers\VirtualCrawler;

use App\Http\Controllers\VirtualCrawler\Crawler;
use Auth;

class CodeForces extends Crawler
{
    /**
     * Initial
     *
     * @return Response
     */
    public function __construct()
    {
        if(isset($_GET['lev']))
        {
            update_level_codeforces();
            exit;
        }
        if(isset($_GET['con']))
        codeforces($_GET['con']);
        else codeforces('all');
    }

    public function Extract_CodeForces($cid,$num,$url,$default_desc="") {
        global $config;global $pro;
        $pid=$cid.$num;
        $content=get_url($url);
        $content_type=get_headers($url,1)["Content-Type"];
        if (stripos($content,"<title>Codeforces</title>")===false) {
            if (stripos($content,"<title>Attachments")!==false) {
                $pro["description"].=$default_desc;
            }else{
                $first_step = explode( '<div class="input-file"><div class="property-title">input</div>' , $content );
                $second_step = explode("</div>" , $first_step[1] );
                $pro["input_type"] = $second_step[0];
                $first_step = explode( '<div class="output-file"><div class="property-title">output</div>' , $content );
                $second_step = explode("</div>" , $first_step[1] );
                $pro["output_type"]= $second_step[0];

                if (stripos($content_type,"text/html")!==false) {
                    //if (preg_match("/<div class=\"title\">$num\\. (.*)<\\/div>/sU", $content,$matches)) $pro["name"]=trim(html_entity_decode($matches[1]));
                    if (preg_match("/time limit per test<\\/div>(.*) second/sU", $content,$matches)) $pro["time_limit"]=intval(trim($matches[1]))*1000;
                    if (preg_match("/memory limit per test<\\/div>(.*) megabyte/sU", $content,$matches)) $pro["memory_limit"]=intval(trim($matches[1]))*1024;
                    if (preg_match("/output<\\/div>.*<div>(<p>.*)<\\/div>/sU", $content,$matches)) $pro["description"].=trim(($matches[1]));
                    if (preg_match("/Input<\\/div>(.*)<\\/div>/sU", $content,$matches)) $pro["input"]=trim($matches[1]);
                    if (preg_match("/Output<\\/div>(.*)<\\/div>/sU", $content,$matches)) $pro["output"]=trim($matches[1]);
                    //if (preg_match("/Example\\(s\\)<\\/div>(.*<\\/div><\\/div>)<\\/div>/sU", $content,$matches)) $pro["sample_input"]=trim($matches[1]);
                    //echo "Sam<br>";
                    $pro["sample_input"]=explode( '<div class="sample-test">',$content)[1];
                    if(!(strpos($content, '<div class="note">') !== false))
                    {
                        $pro["sample_input"]=explode( '<script type="text/javascript">',$pro["sample_input"])[0];

                    }
                    else
                    {
                        $pro["sample_input"]=explode( '<div class="note">',$pro["sample_input"])[0];
                    }
                    //echo $pro["sample_input"];exit;
                    $pro["sample_output"]="";
                    if (preg_match("/Note<\\/div>(.*)<\\/div><\\/div>/sU", $content,$matches)) $pro["notes"]=trim(($matches[1]));
                    if (preg_match("/<th class=\"left\" style=\"width:100%;\">(.*)<\\/th>/sU", $content,$matches)) $pro["source"]=trim(strip_tags($matches[1]));
                }else{
                    if(stripos($content_type,"application/pdf")!==false) $ext="pdf";
                    else if(stripos($content_type,"application/msword")!==false) $ext="doc";
                    else if(stripos($content_type,"application/application/vnd.openxmlformats-officedocument.wordprocessingml.document")!==false) $ext="docx";
                    file_put_contents($config["base_local_path"]."external/gym/$cid$num.$ext",$content);
                    $pro["description"].="<a href=\"external/gym/$cid$num.$ext\">[Attachment Link]</a>";
                }
            }
        }
        else return false;
    }


    public function CodeForces($con)
    {
        global $db,$pro;
        $start=time();
        $ch=curl_init();
        $url="http://codeforces.com/api/problemset.problems";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $response=curl_exec($ch);
        curl_close($ch);
        $result=json_decode($response,true);
        if($result["status"]=="OK")
        {
            $now=time()-$start;
            $f = fopen("codeforces_status.txt", "w") or die("Unable to open file!");
            fwrite($f, "CodeForces api Success at {$now}".PHP_EOL);
            for($i=count($result['result']['problems'])-1;$i>=0;$i--)
            {

                /*if(IS_RUN('codeforces')=="false")
                {
                    $now=time()-$start;
                    fwrite($f, "codeforces Force Stop At {$now}".PHP_EOL);
                    return;
                }*/

                foreach($pro as $x=>$y)
                    $pro[$x]='';
                if($con!='all')if($con!=$result['result']['problems'][$i]['contestId'])continue;

                $pro['url'] = "http://codeforces.com/contest/{$result['result']['problems'][$i]['contestId']}/problem/{$result['result']['problems'][$i]['index']}";
                $pro['name']=str_replace('"',"'",$result['result']['problems'][$i]['name']);
                $pro['solved_count']=$result['result']['problemStatistics'][$i]['solvedCount'];
                $pro['id']=$result['result']['problems'][$i]['contestId'].$result['result']['problems'][$i]['index'];
                $pro['ind']=$result['result']['problems'][$i]['index'];
                $pro['contest_id']=$result['result']['problems'][$i]['contestId'];
                $pro['from_oj']='CodeForces';

                $now=time()-$start;
                fwrite($f, "P{$pro['id']} start at {$now}".PHP_EOL);

                Extract_CodeForces($pro['contest_id'],$pro['ind'],$pro['url'] );

                foreach($pro as $x=>$y)
                    $pro[$x]=mysqli_real_escape_string($db,$y);



                $query="SELECT problem_id FROM problem where id='{$pro['id']}' AND from_oj='CodeForces'";
                $res=mysqli_query($db,$query);
                if(!$res)
                {
                    die("query failed "." ".mysqli_error($db));
                }
                $row=mysqli_num_rows($res);
                $new;
                if($row!=0)
                {
                    $r=mysqli_fetch_row($res);
                    $query="DELETE FROM problem_category where problem_id={$r[0]}";
                    $rr=mysqli_query($db,$query);
                    if(!$rr)
                    {
                        die("query failed samples"." ".mysqli_error($db));
                    }
                    $new=update_problem();
                }
                else $new=insert_problem();




                for($j=0;$j<count($result['result']['problems'][$i]['tags']);$j++)
                {
                    $query="INSERT INTO problem_category(problem_id,category_name) ";
                    $query.="VALUES({$new},'{$result['result']['problems'][$i]['tags'][$j]}')";
                    $res=mysqli_query($db,$query);
                    if(!$res)
                    {
                        die("query failed Category"." ".mysqli_error($db));
                    }
                }

                $now=time()-$start;
                fwrite($f, "P{$pro['id']} end at {$now}".PHP_EOL);

            }
            $now=time()-$start;
            fwrite($f, "Updata All Problems end at {$now}".PHP_EOL);
            fclose($f);

        }
    }
}
