<?php

namespace App\Http\Controllers\VirtualCrawler\PTA;

use App\Http\Controllers\VirtualCrawler\CrawlerBase;
use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth,Requests,Exception;

class PTA extends CrawlerBase
{
    public $oid=6;
    private $con, $imgi;
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
            $this->crawling($con);
        }
    }

    public function judge_level()
    {
        // TODO
    }

    private static function find($pattern, $subject)
    {
        if (preg_match($pattern, $subject, $matches)) return $matches[1];
        return NULL;
    }

    private function getDOM($html, $start, $end)
    {
        if ($start === false || $end === false) throw new Exception("Missing keywords.");
        return $this->cacheImage(HtmlDomParser::str_get_html(substr($html, $start, $end - $start), true, true, DEFAULT_TARGET_CHARSET, false));
    }

    private function getInnertext($html, $start, $end, $tag)
    {
        return $this->getDOM($html, $start, $end)->find($tag, 0)->innertext();
    }

    private function cacheImage($dom)
    {
        foreach ($dom->find('img') as $ele) {
            if (strpos($ele->src, '://') !== false) {
                $url = $ele->src;
            } else if ($ele->src[0] == '/') {
                $url = 'http://poj.org'.$ele->src;
            } else {
                $url = 'http://poj.org/'.$ele->src;
            }
            $res = Requests::get($url, ['Referer' => 'http://poj.org']);
            $ext = ['image/jpeg'=>'.jpg', 'image/png'=>'.png', 'image/gif'=>'.gif', 'image/bmp'=>'.bmp'];
            if (isset($res->headers['content-type'])) $cext = $ext[$res->headers['content-type']];
            else {
                $pos = strpos($ele->src, '.');
                if ($pos === false) $cext = '';
                else  $cext = substr($ele->src, $pos);
            }
            $fn = $this->con.'_'.($this->imgi++).$cext;
            $dir = base_path("public/external/poj/img");
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents(base_path("public/external/poj/img/$fn"), $res->body);
            $ele->src = '/external/poj/img/'.$fn;
        }
        return $dom;
    }

    public function crawling($conType)
    {
        if ($conType == 'all') {
            // Here is the script
            //
            // var a="";
            // document.querySelectorAll('a[href^="/problem-sets/"]').forEach(v=>{a+=v.href.split("/")[4]+","})
            // console.log(a);

            $conList=[12,13,14,15,16,17,434,994805046380707840,994805148990160896,994805260223102976,994805342720868352];
        } else {
            $conList=[intval($conType)];
        }

        foreach ($conList as $con) {
            $this->con = $con;
            $this->imgi = 1;
            $problemModel=new ProblemModel();
            $res = Requests::post("https://pintia.cn/api/problem-sets/$con/exams",[
                "Content-Type"=>"application/json"
            ],[]);

            if (strpos($res->body, 'PROBLEM_SET_NOT_FOUND') !== false) {
                header('HTTP/1.1 404 Not Found');
                die();
            } else {
                $generalDetails=json_decode($res->body,true);
            }

            $probLists = json_decode(Requests::get(
                "https://pintia.cn/api/problem-sets/$con/problems?type=PROGRAMMING&exam_id=0",[
                    "Content-Type"=>"application/json"
                ]
            )->body, true)["problemSetProblems"];

            foreach ($probLists as $prob) {
                $probDetails = json_decode(Requests::get(
                    "https://pintia.cn/api/problem-sets/$con/problems/{$prob["id"]}?exam_id=0",[
                        "Content-Type"=>"application/json"
                    ]
                )->body, true)["problemSetProblem"];
                $this->pro['pcode'] = 'PAT'.$prob["id"];
                $this->pro['OJ'] = $this->oid;
                $this->pro['contest_id'] = $con;
                $this->pro['index_id'] = $prob["id"];
                $this->pro['origin'] = "https://pintia.cn/problem-sets/$con/problems/{$prob["id"]}";
                $this->pro['title'] = $prob["title"];
                $this->pro['time_limit'] = $probDetails["problemConfig"]["programmingProblemConfig"]["timeLimit"];
                $this->pro['memory_limit'] = $probDetails["problemConfig"]["programmingProblemConfig"]["memoryLimit"];
                $this->pro['solved_count'] = $prob["acceptCount"];
                $this->pro['input_type'] = 'standard input';
                $this->pro['output_type'] = 'standard output';

                $this->pro['description'] = $probDetails["content"];
                $this->pro['markdown'] = 1;
                $this->pro['input'] = null;
                $this->pro['output'] = null;
                $this->pro['note'] = null;
                $this->pro['sample'] = [];
                $this->pro['source'] = trim($this->getDOM($res->body, $pos1, $pos2)->find('div', 0)->find('a', 0)->innertext());

                $problem=$problemModel->pid($this->pro['pcode']);

                if ($problem) {
                    $problemModel->clearTags($problem);
                    $new_pid=$this->update_problem($this->oid);
                } else {
                    $new_pid=$this->insert_problem($this->oid);
                }
                // $problemModel->addTags($new_pid, $tag);
            }
        }
    }
}
