<?php
namespace App\Babel;

use App\Babel\Judger\Submit;
use App\Babel\Judger\Crawler;

class Bable {
    public function submit($conf)
    {
        new Submit($conf);
    }

    public function crawl($conf)
    {
        new Crawler($conf["name"], $conf["action"], $conf["con"], $conf["cached"]=="true");
    }

    public function judge($conf)
    {
        ;
    }
}
