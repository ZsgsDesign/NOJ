<?php
namespace App\Babel\Submit;

use App\Models\Submission\SubmissionModel;
use Exception;

interface CurlInterface {
    function login($all_data);
    function grab_page($all_data);
    function post_data($all_data);
}
class Curl
{
    public function __construct()
    {
        //
    }

    public function login($all_data)
    {
        if (isset($all_data["url"]))    $url=$all_data["url"]; else throw new Exception("url is not exist in all_data");
        if (isset($all_data["data"]))   $data=$all_data["data"]; else throw new Exception("data is not exist in all_data");
        if (isset($all_data["oj"]))     $oj=$all_data["oj"]; else throw new Exception("oj is not exist in all_data");
        if (isset($all_data["ret"]))    $ret=$all_data["ret"]; else $ret='false';
        if (isset($all_data["handle"])) $handle=$all_data["handle"]; else $handle="default";

        $datapost=curl_init();
        $headers=array("Expect:");
        $handle=urlencode($handle);

        curl_setopt($datapost, CURLOPT_CAINFO, babel_path("Cookies/cacert.pem"));
        curl_setopt($datapost, CURLOPT_URL, $url);
        curl_setopt($datapost, CURLOPT_HEADER, true); //
        curl_setopt($datapost, CURLOPT_HTTPHEADER, $headers); //
        curl_setopt($datapost, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36");
        curl_setopt($datapost, CURLOPT_POST, true);

        curl_setopt($datapost, CURLOPT_RETURNTRANSFER, $ret);
        curl_setopt($datapost, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($datapost, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($datapost, CURLOPT_POSTFIELDS, $data);
        curl_setopt($datapost, CURLOPT_COOKIEFILE, babel_path("Cookies/{$oj}_{$handle}.cookie"));
        curl_setopt($datapost, CURLOPT_COOKIEJAR, babel_path("Cookies/{$oj}_{$handle}.cookie"));
        ob_start();
        $response=curl_exec($datapost);
        if (curl_errno($datapost)) {
            throw new Exception(curl_error($datapost), 10000);
        }
        ob_end_clean();
        curl_close($datapost);
        unset($datapost);
        return $response;
    }

    public function grab_page($all_data)
    {
        if (isset($all_data["site"]))    $site=$all_data["site"]; else throw new Exception("site is not exist in all_data");
        if (isset($all_data["oj"]))      $oj=$all_data["oj"]; else throw new Exception("oj is not exist in all_data");
        if (isset($all_data["headers"])) $headers=$all_data["headers"]; else $headers=[];
        if (isset($all_data["handle"]))  $handle=$all_data["handle"]; else $handle="default";
        if (isset($all_data["follow"]))  $follow=$all_data["follow"]; else $follow=false;
        if (isset($all_data["vcid"]))  $vcid=$all_data["vcid"]."_"; else $vcid="";

        $handle=urlencode($handle);

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_CAINFO, babel_path("Cookies/cacert.pem"));
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36");
        curl_setopt($ch, CURLOPT_COOKIEFILE, babel_path("Cookies/{$oj}_{$vcid}{$handle}.cookie"));
        curl_setopt($ch, CURLOPT_COOKIEJAR, babel_path("Cookies/{$oj}_{$vcid}{$handle}.cookie"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $site);
        ob_start();
        $response=curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), 10000);
        }
        ob_end_clean();
        curl_close($ch);
        return $response;
    }

    public function post_data($all_data)
    {
        if (isset($all_data["site"]))         $site=$all_data["site"]; else throw new Exception("site is not exist in all_data");
        if (isset($all_data["data"]))         $data=$all_data["data"]; else throw new Exception("data is not exist in all_data");
        if (isset($all_data["oj"]))           $oj=$all_data["oj"]; else throw new Exception("oj is not exist in all_data");
        if (isset($all_data["ret"]))          $ret=$all_data["ret"]; else $ret=false;
        if (isset($all_data["follow"]))       $follow=$all_data["follow"]; else $follow=true;
        if (isset($all_data["returnHeader"])) $returnHeader=$all_data["returnHeader"]; else $returnHeader=true;
        if (isset($all_data["postJson"]))     $postJson=$all_data["postJson"]; else $postJson=false;
        if (isset($all_data["extraHeaders"])) $extraHeaders=$all_data["extraHeaders"]; else $extraHeaders=[];
        if (isset($all_data["handle"]))       $handle=$all_data["handle"]; else $handle="default";
        if (isset($all_data["vcid"]))  $vcid=$all_data["vcid"]."_"; else $vcid="";

        $handle=urlencode($handle);

        $datapost=curl_init();
        $headers=array("Expect:");
        if ($postJson) {
            $data=$data ? json_encode($data) : '{}';
            array_push($headers, 'Content-Type: application/json', 'Content-Length: '.strlen($data));
        }
        curl_setopt($datapost, CURLOPT_CAINFO, babel_path("Cookies/cacert.pem"));
        curl_setopt($datapost, CURLOPT_URL, $site);
        curl_setopt($datapost, CURLOPT_HEADER, $returnHeader);
        curl_setopt($datapost, CURLOPT_HTTPHEADER, array_merge($headers, $extraHeaders));
        curl_setopt($datapost, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36");
        curl_setopt($datapost, CURLOPT_POST, true);

        curl_setopt($datapost, CURLOPT_RETURNTRANSFER, $ret);
        curl_setopt($datapost, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($datapost, CURLOPT_FOLLOWLOCATION, $follow);

        curl_setopt($datapost, CURLOPT_POSTFIELDS, $data);
        curl_setopt($datapost, CURLOPT_COOKIEFILE, babel_path("Cookies/{$oj}_{$vcid}{$handle}.cookie"));
        curl_setopt($datapost, CURLOPT_COOKIEJAR, babel_path("Cookies/{$oj}_{$vcid}{$handle}.cookie"));
        ob_start();
        $response=curl_exec($datapost);
        if (curl_errno($datapost)) {
            throw new Exception(curl_error($datapost), 10000);
        }
        ob_end_clean();
        curl_close($datapost);
        unset($datapost);
        return $response;
    }
}
