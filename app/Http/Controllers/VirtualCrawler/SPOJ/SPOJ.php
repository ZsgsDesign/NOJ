<?php

namespace App\Http\Controllers\VirtualCrawler\SPOJ;

use App\Http\Controllers\VirtualCrawler\CrawlerBase;
use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth;
use Requests;
use Exception;

class SPOJ extends CrawlerBase 
{
    public $oid=10;
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
        } else if($con=="all"){
            $this->getSpojProblem();
        }else{
            //TODO
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
            $url='https://www.spoj.com'.$ele->src;
            $res=Requests::get($url, ['Referer' => 'https://www.spoj.com']);
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
            $fn=$this->pro['index_id'].'_'.($this->imgi++).$cext;
            $dir=base_path("public/external/spoj/img");
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents(base_path("public/external/spoj/img/$fn"), $res->body);
            $ele->src='/external/spoj/img/'.$fn;
        }
        return $dom;
    }

    public function getProblemDetail($pid) {
        $cate = [];

        $url = "https://www.spoj.com/problems/".$pid;
        $res = Requests::get($url);
        if (strpos("<h1>Not Found</h1>",$res->body) !== false) {
            header('HTTP/1.1 404 Not Found');
            die();
        }else{
            $this->pro['title'] = trim(self::find('/<h2 id="problem-name".* - (.*)<\/h2>/sU',$res->body));
            $temp = $res->body;
			$temp = explode('<tr><td>Time limit:</td><td>', $temp)[1];
			$this->pro['time_limit']=trim(explode('s			</td></tr>', $temp)[0]); //as for html arrange
            $temp = $res->body;
            $temp = explode('<tr><td>Memory limit:</td><td>', $temp)[1];
			$this->pro['memory_limit']=trim(explode('MB</td></tr>' , $temp)[0]);
            $temp = $res->body;
			$temp = explode('<tr><td>Languages:</td><td>', $temp)[1];
			$this->pro['note'] = "Languages Limit:".explode('</td></tr>', $temp)[0];

            if(strpos($res->body, "<tr><td>Resource:</td><td>")!==false)
			{
				$temp = $res->body;
				$temp = explode('<tr><td>Resource:</td><td>', $temp)[1];
				$this->pro['source'] = strip_tags(explode('</td></tr>', $temp)[0]);
			}else {
                $this->pro['source'] = "SPOJ-".$pid;
            }

            if(strpos($res->body,"No tags")===false)
			{
				$temp = $res->body;
				$temp = explode('<div id="problem-tags" class="col-lg-12 text-center">', $temp)[1];
				$temp = explode('</div>', $temp)[0];
				$cat = explode('<a href="/problems/tag/', $temp);
				for($i = 1;$i < count($cat); $i++)
				{
					$temp = $cat[$i];
					$temp = explode('">', $temp)[0];
					array_push($cate,$temp);
				}
			}

            $temp = $res->body;
			$temp = explode('<div id="problem-body">', $temp)[1];
			$content = explode('<div class="text-center">', $temp)[0];

            $this->pro['description'] = $this->cacheImage(HtmlDomParser::str_get_html(explode('<h3>Input</h3>', $content)[0], true, true, DEFAULT_TARGET_CHARSET, false));
            $content = explode('<h3>Input</h3>', $content)[1];
            $this->pro['input'] = explode('<h3>Output</h3>', $content)[0];
            $content = explode('<h3>Output</h3>', $content)[1];
            $tgis->pro['output'] = explode('<h3>Example</h3>', $content)[0];
            $content = explode('<h3>Example</h3>', $content)[1];

            //TODO: Get compiler of each problem. 
        }
    }

    public function getSpojProblem() {
        $types = ['classical','challenge','partial','tutorial','riddle','basics'];

        foreach($types as $type) {
            $iterator = 0;
            $endPos = false;
            while($endPos == false) {
                $url = "https://www.spoj.com/problems/".$type."/sort=0,start=".$iterator*50;
                $res = Requests::get($url);
                $problemTable = explode('<td align="left">', $res->body);
                for($cnt = 1; $cnt < count($problemTable); $cnt++) {
                    $problemLink = $problemTable[$cnt];
                    $problemLink = explode('<a href="/problems/', $problemLink)[1];
					$pid=explode('">', $problemLink)[0];

                    $problemLink = $problemTable[$cnt];
                    $problemLink = explode('See the best solutions.">', $problemLink)[1];
					$solved_count=explode('</a></td>', $problemLink)[0];

                    $problemLink = $problemTable[$cnt];
                    $index = self::find('/<td align="center">([\s]*?)<\/td>/',$problemLink);

                    $this->pro['pcode'] = "SPOJ-".$pid;
                    $this->pro['OJ'] = 10;
                    $this->pro['contest_id'] = null;
                    $this->pro['index_id'] = $index;
                    $this->pro['origin'] = "https://www.spoj.com/problems/".$pid;
                    $this->pro['input_type']='standard input';
                    $this->pro['output_type']='standard output';
                    $this->getProblemDetail($pid);
                    
                    $problem=$problemModel->pid($this->pro['pcode']);

                    if ($problem) {
                        $problemModel->clearTags($problem);
                        $new_pid=$this->update_problem($this->oid);
                    }else {
                        $new_pid=$this->insert_problem($this->oid);
                    }
                }
                if(count($table) < 50) $endPos = true;
                $iterator++;
            }
        }
    }
}