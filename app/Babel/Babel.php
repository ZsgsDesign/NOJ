<?php
namespace App\Babel;

use App\Babel\Submit\Submitter;
use App\Babel\Crawl\Crawler;
use App\Babel\Judge\Judger;

class Babel
{

    public function submit($conf)
    {
        return new Submitter($conf);
    }

    public function crawl($conf)
    {
        return new Crawler($conf);
    }

    public function judge()
    {
        return new Judger();
    }

}
