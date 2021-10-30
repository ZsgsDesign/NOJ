<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\GroupHomework;
use App\Models\Eloquent\GroupHomeworkProblem;
use DateTimeInterface;
use DB;
use Log;
use Exception;

class Group extends Model
{
    use HasFactory;

    protected $table='group';
    protected $primaryKey='gid';

    public function members()
    {
        return $this->hasMany('App\Models\Eloquent\GroupMember', 'gid', 'gid');
    }

    public function banneds()
    {
        return $this->hasMany('App\Models\Eloquent\GroupBanned', 'group_id', 'gid');
    }

    public function homework()
    {
        return $this->hasMany('App\Models\Eloquent\GroupHomework', 'group_id', 'gid');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function($model) {
            if ($model->img!="" && $model->img!=null && $model->img[0]!="/") {
                $model->img="/$model->img";
            }
        });
    }

    public function getLeaderAttribute()
    {
        return $this->members()->where('role', 3)->first()->user;
    }

    public function getLinkAttribute()
    {
        return route('group.detail', ['gcode' => $this->gcode]);
    }

    public function addHomework($title, $description, $endedAt, $problems)
    {
        DB::beginTransaction();
        try {

            // Create Homework Itself

            $newHomework = new GroupHomework();

            $newHomework->title = $title;
            $newHomework->description = $description;
            $newHomework->ended_at = $endedAt;
            $newHomework->is_simple = 1;
            $this->homework()->save($newHomework);

            // Created Related Problem List

            $problemIndex = 1;
            foreach ($problems as $problem) {
                $newProblem = new GroupHomeworkProblem();
                $newProblem->problem_id = $problem['pid'];
                $newProblem->order_index = $problemIndex++;
                $newHomework->problems()->save($newProblem);
            }

            // Commit Transaction

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            Log::alert($e);
            throw new Exception($e->getMessage());
        }

        return $newHomework;
    }
}
