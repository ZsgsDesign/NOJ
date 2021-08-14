<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table='announcement';
    protected $primaryKey='anid';

    protected $fillable=[
        'title', 'content'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\User', 'uid');
    }

    public function getPostDateParsedAttribute()
    {
        return formatHumanReadableTime($this->created_at);
    }

    public function getContentParsedAttribute()
    {
        return clean(convertMarkdownToHtml($this->content));
    }
}
