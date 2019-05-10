<?php
namespace App\Babel;

class Bable {
    public function submit($conf)
    {
        new Judger\Submit($conf);
    }

    public function crawl($conf)
    {
        new Crawler\Crawler($conf["name"], $conf["action"], $conf["con"], $conf["cached"]=="true");
    }

    public function judge($conf)
    {
        ;
    }
}
