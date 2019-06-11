<?php
namespace App\Babel;

use App\Babel\Submit\Submitter;
use App\Babel\Crawler\Crawler;
use App\Babel\Judge\Judger;

class Bable {

    public function submit($conf)
    {
        new Submitter($conf);
    }

    public function crawl($conf)
    {
        new Crawler($conf);
    }

    public function judge($conf)
    {
        new Judger($conf);
    }

}
