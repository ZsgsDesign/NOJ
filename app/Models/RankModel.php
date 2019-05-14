<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroupModel extends Model
{
    public $rankiing=[
        ""
    ];

    public function list()
    {
        return [];
    }
}
