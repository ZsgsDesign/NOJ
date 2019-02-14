<?php

namespace App\Http\Controllers\VirtualCrawler;

use App\Models\ProblemModel;
use Auth;

class Crawler
{
    public $pro=[
        'title'=>'',
        'time_limit'=>'',
        'memory_limit'=>'',
        'OJ'=>'',
        'description'=>'',
        'input'=>'',
        'output'=>'',
        'notes'=>'',
        'input_type'=>'',
        'output_type'=>'',
        'pcode'=>'',
        'contest_id'=>'',
        'index_id'=>'',
        'origin'=>'',
        'source'=>'',
        'solved_count'=>'',
        'sample_input'=>'',
        'sample_output'=>''
    ];

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
        return ($a[1]>$b[1])?-1:1;
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

        return preg_replace_callback($reg, function ($matches) use ($para) {
            global $config;
            $url=trim($matches[1]);
            if (stripos($url, "http://")===false&&stripos($url, "https://")===false) {
                if ($para["trans"]) {
                    $url=str_replace(" ", "%20", $url);
                }
                $url=$para["base"].$url;
            }
            $name=basename($url);
            $name="images/".strtr($name, ":", "_");
            $result=str_replace(trim($matches[1]), "online_Judges/spoj/images/".strtr(basename($url), ":", "_"), $matches[0]);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            if ($para["cookie"]!="") {
                curl_setopt($ch, CURLOPT_COOKIEFILE, $para["cookie"]);
            }
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);
            $fp = fopen($name, "wb");
            fwrite($fp, $content);
            fclose($fp);
            return $result;

        }, $ori);
    }

    public function pcrawler_process_info($path, $baseurl, $space_deli=true, $cookie="")
    {
        $this->pro["description"]=$this->process_and_get_image($pro["description"], $path, $baseurl, $space_deli, $cookie);
        $this->pro["input"]=$this->process_and_get_image($pro["input"], $path, $baseurl, $space_deli, $cookie);
        $this->pro["output"]=$this->process_and_get_image($pro["output"], $path, $baseurl, $space_deli, $cookie);
        $this->pro["notes"]=$this->process_and_get_image($pro["notes"], $path, $baseurl, $space_deli, $cookie);
    }

    public function get_url($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    public function insert_problem($OJ="CodeForces")
    {
        $query="INSERT INTO problem";
        $query.="(
            difficulty,
            title,
            time_limit,
            memory_limit,
            OJ,
            description,
            input,
            output,
            notes,
            input_type,
            output_type,
            pcode,
            contest_id,
            index_id,
            origin,
            source,
            solved_count,
            sample_input,
            sample_output
        ) ";

        $query.="VALUES(
            -1,
            '{$this->pro['title']}',
            '{$this->pro['time_limit']}',
            '{$this->pro['memory_limit']}',
            '{$this->pro['OJ']}',
            '{$this->pro['description']}',
            '{$this->pro['input']}',
            '{$this->pro['output']}',
            '{$this->pro['notes']}',
            '{$this->pro['input_type']}',
            '{$this->pro['output_type']}',
            '{$this->pro['pcode']}',
            '{$this->pro['contest_id']}',
            '{$this->pro['index_id']}',
            '{$this->pro['origin']}',
            '{$this->pro['source']}',
            {$this->pro['solved_count']},
            '{$this->pro['sample_input']}',
            '{$this->pro['sample_output']}'
        )";
        if (!mysqli_query($db, $query)) {
            die("query failed "." ".mysqli_error($db));
        }
        $query="SELECT problem_id FROM problem where pcode='{$this->pro['pcode']}' AND OJ='{$OJ}'";
        $res=mysqli_query($db, $query);
        if (!$res) {
            die("query failed "." ".mysqli_error($db));
        }
        return mysqli_fetch_row($res)[0];
    }
    public function update_problem($OJ=2)
    {
        global $db,$pro;
        $query="UPDATE problem ";
        $query.="SET
                difficulty=-1,
                title='{$pro['title']}',
                time_limit='{$pro['time_limit']}',
                memory_limit='{$pro['memory_limit']}',
                description='{$pro['description']}',
                input='{$pro['input']}',
                output='{$pro['output']}',
                notes='{$pro['notes']}',
                input_type='{$pro['input_type']}',
                output_type='{$pro['output_type']}',
                contest_id='{$pro['contest_id']}',
                index_id='{$pro['index_id']}',
                origin='{$pro['origin']}',
                source='{$pro['source']}',
                solved_count= {$pro['solved_count']},
                sample_input='{$pro['sample_input']}',
                sample_output='{$pro['sample_output']}'
                WHERE OJ='{$pro['OJ']}' AND pcode='{$pro['pcode']}'";
        if (!mysqli_query($db, $query)) {
            die("query failed "." ".mysqli_error($db));
        }
        $query="SELECT problem_id FROM problem where pcode='{$pro['pcode']}' AND OJ='{$OJ}'";
        $res=mysqli_query($db, $query);
        if (!$res) {
            die("query failed "." ".mysqli_error($db));
        }
        return mysqli_fetch_row($res)[0];
    }
}
