<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Requests;

class LatexController extends Controller
{
    public function latex(Request $request)
    {
        $ltxsource = $request->input('ltxsource');
        if (is_null($ltxsource)) return;
        if(!Storage::exists('latex-svg/'.urlencode($ltxsource).'.svg')) {
            $contents=str_replace('fill:rgb(0%,0%,0%)', 'fill:rgb(0,0,0)', Requests::get('http://www.tlhiv.org/ltxpreview/ltxpreview.cgi?' . http_build_query([
                'width' => 10,
                'height' => 10,
                'ltx' => '',
                'ltxsource' => $ltxsource,
                'result' => 'preview',
                'init' => 0,
            ]))->body);
            Storage::put('latex-svg/'.urlencode($ltxsource).'.svg', $contents);
        }
        return response()->make(Storage::get('latex-svg/'.urlencode($ltxsource).'.svg'), 200)->header('Content-Type', 'image/svg+xml');
    }
}
