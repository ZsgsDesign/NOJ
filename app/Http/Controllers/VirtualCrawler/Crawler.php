<?php

namespace App\Http\Controllers\VirtualCrawler;

use Auth;

class Crawler
{
    public $pro=[
        'name'=>'',
        'time_limit'=>'',
        'memory_limit'=>'',
        'from_oj'=>'',
        'description'=>'',
        'input'=>'',
        'output'=>'',
        'notes'=>'',
        'input_type'=>'',
        'output_type'=>'',
        'id'=>'',
        'contest_id'=>'',
        'ind'=>'',
        'url'=>'',
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
        set_time_limit(0);
    }

    public function cmp($a, $b)
    {
        return ($a[1]>$b[1])?-1:1;
    }

    public function update_level_codeforces()
    {
        global $db;
        $query="SELECT problem_id,solved_count FROM problem WHERE from_oj='CodeForces'";
        $result=mysqli_query($db, $query);
        if (!$result) {
            die("query failed "." ".mysqli_error($db));
        }
        $arr=array();
        while ($row=mysqli_fetch_row($result)) {
            array_push($arr, array($row[0],$row[1]));
        }
        usort($arr, "cmp");
        $m=count($arr)/10;
        for ($i=1;$i<=count($arr);$i++) {
            //echo $arr[$i-1][1]."  ".ceil($i/$m)."<br>";
            $level =ceil($i/$m);
            $query="UPDATE problem SET difficulty={$level} WHERE problem_id={$arr[$i-1][0]}";
            $result=mysqli_query($db, $query);
            if (!$result) {
                die("query failed "." ".mysqli_error($db));
            }
        }
    }

    public function update_level_uva()
    {
        global $db;
        $query="SELECT problem_id,solved_count FROM problem WHERE from_oj='Uva'";
        $result=mysqli_query($db, $query);
        if (!$result) {
            die("query failed "." ".mysqli_error($db));
        }
        $arr=array();
        while ($row=mysqli_fetch_row($result)) {
            array_push($arr, array($row[0],$row[1]));
        }
        usort($arr, "cmp");
        $m=count($arr)/10;
        for ($i=1;$i<=count($arr);$i++) {
            //echo $arr[$i-1][1]."  ".ceil($i/$m)."<br>";
            $level =ceil($i/$m);
            $query="UPDATE problem SET difficulty={$level} WHERE problem_id={$arr[$i-1][0]}";
            $result=mysqli_query($db, $query);
            if (!$result) {
                die("query failed "." ".mysqli_error($db));
            }
        }
    }

    public function update_level_spoj()
    {
        global $db;
        $query="SELECT problem_id,solved_count FROM problem WHERE from_oj='Spoj'";
        $result=mysqli_query($db, $query);
        if (!$result) {
            die("query failed "." ".mysqli_error($db));
        }
        $arr=array();
        while ($row=mysqli_fetch_row($result)) {
            array_push($arr, array($row[0],$row[1]));
        }
        usort($arr, "cmp");
        $m=count($arr)/10;
        for ($i=1;$i<=count($arr);$i++) {
            //echo $arr[$i-1][1]."  ".ceil($i/$m)."<br>";
            $level =ceil($i/$m);
            $query="UPDATE problem SET difficulty={$level} WHERE problem_id={$arr[$i-1][0]}";
            $result=mysqli_query($db, $query);
            if (!$result) {
                die("query failed "." ".mysqli_error($db));
            }
        }
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
        global $pro;
        $pro["description"]=process_and_get_image($pro["description"], $path, $baseurl, $space_deli, $cookie);
        $pro["input"]=process_and_get_image($pro["input"], $path, $baseurl, $space_deli, $cookie);
        $pro["output"]=process_and_get_image($pro["output"], $path, $baseurl, $space_deli, $cookie);
        $pro["notes"]=process_and_get_image($pro["notes"], $path, $baseurl, $space_deli, $cookie);
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
        global $db,$pro;
        $query="INSERT INTO problem";
        $query.="(
            difficulty,
            name,
            time_limit,
            memory_limit,
            from_oj,
            description,
            input,
            output,
            notes,
            input_type,
            output_type,
            id,
            contest_id,
            ind,
            link,
            source,
            solved_count,
            sample_input,
            sample_output
        ) ";

        $query.="VALUES(
            -1,
            '{$pro['name']}',
            '{$pro['time_limit']}',
            '{$pro['memory_limit']}',
            '{$pro['from_oj']}',
            '{$pro['description']}',
            '{$pro['input']}',
            '{$pro['output']}',
            '{$pro['notes']}',
            '{$pro['input_type']}',
            '{$pro['output_type']}',
            '{$pro['id']}',
            '{$pro['contest_id']}',
            '{$pro['ind']}',
            '{$pro['url']}',
            '{$pro['source']}',
            {$pro['solved_count']},
            '{$pro['sample_input']}',
            '{$pro['sample_output']}'
        )";
        if (!mysqli_query($db, $query)) {
            die("query failed "." ".mysqli_error($db));
        }
        $query="SELECT problem_id FROM problem where id='{$pro['id']}' AND from_oj='{$OJ}'";
        $res=mysqli_query($db, $query);
        if (!$res) {
            die("query failed "." ".mysqli_error($db));
        }
        return mysqli_fetch_row($res)[0];
    }
    public function update_problem($OJ="CodeForces")
    {
        global $db,$pro;
        $query="UPDATE problem ";
        $query.="SET
                difficulty=-1,
                name='{$pro['name']}',
                time_limit='{$pro['time_limit']}',
                memory_limit='{$pro['memory_limit']}',
                description='{$pro['description']}',
                input='{$pro['input']}',
                output='{$pro['output']}',
                notes='{$pro['notes']}',
                input_type='{$pro['input_type']}',
                output_type='{$pro['output_type']}',
                contest_id='{$pro['contest_id']}',
                ind='{$pro['ind']}',
                link='{$pro['url']}',
                source='{$pro['source']}',
                solved_count= {$pro['solved_count']},
                sample_input='{$pro['sample_input']}',
                sample_output='{$pro['sample_output']}'
                WHERE from_oj='{$pro['from_oj']}' AND id='{$pro['id']}'";
        if (!mysqli_query($db, $query)) {
            die("query failed "." ".mysqli_error($db));
        }
        $query="SELECT problem_id FROM problem where id='{$pro['id']}' AND from_oj='{$OJ}'";
        $res=mysqli_query($db, $query);
        if (!$res) {
            die("query failed "." ".mysqli_error($db));
        }
        return mysqli_fetch_row($res)[0];
    }
}
