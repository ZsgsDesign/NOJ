<?php

namespace App\Babel;

use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth;

class CrawlerBase
{
    public $pro=[
        'file'=> 0,
        'pcode'=>'',
        'solved_count'=>'',
        'time_limit'=>'',
        'memory_limit'=>'',
        'title'=>'',
        'OJ'=>'',
        'description'=>'',
        'input'=>'',
        'output'=>'',
        'note'=>'',
        'input_type'=>'',
        'output_type'=>'',
        'contest_id'=>'',
        'index_id'=>'',
        'origin'=>'',
        'source'=>'',
        'sample'=>[],
        'markdown'=>0,
        'tot_score'=>1,
        'partial'=>0,
        'special_compiler'=>null,
    ];

    public $data = null;

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct()
    {
    }

    public static function cmp($a, $b)
    {
        return ($a[1]>$b[1]) ?-1 : 1;
    }

    public function process_and_get_image($ori, $path, $baseurl, $space_deli, $cookie)
    {
        $para["path"]=$path;
        $para["base"]=$baseurl;
        $para["trans"]=!$space_deli;
        $para["cookie"]=$cookie;

        if ($space_deli) {
            $reg="/< *im[a]?g[^>]*src *= *[\"\\']?([^\"\\' >]*)[^>]*>/si";
        } else {
            $reg="/< *im[a]?g[^>]*src *= *[\"\\']?([^\"\\'>]*)[^>]*>/si";
        }

        return preg_replace_callback($reg, function($matches) use ($para) {
            global $config;
            $url=trim($matches[1]);
            if (stripos($url, "http://")===false && stripos($url, "https://")===false) {
                if ($para["trans"]) {
                    $url=str_replace(" ", "%20", $url);
                }
                $url=$para["base"].$url;
            }
            $name=basename($url);
            $name="images/".strtr($name, ":", "_");
            $result=str_replace(trim($matches[1]), "online_Judges/spoj/images/".strtr(basename($url), ":", "_"), $matches[0]);
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            if ($para["cookie"]!="") {
                curl_setopt($ch, CURLOPT_COOKIEFILE, $para["cookie"]);
            }
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content=curl_exec($ch);
            curl_close($ch);
            $fp=fopen($name, "wb");
            fwrite($fp, $content);
            fclose($fp);
            return $result;
        }, $ori);
    }

    public function pcrawler_process_info($path, $baseurl, $space_deli=true, $cookie="")
    {
        $this->pro["description"]=$this->process_and_get_image($this->pro["description"], $path, $baseurl, $space_deli, $cookie);
        $this->pro["input"]=$this->process_and_get_image($this->pro["input"], $path, $baseurl, $space_deli, $cookie);
        $this->pro["output"]=$this->process_and_get_image($this->pro["output"], $path, $baseurl, $space_deli, $cookie);
        $this->pro["note"]=$this->process_and_get_image($this->pro["note"], $path, $baseurl, $space_deli, $cookie);
    }

    public function get_url($url)
    {
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content=curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    public function insert_problem($oid=2)
    {
        $problemModel=new ProblemModel();
        return $problemModel->insertProblem($this->pro);
    }

    public function update_problem($oid=2)
    {
        $problemModel=new ProblemModel();
        return $problemModel->updateProblem($this->pro);
    }
}
