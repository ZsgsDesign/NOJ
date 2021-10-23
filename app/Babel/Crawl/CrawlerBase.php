<?php

namespace App\Babel\Crawl;

use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth;
use Exception;

class CrawlerBase
{
    public $pro=[
        'file'=> 0,
        'file_url'=> null,
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

    public $data=null;
    public $command=null;

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct()
    {
    }

    protected function _resetPro()
    {
        $this->pro=[
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
            'order_index'=>null,
        ];
    }

    public function importCommandLine($commandTemp)
    {
        $this->command=$commandTemp;
    }

    public static function cmp($a, $b)
    {
        return ($a[1]>$b[1]) ?-1 : 1;
    }

    private function _cacheImage($data)
    {
        if (!isset($data["ori"]) || !isset($data["path"]) || !isset($data["baseurl"]) || !isset($data["space_deli"]) || !isset($data["cookie"])) {
            throw new Exception("data is not completely exist in cacheImage");
        }
        $ori=$data["ori"];
        $path=$data["path"];
        $baseurl=$data["baseurl"];
        $space_deli=$data["space_deli"];
        $cookie=$data["cookie"];

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

    public function procInfo($data)
    {
        if (isset($data["path"])) {
            $path=$data["path"];
        } else {
            throw new Exception("path is not exist in data");
        }
        if (isset($data["baseurl"])) {
            $baseurl=$data["baseurl"];
        } else {
            throw new Exception("baseurl is not exist in data");
        }
        if (isset($data["space_deli"])) {
            $space_deli=$data["space_deli"];
        } else {
            $space_deli=true;
        }
        if (isset($data["cookie"])) {
            $cookie=$data["cookie"];
        } else {
            $cookie="";
        }

        $this->pro["description"]=$this->_cacheImage($this->pro["description"], $path, $baseurl, $space_deli, $cookie);
        $this->pro["input"]=$this->_cacheImage($this->pro["input"], $path, $baseurl, $space_deli, $cookie);
        $this->pro["output"]=$this->_cacheImage($this->pro["output"], $path, $baseurl, $space_deli, $cookie);
        $this->pro["note"]=$this->_cacheImage($this->pro["note"], $path, $baseurl, $space_deli, $cookie);
    }

    public function getUrl($url)
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

    public function insertProblem($oid=2)
    {
        $problemModel=new ProblemModel();
        return $problemModel->insertProblem($this->pro);
    }

    public function updateProblem($oid=2)
    {
        $problemModel=new ProblemModel();
        return $problemModel->updateProblem($this->pro);
    }

    protected function line($line)
    {
        if (is_null($this->command)) {
            echo $line;
        } else {
            $this->command->line($line);
        }
    }
}
