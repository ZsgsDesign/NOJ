<?php

namespace App\Http\Controllers\VirtualCrawler\ContestHunter;

use App\Http\Controllers\VirtualCrawler\CrawlerBase;
use App\Models\ProblemModel;
use KubAT\PhpSimple\HtmlDomParser;
use Auth;
use Requests,Exception;

class ContestHunter extends CrawlerBase
{
    public $oid=3;
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
            $this->ContestHunter($con);
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

    private static function clearSampleText($raw) {
        return preg_replace_callback('/【?(\w*)样例(\w*)? ?(\d+)】?/u', function($match) {
            return $match[1].$match[2].$match[3];
        }, $raw);
    }

    public function ContestHunter($con)
    {
        $problemModel=new ProblemModel();
        $start=time();
        $f = fopen(__DIR__."/contesthunter_status.log", "w") or die("Unable to open file!");
        try {
            $res = Requests::get("http://contest-hunter.org:83/contest?type=1");
        }
        catch (Exception $e) {
            try { // It seems that first query often fails
                $res = Requests::get("http://contest-hunter.org:83/contest?type=1");
            }
            catch (Exception $e2) {
                fwrite($f, "Loaded contest list failed.".PHP_EOL);
                fclose($f);
                die("Loaded contest list failed.");
            }
        }
        preg_match_all('/<a href="\/contest\/([0-9A-Za-z$\-_.+!*\'\(\),%]*)"/', $res->body, $matches);
        $now=time()-$start;
        fwrite($f, "Loaded contest list at {$now}".PHP_EOL);
        $rcnames = array_reverse($matches[1]);
        foreach ($rcnames as $rcname) {
            $now=time()-$start;
            $cname = urldecode($rcname);
            $cid = substr($rcname, 0, 4);
            $tag = NULL;
            if (preg_match('/「(.*?)」/u', $cname, $match)) $tag = $match[1];
            try {
                $now=time()-$start;
                fwrite($f, "Start loading problem list of {$cid} at {$now}".PHP_EOL);
                $res = Requests::get("http://contest-hunter.org:83/contest/{$rcname}");
                preg_match_all('/<a href="\/contest\/[0-9A-Za-z$\-_.+!*\'\(\),%]*\/([0-9A-Fa-f]{4}%20[0-9A-Za-z$\-_.+!*\'\(\),%]*)"/', $res->body, $matches);
                $rpnames = $matches[1];
                foreach ($rpnames as $rpname) {
                    $pname = urldecode($rpname);
                    $pid = substr($rpname, 0, 4);
                    $now=time()-$start;
                    fwrite($f, "Start loading problem {$pid} at {$now}".PHP_EOL);
                    try {
                        $res = Requests::get("http://contest-hunter.org:83/contest/{$rcname}/{$rpname}");

                        $tests = ContestHunter::find('/<dt>测试点数<\/dt>\s*<dd>\s*(\d+)\s*<\/dd>/u', $res->body);
                        $totalTimeLimit = ContestHunter::find('/<dt>总时限<\/dt>\s*<dd>\s*([\d.]+) s\s*<\/dd>/u', $res->body);
                        if ($tests) $timeLimit = $totalTimeLimit * 1000 / $tests;
                        $memoryLimit = ContestHunter::find('/<dt>总内存<\/dt>\s*<dd>\s*(\d+) MiB\s*<\/dd>/u', $res->body);
                        if ($memoryLimit) $memoryLimit *= 1024;
                        $passes = ContestHunter::find('/<dt>通过率<\/dt>\s*<dd>\s*([\d,]+)\/[\d,]+\s*<\/dd>/u', $res->body);
                        $speTimeLimits = [
                            "0103" => 2000,
                            "1102" => 2000,
                            "1402" => 2000,
                            "3602" => 2000,
                            "6B12" => 3000,
                        ];
                        $descPattern = "<h4>描述</h4>";
                        $inputPattern = "<h4>输入格式</h4>";
                        $outputPattern = "<h4>输出格式</h4>";
                        $sampleInputPattern = "<h4>样例输入</h4>";
                        $codePattren = "<pre>";
                        $codeClosePattern = "</pre>";
                        $sourcePattern = "<h4>来源</h4>";
                        if ($pid == "6703" || $pid == "1808") {
                            if ($pid == "6703") {
                                $timeLimit = 2000;
                                $memoryLimit = 65536;
                            }
                            $descPattern = "<dt>描述</dt>";
                            $inputPattern = "<dt>输入</dt>";
                            $outputPattern = "<dt>输出</dt>";
                            $sampleInputPattern = "<dt>样例输入</dt>";
                            $sourcePattern = "<dt>来源</dt>";
                        } else if (array_key_exists($pid, $speTimeLimits)) {
                            $timeLimit = $speTimeLimits[$pid];
                            if ($pid == "3602") {
                                $codePattren = '<code style="display: inline-block; padding: 2px; border: 1px solid black;">';
                                $codeClosePattern = "</code>";
                            }
                        } else if ($pid == "2902") {
                            $outputPattern = "<p>输出格式</p>";
                        } else if ($pid == "5A01") {
                            $inputPattern = "<p>输入格式</p>";
                        }
                        $this->pro['pcode'] = 'CH'.$pid;
                        $this->pro['solved_count'] = $passes;
                        $this->pro['time_limit'] = $timeLimit;
                        $this->pro['memory_limit'] = $memoryLimit;
                        $this->pro['title'] = substr($pname, 5);
                        $this->pro['OJ'] = $this->oid;
                        $this->pro['input_type'] = 'standard input';
                        $this->pro['output_type'] = 'standard output';
                        $this->pro['contest_id'] = $rcname;
                        $this->pro['index_id'] = $rpname;
                        $this->pro['origin'] = "http://contest-hunter.org:83/contest/{$rcname}/{$rpname}";
                        $this->pro['source'] = $cname;

                        if (preg_match('/<a href="([0-9A-Za-z$\-_.+!*\'\(\),%:\/]*.pdf)">/', $res->body, $match)) {
                            $res = Requests::get($match[1]);
                            file_put_contents(base_path("public/external/gym/$pname.pdf"), $res->body);
                            $this->pro['description'] = "<a href=\"/external/gym/$rpname.pdf\">[Attachment Link]</a>";
                            $this->pro['input'] = '';
                            $this->pro['output'] = '';
                            $this->pro['sample'] = '';
                            $this->pro['note'] = '';
                        } else {
                            $pos1 = strpos($res->body, $descPattern) + strlen($descPattern);
                            $pos2 = strpos($res->body, $inputPattern, $pos1);
                            $this->pro['description'] = trim(substr($res->body, $pos1, $pos2 - $pos1));
                            $pos1 = $pos2 + strlen($inputPattern);
                            $pos2 = strpos($res->body, $outputPattern, $pos1);
                            $this->pro['input'] = trim(substr($res->body, $pos1, $pos2 - $pos1));
                            $pos1 = $pos2 + strlen($outputPattern);
                            $pos2 = strpos($res->body, $sampleInputPattern, $pos1);
                            $this->pro['output'] = trim(substr($res->body, $pos1, $pos2 - $pos1));
                            $samples = [];
                            while (($pos1 = strpos($res->body, $codePattren, $pos2)) !== FALSE) {
                                $pos1 += strlen($codePattren);
                                $pos2 = strpos($res->body, $codeClosePattern, $pos1);
                                $sampleInput = ContestHunter::clearSampleText(trim(substr($res->body, $pos1, $pos2 - $pos1)));
                                $pos1 = strpos($res->body, $codePattren, $pos2) + strlen($codePattren);
                                $pos2 = strpos($res->body, $codeClosePattern, $pos1);
                                $sampleOutput = ContestHunter::clearSampleText(trim(substr($res->body, $pos1, $pos2 - $pos1)));
                                if (strpos($sampleInput, "输入") !== FALSE) {
                                    $lip = 7;
                                    $lop = 7;
                                    $si = 2;
                                    do {
                                        $nip = strpos($sampleInput, "输入".$si, $lip);
                                        $nop = strpos($sampleOutput, "输出".$si, $lop);
                                        array_push($samples, [
                                            "sample_input" => trim(substr($sampleInput, $lip, ($nip === FALSE ? strlen($sampleInput) : $nip) - $lip)),
                                            "sample_output" => trim(substr($sampleOutput, $lop, ($nop === FALSE ? strlen($sampleOutput) : $nop) - $lop)),
                                        ]);
                                        $pl = 6 + strlen($si);
                                        $lip = $nip + $pl;
                                        $lop = $nop + $pl;
                                    } while ($nip !== FALSE);
                                } else {
                                    array_push($samples, [
                                        "sample_input" => $sampleInput,
                                        "sample_output" => $sampleOutput,
                                    ]);
                                }
                            }
                            $this->pro['sample'] = $samples;
                            $pos1 = $pos2 + 6;
                            $pos2 = strpos($res->body, $sourcePattern, $pos1);
                            if ($pos2 === FALSE) $pos2 = strpos($res->body, "</article>", $pos1);
                            $note = trim(substr($res->body, $pos1, $pos2 - $pos1));
                            while (substr($note, 0, 2) == '</') $note = trim(preg_replace('/<\/\w+>/', '', $note, 1));
                            if ($note == "<p>&nbsp;</p>") $note = ''; // cnmch
                            $this->pro['note'] = $note;
                        }

                        $problem=$problemModel->pid($this->pro['pcode']);

                        if ($problem) {
                            $problemModel->clearTags($problem);
                            $new_pid=$this->update_problem($this->oid);
                        } else {
                            $new_pid=$this->insert_problem($this->oid);
                        }

                        $problemModel->addTags($new_pid, $tag);

                        $now=time()-$start;
                        fwrite($f, "Finished loading problem {$pid} at {$now}".PHP_EOL);
                    }
                    catch (Exception $e) {
                        fwrite($f, "Failed loading problem {$pid}".PHP_EOL);
                    }
                }
                fwrite($f, "Finished loading problem list of {$cid} at {$now}".PHP_EOL);
            }
            catch(Exception $e) {
                fwrite($f, "Failed loading problem list of {$cid}".PHP_EOL);
            }
        }
        fclose($f);
    }
}
