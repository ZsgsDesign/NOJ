<?php
namespace App\Babel;

use App\Babel\Submit\Submitter;
use App\Babel\Crawl\Crawler;
use App\Babel\Judge\Judger;
use App\Babel\Synchronize\Synchronizer;
use App\Babel\Monit\Monitor;
use App\Babel\TestRun\TestRunner;

class Babel
{
    public function submit($conf)
    {
        return new Submitter($conf);
    }

    public function crawl($conf, $commandLineObject=null)
    {
        return new Crawler($conf, $commandLineObject);
    }

    public function judge()
    {
        return new Judger();
    }

    public function synchronize($conf)
    {
        return new Synchronizer($conf);
    }

    public function monitor($conf)
    {
        return new Monitor($conf);
    }

    public function testrun($conf)
    {
        return new TestRunner($conf);
    }
}
