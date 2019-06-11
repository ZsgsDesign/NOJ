<?php
namespace App\Babel;

use App\Babel\Submit\Submit;
use App\Babel\Crawler\Crawler;

class Bable {
    public function submit($conf)
    {
        new Submit($conf);
    }

    public function crawl($conf)
    {
        new Crawler($conf);
    }

    public function judge($conf)
    {
        ;
    }
}
