<?php

namespace App\Models\Latex;

use Illuminate\Database\Eloquent\Model;
use Imagick;

class LatexModel extends Model
{
    public static function info($ltxsource, $type="png")
    {
        if(!in_array($type,['png','svg'])) return [];
        $url=route("latex.$type", ['ltxsource' => $ltxsource]);
        $image = new Imagick();
        $image->readImageBlob(Storage::get('latex-svg/'.urlencode($ltxsource).'.svg'));
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();
        return [$url,$width/5,$height/5];
    }
}
