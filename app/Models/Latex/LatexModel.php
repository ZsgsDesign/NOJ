<?php

namespace App\Models\Latex;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Imagick;

class LatexModel extends Model
{
    public static function info($ltxsource, $type="png")
    {
        if(!Arr::has(['png','svg'],$type)) return [];
        $url=route("latex.$type", ['ltxsource' => $ltxsource]);
        $image = new Imagick();
        $image->readImageBlob(Storage::get('latex-svg/'.urlencode($ltxsource).'.svg'));
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();
        return [$url,$width/5,$height/5];
    }
}
