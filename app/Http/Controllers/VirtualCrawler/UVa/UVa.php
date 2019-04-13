<?php

namespace App\Http\Controllers\VirtualCrawler\UVa;

use App\Http\Controllers\VirtualCrawler\CrawlerBase;
use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth;
use Requests;
use Exception;

class UVa extends CrawlerBase
{
    public $oid=7;
    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($action='crawl_problem', $con='all', $cached=false)
    {
        set_time_limit(0); // Pandora's box, engage!
        if ($action=='judge_level') {
            $this->judge_level();
        } else {
            if ($con == 'all') {
                $this->crawler(1, true);
                $this->crawler(2, true);
            } else if ($con[0] == 'c') {
                $this->crawler(substr($con, 1), true);
            } else {
                $this->crawler($con, false);
            }
        }
    }

    public function judge_level()
    {
        // TODO
    }


    public function crawler($con, $isCategory, $solves = -1)
    {
        $problemModel=new ProblemModel();
        if ($isCategory) {
            $res=Requests::get("https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=8&category=$con");
            $count = preg_match_all('/<td><a href="index\.php\?option=com_onlinejudge&amp;Itemid=8&amp;category=(\d+)(?:&amp;page=show_problem&amp;problem=(\d+)"[\S\s]*?<td align="right">[\S\s]*?<td align="right">(\d+)[\S\s]*?>([\d.]+)%<\/div>)?/i', $res->body, $matches);
            for ($i = 0; $i < $count; ++$i) {
                if ($matches[2][$i]) {
                    $this->crawler($matches[2][$i], false, intval($matches[3][$i] * $matches[4][$i] / 100 + .5)); // solve count here is not accurate
                } else {
                    $this->crawler($matches[1][$i], true);
                }
            }
        } else {
            $res=Requests::get("https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=8&page=show_problem&problem=$con");
            preg_match('/<h3>(\d+) - (.*?)<\/h3>\s*Time limit: ([\d.]+) seconds/', $res->body, $match);
            $this->pro['pcode']='UVA'.$match[1];
            $this->pro['OJ']=$this->oid;
            $this->pro['contest_id']=null;
            $this->pro['index_id']=$con;
            $this->pro['origin']="https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=8&page=show_problem&problem=$con";
            $this->pro['title']=$match[2];
            $this->pro['time_limit']=intval($match[3] * 1000);
            $this->pro['memory_limit']=0;
            $this->pro['solved_count']=$solves; // Won't crawler specially for efficiency purpose
            $this->pro['input_type']='standard input';
            $this->pro['output_type']='standard output';
            $this->pro['description']="<a href=\"/external/gym/UVa$match[1].pdf\">[Attachment Link]</a>";
            $this->pro['input']='';
            $this->pro['output']='';
            $this->pro['note']='';
            $this->pro['sample']=[];
            $this->pro['source']='Here';
            $this->pro['file']=1;
            $pf = substr($match[1], 0, strlen($match[1]) - 2);
            $res=Requests::get("https://uva.onlinejudge.org/external/$pf/p$match[1].pdf");
            file_put_contents(base_path("public/external/gym/UVa$match[1].pdf"), $res->body);

            $problem=$problemModel->pid($this->pro['pcode']);

            if ($problem) {
                $problemModel->clearTags($problem);
                $new_pid=$this->update_problem($this->oid);
            } else {
                $new_pid=$this->insert_problem($this->oid);
            }

            // $problemModel->addTags($new_pid, $tag); // not present
        }
    }
}
