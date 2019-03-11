<?php

namespace App\Http\Controllers\VirtualCrawler\CodeForces;

use App\Http\Controllers\VirtualCrawler\CrawlerBase;
use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth;

class CodeForces extends CrawlerBase
{
    public $oid=2;
    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($action = 'crawl_problem', $con = 'all', $cached = false)
    {
        set_time_limit(0); // Pandora's box, engage!
        if ($action=='judge_level') {
            $this->judge_level();
        } else {
            $this->Codeforces($con, $cached);
        }
    }

    public function judge_level()
    {
        $problemModel=new ProblemModel();
        $arr=$problemModel->getSolvedCount($this->oid);
        usort($arr, ["CrawlerBase","cmp"]);
        $m = count($arr)/10;
        for ($i=1; $i<=count($arr); $i++) {
            $level =ceil($i/$m);
            $problemModel->updateDifficulty($arr[$i-1][0], $level);
        }
    }

    public function extractCodeForces($cid, $num, $url, $default_desc = "")
    {
        $pid=$cid.$num;
        $content=$this->get_url($url);
        $content_type=get_headers($url, 1)["Content-Type"];
        if (stripos($content, "<title>Codeforces</title>")===false) {
            if (stripos($content, "<title>Attachments")!==false) {
                $this->pro["description"].=$default_desc;
            } else {
                if (stripos($content_type, "text/html")!==false) {
                    $this->pro["file"]=0;
                    $first_step = explode('<div class="input-file"><div class="property-title">input</div>', $content);
                    $second_step = explode("</div>", $first_step[1]);
                    $this->pro["input_type"] = $second_step[0];
                    $first_step = explode('<div class="output-file"><div class="property-title">output</div>', $content);
                    $second_step = explode("</div>", $first_step[1]);
                    $this->pro["output_type"]= $second_step[0];

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

                    if (strpos($content, '<div class="sample-test">') !== false) {
                        $temp_sample=explode('<div class="sample-test">', $content)[1];
                        if (!(strpos($content, '<div class="note">') !== false)) {
                            $temp_sample=explode('<script type="text/javascript">', $temp_sample)[0];
                        } else {
                            $temp_sample=explode('<div class="note">', $temp_sample)[0];
                        }

                        $sample_list=HtmlDomParser::str_get_html($temp_sample);
                        $sample_pairs=intval(count($sample_list->find('pre'))/2);
                        $this->pro["sample"]=[];
                        for ($i=0; $i<$sample_pairs; $i++) {
                            $sample_input=$sample_list->find('pre')[$i*2]->innertext;
                            $sample_output=$sample_list->find('pre')[$i*2+1]->innertext;
                            array_push($this->pro["sample"], [
                                "sample_input"=>$sample_input,
                                "sample_output"=>$sample_output
                            ]);
                        }
                    }

                    if (preg_match("/Note<\\/div>(.*)<\\/div><\\/div>/sU", $content, $matches)) {
                        $this->pro["note"]=trim(($matches[1]));
                    }
                    if (preg_match("/<th class=\"left\" style=\"width:100%;\">(.*)<\\/th>/sU", $content, $matches)) {
                        $this->pro["source"]=trim(strip_tags($matches[1]));
                    }

                    $this->pro["description"]=str_replace("src=\"", "src=\"http://codeforces.com/", $this->pro["description"]);
                    $this->pro["input"]=str_replace("src=\"", "src=\"http://codeforces.com/", $this->pro["input"]);
                    $this->pro["output"]=str_replace("src=\"", "src=\"http://codeforces.com/", $this->pro["output"]);
                    $this->pro["note"]=str_replace("src=\"", "src=\"http://codeforces.com/", $this->pro["note"]);
                } else {
                    if (stripos($content_type, "application/pdf")!==false) {
                        $ext="pdf";
                    } elseif (stripos($content_type, "application/msword")!==false) {
                        $ext="doc";
                    } elseif (stripos($content_type, "application/vnd.openxmlformats-officedocument.wordprocessingml.document")!==false) {
                        $ext="docx";
                    }
                    $dir = base_path("public/external/gym");
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    file_put_contents(base_path("public/external/gym/$cid$num.$ext"), $content);
                    $this->pro["description"].="<a href=\"/external/gym/$cid$num.$ext\">[Attachment Link]</a>";
                    $this->pro["time_limit"]=0;
                    $this->pro["memory_limit"]=0;
                    $this->pro["source"]="Here";
                    $this->pro["file"]=1;
                    $this->pro["sample"]=[];
                }
            }
        } else {
            return false;
        }
    }


    public function CodeForces($con, $cached)
    {
        $problemModel=new ProblemModel();
        $start=time();
        if ($cached) {
            $response=file_get_contents(__DIR__."/problemset.problems");
        } else {
            $ch=curl_init();
            $url="http://codeforces.com/api/problemset.problems";
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response=curl_exec($ch);
            curl_close($ch);
            // cache to folder
            $fp = fopen(__DIR__."/problemset.problems", "w");
            fwrite($fp, $response);
            fclose($fp);
        }
        $result=json_decode($response, true);
        if ($result["status"]=="OK") {
            $now=time()-$start;
            $f = fopen(__DIR__."/codeforces_status.log", "w") or die("Unable to open file!");
            fwrite($f, "CodeForces API Success at {$now}".PHP_EOL);
            for ($i=count($result['result']['problems'])-1; $i>=0; $i--) {
                foreach ($this->pro as $x => $y) {
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
                $this->pro['OJ']=$this->oid;

                $now=time()-$start;
                fwrite($f, "{$this->pro['pcode']} start at {$now}".PHP_EOL);

                $this->extractCodeForces($this->pro['contest_id'], $this->pro['index_id'], $this->pro['origin']);

                $pid=$problemModel->pid($this->pro['pcode']);

                if ($pid) {
                    $problemModel->clearTags($pid);
                    $new_pid=$this->update_problem($this->oid);
                } else {
                    $new_pid=$this->insert_problem($this->oid);
                }

                for ($j=0; $j<count($result['result']['problems'][$i]['tags']); $j++) {
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
