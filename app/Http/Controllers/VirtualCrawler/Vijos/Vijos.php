<?php

namespace App\Http\Controllers\VirtualCrawler\Vijos;

use App\Http\Controllers\VirtualCrawler\CrawlerBase;
use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth,Requests,Exception;

class Vijos extends CrawlerBase
{
    public $oid=5;
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

    public function crawling($con)
    {
        if ($con == 'all') {
            // TODO
            return;
        }

        try {
            $dom = HtmlDomParser::file_get_html('https://vijos.org/p/'.$con, false, null, 0, -1, true, true, DEFAULT_TARGET_CHARSET, false);
        }
        catch (Exception $e) {
            if (strpos($e->getMessage(), '404 Not Found') !== false) {
                header('HTTP/1.1 404 Not Found');
                die();
            }
            if (strpos($e->getMessage(), '403 Forbidden') !== false) {
                header('HTTP/1.1 403 Forbidden');
                die();
            }
            throw $e;
        }

        $mainDiv = $dom->find(".section__body", 0);

        $eles = $mainDiv->children();
        array_push($eles, null);
        $this->pro['description'] = null;
        $this->pro['input'] = null;
        $this->pro['output'] = null;
        $this->pro['sample'] = [];
        $this->pro['note'] = null;
        $this->pro['sampleDesc'] = null;
        $this->pro['limit'] = null;
        $patterns = [
            'description' => '<h1>描述</h1>',
            '_format' => '<h1>格式</h1>',
            'input' => '<h2>输入格式</h2>',
            'output' => '<h2>输出格式</h2>',
            '_sample' => '/^<h1>样例\d+<\/h1>$/u',
            '__sampleInput' => '/^<h2>样例输入\d+<\/h2>$/u',
            '__sampleOutput' => '/^<h2>样例输出\d+<\/h2>$/u',
            'limit' => '<h1>限制</h1>',
            'note' => '<h1>提示</h1>',
            'sampleDesc' => '/<h1>样例(说明|解释)<\/h1>|<h2>样例说明1<\/h2>/', // P2036 has <h2>样例说明1</h2>
            'source' => '<h1>来源</h1>',
        ];
        $lastPart = '';
        $content = '';
        $cursample = [];
        foreach ($eles as $ele) {
            $html = $ele ? $ele->outertext : null;
            $match = !$ele;
            if (!$match) {
                foreach ($patterns as $key=>$value) {
                    if ($value[0] != '/' && $html == $value || $value[0] == '/' && preg_match($value, $html)) {
                        $match = $key;
                        break;
                    }
                }
            }
            if (!$lastPart) {
                if ($match) $lastPart = $match;
                continue;
            }
            if ($match) {
                if ($lastPart[0] != '_') {
                    $this->pro[$lastPart] = $content;
                    $content = '';
                } else if ($lastPart == '__sampleOutput') { // Assume output always follows by input
                    array_push($this->pro['sample'], $cursample);
                    $cursample = [];
                }
                $lastPart = $match;
            } else {
                if ($lastPart[1] != '_') {
                    if ($lastPart != 'source') $content .= $html;
                    else $content .= $ele->innertext;
                } else { // Code
                    $code = trim($ele->find('code', 0)->innertext);
                    if ($lastPart == '__sampleInput') { if (isset($cursample['sampleInput'])) die($con); }
                    else { if (isset($cursample['sampleOutput'])) die($con); }
                    if (count($ele->children()) != 1) die($con);
                    if ($lastPart == '__sampleInput') $cursample['sample_input'] = $code;
                    else $cursample['sample_output'] = $code;
                }
            }
            if (!$ele) break;
        }

        $this->pro['time_limit'] = 1000;
        $this->pro['memory_limit'] = 262144;
        if ($this->pro['sampleDesc']) {
            $this->pro['note'] = '<h3>样例说明</h3>'.$this->pro['sampleDesc'].$this->pro['note'];
        }
        if ($this->pro['limit']) {
            $this->pro['note'] = $this->pro['limit'].$this->pro['note'];
            $this->pro['time_limit'] = 0;
            $this->pro['memory_limit'] = 0;
        }

        $this->pro['pcode'] = 'VIJ'.$con;
        $this->pro['OJ'] = $this->oid;
        $this->pro['contest_id'] = null;
        $this->pro['index_id'] = $con;
        $this->pro['origin'] = 'https://vijos.org/p/'.$con;
        $this->pro['title'] = $dom->find('.section__header', 0)->find('h1', 0)->innertext;
        $this->pro['input_type'] = 'standard input';
        $this->pro['output_type'] = 'standard output';

        $this->pro['markdown'] = 0;
        $this->pro['tot_score'] = 100;
        $this->pro["partial"] = 1;
        $this->pro['source'] = 'Vijos'; // Force Override

        $info = $dom->find(".horizontal", 0);
        preg_match('/<dt>已通过<\/dt>[\s\S]*<dd>(\d+)<\/dd>/', $info->innertext, $match);
        $this->pro['solved_count'] = $match[1];

        $problemModel=new ProblemModel();
        $problem=$problemModel->pid($this->pro['pcode']);

        if ($problem) {
            $problemModel->clearTags($problem);
            $new_pid=$this->update_problem($this->oid);
        } else {
            $new_pid=$this->insert_problem($this->oid);
        }

        $tags = $info->find('.hasjs--hide', 0);
        if ($tags) {
            foreach ($tags->find('a') as $tag) {
                $problemModel->addTags($new_pid, $tag->innertext);
            }
        }
    }
}
