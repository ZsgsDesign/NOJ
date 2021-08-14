<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    protected $table='carousel';
    protected $primaryKey='caid';

    protected $fillable=[
        'image', 'url', 'title', 'available'
    ];
}
