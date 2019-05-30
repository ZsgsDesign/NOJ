<?php

namespace App\Http\Controllers\VirtualCrawler\HDU;

use App\Http\Controllers\VirtualCrawler\CrawlerBase;
use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth;
use Requests;
use Exception;


class POJ extends CrawlerBase 
{
    public $oid=6;
    private $con;
    private $imgi;
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
            $this->ContestHunter($con);
        }
    }

    public function judge_level()
    {
        // TODO
    }

    private static function find($pattern, $subject)
    {
        if (preg_match($pattern, $subject, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function cacheImage($dom)
    {
        foreach ($dom->find('img') as $ele) {
            $src=str_replace('../../..', '', $ele->src);
            if (strpos($src, '://')!==false) {
                $url=$src;
            } elseif ($src[0]=='/') {
                $url='http://acm.hdu.edu.cn'.$src;
            } else {
                $url='http://acm.hdu.edu.cn/'.$src;
            }
            $res=Requests::get($src, ['Referer' => 'http://acm.hdu.edu.cn']);
            $ext=['image/jpeg'=>'.jpg', 'image/png'=>'.png', 'image/gif'=>'.gif', 'image/bmp'=>'.bmp'];
            if (isset($res->headers['content-type'])) {
                $cext=$ext[$res->headers['content-type']];
            } else {
                $pos=strpos($ele->src, '.');
                if ($pos===false) {
                    $cext='';
                } else {
                    $cext=substr($ele->src, $pos);
                }
            }
            $fn=$this->con.'_'.($this->imgi++).$cext;
            $dir=base_path("public/external/hdu/img");
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents(base_path("public/external/hdu/img/$fn"), $res->body);
            $ele->src='/external/hdu/img/'.$fn;
        }
        return $dom;
    }

    public function crawler($con) 
    {
        if($con == "all") {
            return ;
        }
        $this->con = $con;
        $this->imgi = 1;
        $problemModel = new ProblemModel();
        $res = Request::get("http://acm.hdu.edu.cn/showproblem.php?pid={$con}");
        if (strpos("No such problem",$res->body) !== false) {
            header('HTTP/1.1 404 Not Found');
            die();
        } 
        else if(strpos("Invalid Parameter.",$res->body) !== false) {
            header('HTTP/1.1 404 Not Found');
            die();
        }
        else {
            $this->pro['pcode'] = "HDU".$con;
            $this->pro['OJ'] = 1;
            $this->pro['contest_id'] = null;
            $this->pro['index_id'] = $con;
            $this->pro['origin'] = "http://acm.hdu.edu.cn/showproblem.php?pid={$con}";
            $this->pro['title'] = find("/<h1 style='color:#1A5CC8'>([\s\S]*?)<\/h1>/",$res->body);
            $this->pro['time_limit'] = find('/Time Limit:.*\/(.*) MS/',$res->body);
            $this->pro['memory_limit'] = find('/Memory Limit:.*\/(.*) K/',$res->body);
            $this->pro['solved_count'] = find("/Accepted Submission(s): ([\d+]*?)/",$res->body);
            $this->pro['input_type']='standard input';
            $this->pro['output_type']='standard output';
            $this->pro['description'] = cacheImage(find("/this->problem Description.*<div class=panel_content>(.*)<\/div><div class=panel_bottom>/sU",$res->body));
            $this->pro['input'] = find("/<div class=panel_title align=left>Input.*<div class=panel_content>(.*)<\/div><div class=panel_bottom>/sU",$res->body);
            $this->pro['output'] = find("/<div class=panel_title align=left>Output.*<div class=panel_content>(.*)<\/div><div class=panel_bottom>/sU",$res->body);
            $this->pro['sample'] = [];
            $this->pro['sample']['sample_input'] = find("/<pre><div.*>(.*)<\/div><\/pre>/sU",$res->body);
            $this->pro['sample']['sample_output'] = find("/<div.*>Sample Output<\/div><div.*><pre><div.*>(.*)<\/div><\/pre><\/div>/sU",$res->body);
            $this->pro['note'] = find("/<i>Hint<\/i><\/div>(.*)<\/div><i style='font-size:1px'>/sU",$res->body);
            $this->pro['source'] = find("/<div class=panel_title align=left>Source<\/div> (.*)<div class=panel_bottom>/sU",$res->body);
            $this->pro['force_raw'] = 0;
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