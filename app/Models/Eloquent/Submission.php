<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Submission extends Model
{
    protected $table='submission';
    protected $primaryKey='sid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    protected $guarded = [];

    public function compiler()
    {
        return $this->belongsTo('App\Models\Eloquent\Compiler', 'coid');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\UserModel', 'uid');
    }

    public function contest()
    {
        return $this->belongsTo('App\Models\Eloquent\Contest', 'cid');
    }

    public function problem()
    {
        return $this->belongsTo('App\Models\Eloquent\Problem', 'pid');
    }

    public function getNcodeAttribute()
    {
        $contest = $this->contest;
        return $contest->problems->where('pid', $this->pid)->first()->ncode;
    }

    public function getNickNameAttribute()
    {
        $member = $this->contest->group->members()->where('uid', $this->user->id)->first();
        if(!empty($member)) {
            return $member->nickname;
        }
        return null;
    }

    public function getColorAttribute()
    {
        return [
            "Waiting"                => "wemd-blue-text",
            "Judge Error"            => "wemd-black-text",
            "System Error"           => "wemd-black-text",
            "Compile Error"          => "wemd-orange-text",
            "Runtime Error"          => "wemd-red-text",
            "Wrong Answer"           => "wemd-red-text",
            "Time Limit Exceed"      => "wemd-deep-purple-text",
            "Real Time Limit Exceed" => "wemd-deep-purple-text",
            "Accepted"               => "wemd-green-text",
            "Memory Limit Exceed"    => "wemd-deep-purple-text",
            "Presentation Error"     => "wemd-red-text",
            "Submitted"              => "wemd-blue-text",
            "Pending"                => "wemd-blue-text",
            "Judging"                => "wemd-blue-text",
            "Partially Accepted"     => "wemd-cyan-text",
            'Submission Error'       => 'wemd-black-text',
            'Output Limit Exceeded'  => 'wemd-deep-purple-text',
            "Idleness Limit Exceed"  => 'wemd-deep-purple-text'
        ][$this->verdict];
    }

    public function getLangAttribute()
    {
        return $this->compiler->lang;
    }

    public function getSubmissionDateParsedAttribute()
    {
        $submission_date = date('Y-m-d H:i:s', $this->submission_date);
        return formatHumanReadableTime($submission_date);
    }


}
