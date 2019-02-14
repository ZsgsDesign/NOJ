<?php

namespace App\Http\Controllers\VirtualCrawler;

use App\Http\Controllers\VirtualCrawler\Crawler;
use App\Models\ProblemModel;
use Auth;

class CodeForces extends Crawler
{
    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($con='all')
    {
        set_time_limit(0); // Pandora's box, engage!
        $this->Codeforces($con);
    }

    public function Extract_CodeForces($cid, $num, $url, $default_desc="")
    {
        $pid=$cid.$num;
        $content=get_url($url);
        $content_type=get_headers($url, 1)["Content-Type"];
        if (stripos($content, "<title>Codeforces</title>")===false) {
            if (stripos($content, "<title>Attachments")!==false) {
                $this->pro["description"].=$default_desc;
            } else {
                $first_step = explode('<div class="input-file"><div class="property-title">input</div>', $content);
                $second_step = explode("</div>", $first_step[1]);
                $this->pro["input_type"] = $second_step[0];
                $first_step = explode('<div class="output-file"><div class="property-title">output</div>', $content);
                $second_step = explode("</div>", $first_step[1]);
                $this->pro["output_type"]= $second_step[0];

                if (stripos($content_type, "text/html")!==false) {

                    if (preg_match("/time limit per test<\\/div>(.*) second/sU", $content, $matches)) {
                        $this->pro["time_limit"]=intval(trim($matches[1]))*1000;
                    }
                    if (preg_match("/memory limit per test<\\/div>(.*) megabyte/sU", $content, $matches)) {
                        $this->pro["memory_limit"]=intval(trim($matches[1]))*1024;
                    }
                    if (preg_match("/output<\\/div>.*<div>(<p>.*)<\\/div>/sU", $content, $matches)) {
                        $this->pro["description"].=trim(($matches[1]));
                    }
                    if (preg_match("/Input<\\/div>(.*)<\\/div>/sU", $content, $matches)) {
                        $this->pro["input"]=trim($matches[1]);
                    }
                    if (preg_match("/Output<\\/div>(.*)<\\/div>/sU", $content, $matches)) {
                        $this->pro["output"]=trim($matches[1]);
                    }

                    $this->pro["sample_input"]=explode('<div class="sample-test">', $content)[1];
                    if (!(strpos($content, '<div class="note">') !== false)) {
                        $this->pro["sample_input"]=explode('<script type="text/javascript">', $this->pro["sample_input"])[0];
                    } else {
                        $this->pro["sample_input"]=explode('<div class="note">', $this->pro["sample_input"])[0];
                    }

                    $this->pro["sample_output"]="";
                    if (preg_match("/Note<\\/div>(.*)<\\/div><\\/div>/sU", $content, $matches)) {
                        $this->pro["notes"]=trim(($matches[1]));
                    }
                    if (preg_match("/<th class=\"left\" style=\"width:100%;\">(.*)<\\/th>/sU", $content, $matches)) {
                        $this->pro["source"]=trim(strip_tags($matches[1]));
                    }

                } else {
                    if (stripos($content_type, "application/pdf")!==false) {
                        $ext="pdf";
                    } elseif (stripos($content_type, "application/msword")!==false) {
                        $ext="doc";
                    } elseif (stripos($content_type, "application/vnd.openxmlformats-officedocument.wordprocessingml.document")!==false) {
                        $ext="docx";
                    }
                    file_put_contents(base_path("public/external/gym/$cid$num.$ext"), $content);
                    $this->pro["description"].="<a href=\"external/gym/$cid$num.$ext\">[Attachment Link]</a>";
                }
            }
        } else {
            return false;
        }
    }


    public function CodeForces($con)
    {
        $problemModel=new ProblemModel();
        $start=time();
        $ch=curl_init();
        $url="http://codeforces.com/api/problemset.problems";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response=curl_exec($ch);
        curl_close($ch);
        $result=json_decode($response, true);
        if ($result["status"]=="OK") {
            $now=time()-$start;
            $f = fopen(__DIR__."/codeforces_status.txt", "w") or die("Unable to open file!");
            fwrite($f, "CodeForces API Success at {$now}".PHP_EOL);
            for ($i=count($result['result']['problems'])-1;$i>=0;$i--) {

                foreach ($this->pro as $x=>$y) {
                    $this->pro[$x]='';
                }

                if ($con!='all') {
                    if ($con!=$result['result']['problems'][$i]['contestId']) {
                        continue;
                    }
                }

                $this->pro['origin'] = "http://codeforces.com/contest/{$result['result']['problems'][$i]['contestId']}/problem/{$result['result']['problems'][$i]['index']}";
                $this->pro['title']=str_replace('"', "'", $result['result']['problems'][$i]['name']);
                $this->pro['solved_count']=$result['result']['problemStatistics'][$i]['solvedCount'];
                $this->pro['pcode']="CF".$result['result']['problems'][$i]['contestId'].$result['result']['problems'][$i]['index'];
                $this->pro['index_id']=$result['result']['problems'][$i]['index'];
                $this->pro['contest_id']=$result['result']['problems'][$i]['contestId'];
                $this->pro['OJ']=2;

                $now=time()-$start;
                fwrite($f, "{$this->pro['pcode']} start at {$now}".PHP_EOL);

                Extract_CodeForces($this->pro['contest_id'], $this->pro['index_id'], $this->pro['origin']);

                $pid=$problemModel->pid($this->pro['pcode']);

                if ($pid) {
                    $problemModel->clearTags($pid);
                    $new_pid=$this->update_problem();
                } else {
                    $new_pid=$this->insert_problem();
                }

                for ($j=0;$j<count($result['result']['problems'][$i]['tags']);$j++) {
                    $problemModel->addTags($new_pid, $result['result']['problems'][$i]['tags'][$j]);
                }

                // Why not foreach ?????? I don't know...

                $now=time()-$start;
                fwrite($f, "{$this->pro['pcode']} end at {$now}".PHP_EOL);
            }
            $now=time()-$start;
            fwrite($f, "Updata All Problems end at {$now}".PHP_EOL);
            fclose($f);
        }
    }
}
