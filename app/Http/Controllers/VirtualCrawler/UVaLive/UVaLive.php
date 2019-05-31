<?php

namespace App\Http\Controllers\VirtualCrawler\UVaLive;

use App\Http\Controllers\VirtualCrawler\CrawlerBase;
use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth;
use Requests;
use Exception;

class UVaLive extends CrawlerBase
{
    public $oid=9;
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
            $this->crawler($con);
        }
    }

    public function judge_level()
    {
        // TODO
    }


    public function crawler($con)
    {
        $problemModel=new ProblemModel();
        if ($con=='all') {
            $res=Requests::get("https://icpcarchive.ecs.baylor.edu/uhunt/api/p", [], ['timeout'=>600]);
            $result=json_decode($res->body, true);
            $info=[];
            for ($i=0; $i<count($result); ++$i) {
                $info[$result[$i][1]]=[$result[$i][0], $result[$i][2], $result[$i][3], $result[$i][19]];
            }
            ksort($info);
            foreach ($info as $key=>$value) {
                $this->pro['pcode']='UvaL'.$key;
                $this->pro['OJ']=$this->oid;
                $this->pro['contest_id']=null;
                $this->pro['index_id']=$value[0];
                $this->pro['origin']="https://icpcarchive.ecs.baylor.edu/index.php?option=com_onlinejudge&Itemid=8&page=show_problem&problem=".$value[0];
                $this->pro['title']=$value[1];
                $this->pro['time_limit']=$value[3];
                $this->pro['memory_limit']=131072; // Given in elder codes
                $this->pro['solved_count']=$value[2];
                $this->pro['input_type']='standard input';
                $this->pro['output_type']='standard output';
                $this->pro['description']="<a href=\"/external/gym/UvaL{$key}.pdf\">[Attachment Link]</a>";
                $this->pro['input']='';
                $this->pro['output']='';
                $this->pro['note']='';
                $this->pro['sample']=[];
                $this->pro['source']='Here';
                $this->pro['file']=1;

                $problem=$problemModel->pid($this->pro['pcode']);

                if ($problem) {
                    $problemModel->clearTags($problem);
                    $new_pid=$this->update_problem($this->oid);
                } else {
                    $new_pid=$this->insert_problem($this->oid);
                }

                // $problemModel->addTags($new_pid, $tag); // not present
            }
            $this->data=array_keys($info);
        } else {
            $pf=substr($con, 0, strlen($con)-2);
            $res=Requests::get("https://icpcarchive.ecs.baylor.edu/external/$pf/$con.pdf");
            file_put_contents(base_path("public/external/gym/UvaL$con.pdf"), $res->body);
        }
    }
}
