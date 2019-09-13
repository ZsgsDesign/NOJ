<?php

namespace App\Admin\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use POEM\Parser as POEMParser;

class ImportPOEM extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'Import';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $err = function ($msg) {
            $error = new MessageBag([
                'title'   => 'POEM parse failed.',
                'message' => $msg,
            ]);
            return back()->with(compact('error'));
        };
        $success_message = '';

        $file = $request->file('Files');
        if(!$file->isValid()){
            $err('Invalid POEM files');
        }

        $path = $file->getRealPath();
        $poetryRaw=file_get_contents($path);
        $parser=new POEMParser();
        dd($parser->parse($poetryRaw));

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->file('Files')->rules('required');
    }
}
