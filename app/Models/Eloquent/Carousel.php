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

    public static function boot()
    {
        parent::boot();
        static::saving(function($model) {
            if ($model->image!="" && $model->image!=null && $model->image[0]!="/") {
                $model->image="/$model->image";
            }
        });
    }
}
