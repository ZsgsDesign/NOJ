<?php

namespace App\Http\Controllers\VirtualCrawler;

use App\Http\Controllers\VirtualCrawler\CodeForces\CodeForces;
use App\Http\Controllers\VirtualCrawler\ContestHunter\ContestHunter;
use App\Models\ProblemModel;
use Auth;

class Crawler
{

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($name,$action,$con,$cached = false)
    {
        if($name=="CodeForces") new CodeForces($action,$con,$cached);
        if($name=="ContestHunter") new ContestHunter($action,$con,$cached);
    }
}
