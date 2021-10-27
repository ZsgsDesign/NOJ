<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Abuse;
use App\Models\Eloquent\Group;
use App\Models\Eloquent\GroupBanned;
use App\Models\Eloquent\UserBanned;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AbuseController extends AdminController
{
    use HasResourceActions;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title='Abuses';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new Abuse);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('cause', __('Cause'))->display(function() {
            return Abuse::$cause[$this->cause];
        });
        $grid->column('supplement', __('Supplement'));
        $grid->column('link', __('Link'));
        $grid->column('audit', __('Status'))->using(['0' => 'Pending', '1' => 'Passed']);
        $grid->column('user', __('Submitter'))->display(function() {
            return $this->user->readable_name;
        });
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show=new Show(Abuse::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('cause', __('Cause'));
        $show->field('supplement', __('Supplement'));
        $show->field('link', __('Link'));
        $show->field('audit', __('Audit'));
        $show->field('user_id', __('User id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new Abuse);

        $form->text('title', __('Title'));
        $form->number('cause', __('Cause'));
        $form->textarea('supplement', __('Supplement'));
        $form->textarea('link', __('Link'));
        $form->switch('audit', __('Audit'));
        $form->number('user_id', __('User id'));

        $form->ignore(['created_at']);

        $form->saving(function(Form $form) {
            $abuse=$form->model();
            //get gategory and subject id
            $regex='/^([A-Za-z]+) #(\d+)/';
            $matches=[];
            preg_match($regex, $abuse->title, $matches);
            $category=array_search(strtolower($matches[1]), Abuse::$supportCategory);
            $subject_id=(int) $matches[2];
            switch ($abuse->category) {
                case 0:
                    $gid=$subject_id;
                    $group=Group::find($gid);
                    if (empty($group)) {
                        return;
                    }
                    if ($form->audit) {
                        $ban_time=request()->created_at;
                        sendMessage([
                            'sender'    => config('app.official_sender'),
                            'receiver'  => $abuse->user_id,
                            'level'     => 5,
                            'title'     => "Your abuse report about group {$group->name} was passed",
                            'content'   => "Hi, Dear **{$abuse->user->name}**,\n\nWe have checked your Abuse report about group **[{$group->name}]({$group->link})**.\n\n We think you're right.\n\n So as the consequence leading to a temporary/permanent sanction against the group.\n\n Thank you for your contribution to our community environment.\n\n Sincerely, NOJ"
                        ]);
                        sendMessage([
                            'sender'    => config('app.official_sender'),
                            'receiver'  => $group->leader->id,
                            'level'     => 3,
                            'title'     => "Your group {$group->name} has been banned.",
                            'content'   => "Hi, Dear **{$group->leader->name}**,\n\n For the following reasons: \n\n {$abuse->supplement}\n\n your group **[{$group->name}]({$group->link})** is currently banned and will continue until {$ban_time}.\n\n Before this, only you can enter the group. \n\n Please rectify before this, or you may be subjected to more serious treatment.\n\n Thank you for your contribution to our community environment.\n\n Sincerely, NOJ"
                        ]);
                        $abuse->delete();
                        GroupBanned::create([
                            'abuse_id'   => $abuse->id,
                            'group_id'   => $group->gid,
                            'reason'     => $abuse->supplement,
                            'removed_at' => $ban_time
                        ]);
                        return;
                    } else {
                        sendMessage([
                            'sender'    => config('app.official_sender'),
                            'receiver'  => $abuse->user_id,
                            'level'     => 2,
                            'title'     => "Your abuse report about group {$group->name} was rejected",
                            'content'   => "Hi, Dear **{$abuse->user->name}**,\n\n We have checked your Abuse report about group **[{$group->name}]({$group->link})**.\n\n However, we regret to say that the information you submitted is not sufficient for us to take action.\n\n Of course, we will continue to follow up the investigation.\n\n Thank you for your contribution to our community environment.\n\n Sincerely, NOJ"
                        ]);
                        $abuse->delete();
                        return;
                    }
                    return;
                case 1:
                    $ban_time=request()->created_at;
                    UserBanned::create([
                        'abuse_id'   => $abuse->id,
                        'user_id'    => $subject_id,
                        'reason'     => $abuse->supplement,
                        'removed_at' => $ban_time
                    ]);
                    $abuse->delete();
                    return;
                default:
                    return;
            }


        });

        return $form;
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('Check Abuses')
            ->description('Refer to abuse reports submitted by users')
            ->body($this->check_form()->edit($id));
    }

    protected function check_form()
    {
        $form=new Form(new Abuse);
        $form->display('id', __('Abuse id'));
        $form->display('title', __('Title'));
        $form->display('cause', __('Cause'));
        $form->display('supplement', __('Supplement'));
        $form->display('link', __('Group Link'));
        $form->display('user_id', __('Submitter'));
        $form->radio('audit', 'result')->options(['0' => 'Reject', '1'=> 'Pass']);
        $form->datetime('created_at', 'ban until');

        return $form;
    }
}
