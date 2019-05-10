<?php

namespace App\Http\Controllers\VirtualCrawler;

use App\Http\Controllers\VirtualCrawler\CodeForces\CodeForces;
use App\Http\Controllers\VirtualCrawler\ContestHunter\ContestHunter;
use App\Http\Controllers\VirtualCrawler\POJ\POJ;
use App\Http\Controllers\VirtualCrawler\PTA\PTA;
use App\Http\Controllers\VirtualCrawler\Vijos\Vijos;
use App\Http\Controllers\VirtualCrawler\UVa\UVa;
use App\Models\ProblemModel;
use Auth;

class Crawler
{
    public $data=null;

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($name, $action, $con, $cached=false)
    {
        if ($name=="CodeForces") {
            $crawler=new CodeForces($action, $con, $cached);
        }
        if ($name=="ContestHunter") {
            $crawler=new ContestHunter($action, $con, $cached);
        }
        if ($name=="POJ") {
            $crawler=new POJ($action, $con, $cached);
        }
        if ($name=="PTA") {
            $crawler=new PTA($action, $con, $cached);
        }
        if ($name=="Vijos") {
            $crawler=new Vijos($action, $con, $cached);
        }
        if ($name=="UVa") {
            $crawler=new UVa($action, $con, $cached);
        }
        if (isset($crawler)) $this->data=$crawler->data;
    }
}
