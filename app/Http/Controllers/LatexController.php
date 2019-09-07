<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Requests;
use Imagick;

class LatexController extends Controller
{
    public function svg(Request $request)
    {
        $ltxsource = $request->input('ltxsource');
        if (is_null($ltxsource)) {
            return;
        }
        if (!Storage::exists('latex-svg/'.urlencode($ltxsource).'.svg')) {
            self::generateSVG($ltxsource);
        }
        return response()->make(Storage::get('latex-svg/'.urlencode($ltxsource).'.svg'), 200)->header('Content-Type', 'image/svg+xml');
    }

    public function png(Request $request)
    {
        $ltxsource = $request->input('ltxsource');
        if (is_null($ltxsource)) {
            return;
        }
        if (!Storage::exists('latex-png/'.urlencode($ltxsource).'.png')) {
            self::generatePNG($ltxsource);
        }
        return response()->make(Storage::get('latex-png/'.urlencode($ltxsource).'.png'), 200)->header('Content-Type', 'image/png');
    }

    private static function generatePNG($ltxsource)
    {
        if (!Storage::exists('latex-svg/'.urlencode($ltxsource).'.svg')) {
            self::generateSVG($ltxsource);
        }
        $image = new Imagick();
        $image->readImageBlob(Storage::get('latex-svg/'.urlencode($ltxsource).'.svg'));
        $res = $image->getImageResolution();
        $x_ratio = $res['x'] / $image->getImageWidth();
        $y_ratio = $res['y'] / $image->getImageHeight();
        // $ratio=intval(200/$image->getImageHeight());
        $ratio=10;
        $width=$image->getImageWidth()*$ratio;
        $height=$image->getImageHeight()*$ratio;
        $image->removeImage();
        $image->setResolution($width*$x_ratio, $height*$y_ratio);
        $image->setBackgroundColor(new \ImagickPixel('transparent'));
        $image->readImageBlob(Storage::get('latex-svg/'.urlencode($ltxsource).'.svg'));
        $image->setImageFormat("png32");
        Storage::put('latex-png/'.urlencode($ltxsource).'.png', $image->getImageBlob());
    }

    private static function generateSVG($ltxsource)
    {
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
}
