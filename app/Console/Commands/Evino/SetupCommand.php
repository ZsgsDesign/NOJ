<?php

namespace App\Console\Commands\Evino;

use Illuminate\Console\Command;
use KubAT\PhpSimple\HtmlDomParser;
use File;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evino:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup <fg=green>EVINO</> - <fg=yellow;options=bold,underscore><Extended View Interface for NOJ></>.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $evinoPath = base_path('node_modules' . DIRECTORY_SEPARATOR . 'evino' . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR . 'spa');

        if(!is_dir($evinoPath)) {
            $this->error('Unable to locate evino path, please run npm ci and npm run production first.');
            return Command::FAILURE;
        }

        File::copyDirectory($evinoPath . DIRECTORY_SEPARATOR . 'css', public_path('css'));
        File::copyDirectory($evinoPath . DIRECTORY_SEPARATOR . 'fonts', public_path('fonts'));
        File::copyDirectory($evinoPath . DIRECTORY_SEPARATOR . 'icons', public_path('icons'));
        File::copyDirectory($evinoPath . DIRECTORY_SEPARATOR . 'js', public_path('js'));

        $indexHTML = file_get_contents($evinoPath . DIRECTORY_SEPARATOR . 'index.html');
        $indexDOM = HtmlDomParser::str_get_html($indexHTML, true, true, DEFAULT_TARGET_CHARSET, false);
        $SPABladeView = '';

        foreach($indexDOM->find('head > script') as $scriptResources) {
            $SPABladeView .= $scriptResources->outertext;
        }
        foreach($indexDOM->find('head > link[rel="stylesheet"]') as $styleResources) {
            $SPABladeView .= $styleResources->outertext;
        }

        file_put_contents(resource_path('views' . DIRECTORY_SEPARATOR . 'spa' . DIRECTORY_SEPARATOR . 'resources.blade.php'), $SPABladeView);
        file_put_contents(resource_path('views' . DIRECTORY_SEPARATOR . 'spa' . DIRECTORY_SEPARATOR . 'body.blade.php'), "<body><div id=q-app></div></body>");

        return Command::SUCCESS;
    }
}
