<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RankModel extends Model
{
    public $professionalRankiing=[
        "None"=>"wemd-black-text"
    ];

    public $casualRanking=[
        "Legendary Grandmaster"=>"cm-colorful-text",
        "International Grandmaster"=>"wemd-pink-text",
        "Grandmaster"=>"wemd-red-text",
        "International Master"=>"wemd-amber-text",
        "Master"=>"wemd-orange-text",
        "Candidate Master"=>"wemd-purple-text",
        "Expert"=>"wemd-blue-text",
        "Specialist"=>"wemd-cyan-text",
        "Pupil"=>"wemd-green-text",
        "Newbie"=>"wemd-gray-text",
    ];

    public function list()
    {
        return [];
    }
}
