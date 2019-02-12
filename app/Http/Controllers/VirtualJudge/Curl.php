<?php
namespace App\Http\Controllers\VirtualJudge;

use App\Models\Submission;

class Curl
{
    public function __construct()
    {
        //
    }

    protected function login($url, $data, $oj, $ret='false')
    {
        $datapost = curl_init();
        $headers = array("Expect:");

        curl_setopt($datapost, CURLOPT_CAINFO, dirname(__FILE__)."/cookie/cacert.pem");
        curl_setopt($datapost, CURLOPT_URL, $url);
        curl_setopt($datapost, CURLOPT_HEADER, true);//
        curl_setopt($datapost, CURLOPT_HTTPHEADER, $headers);//
        curl_setopt($datapost, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($datapost, CURLOPT_POST, true);

        curl_setopt($datapost, CURLOPT_RETURNTRANSFER, $ret);
        curl_setopt($datapost, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($datapost, CURLOPT_POSTFIELDS, $data);
        curl_setopt($datapost, CURLOPT_COOKIEJAR, dirname(__FILE__)."/cookie/{$oj}_cookie.txt");
        curl_setopt($datapost, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/{$oj}_cookie.txt");
        ob_start();
        $response=curl_exec($datapost);
        if (curl_errno($datapost)) {
            die(curl_error($datapost));
        }
        ob_end_clean();
        curl_close($datapost);
        unset($datapost);
        return $response;
    }

    protected function grab_page($site, $oj)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cookie/cacert.pem");
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) ."/cookie/{$oj}_cookie.txt");
        curl_setopt($ch, CURLOPT_URL, $site);
        ob_start();
        $response= curl_exec($ch);
        if (curl_errno($ch)) {
            die(curl_error($ch));
        }
        ob_end_clean();
        curl_close($ch);
        return $response;
    }

    protected function post_data($site, $data, $oj, $ret=false)
    {
        $datapost = curl_init();
        $headers = array("Expect:");
        curl_setopt($datapost, CURLOPT_CAINFO, dirname(__FILE__)."/cookie/cacert.pem");
        curl_setopt($datapost, CURLOPT_URL, $site);
        curl_setopt($datapost, CURLOPT_HEADER, true);
        curl_setopt($datapost, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($datapost, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($datapost, CURLOPT_POST, true);

        curl_setopt($datapost, CURLOPT_RETURNTRANSFER, $ret);
        curl_setopt($datapost, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($datapost, CURLOPT_POSTFIELDS, $data);
        curl_setopt($datapost, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/{$oj}_cookie.txt");
        ob_start();
        $response=curl_exec($datapost);
        if (curl_errno($datapost)) {
            die(curl_error($datapost));
        }
        ob_end_clean();
        curl_close($datapost);
        unset($datapost);
        return $response;
    }
}
