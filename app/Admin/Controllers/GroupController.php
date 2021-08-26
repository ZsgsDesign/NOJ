<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Group;
use App\Models\Eloquent\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;
use App\Models\Eloquent\GroupMember;

class GroupController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Groups')
            ->description('all groups')
            ->body($this->grid()->render());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Group Detail')
            ->description('the detail of groups')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit Group')
            ->description('edit the detail of groups')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create New Group')
            ->description('create a new group')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new Group);
        $grid->column('gid', "ID")->sortable();
        $grid->column("gcode", "Group Code");
        $grid->img("Focus Image")->display(function($url) {
            return '<img src="'.$url.'" style="max-width:200px;max-height:200px" class="img img-thumbnail">';
        });
        $grid->name("Name")->editable();
        $grid->public("Publicity")->display(function($public) {
            return $public ? "Public" : "Private";
        });
        $grid->verified("Verified")->display(function($verified) {
            return $verified ? "Yes" : "No";
        });
        $grid->join_policy("Join Policy");
        // $grid->custom_icon("Custom Icon")->image();
        // $grid->custom_title("Custom Title");
        $grid->filter(function(Grid\Filter $filter) {
            $filter->like('gcode');
            $filter->like('name');
        });
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
        $show=new Show(Group::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new Group);
        $form->model()->makeVisible('password');
        $form->tab('Basic', function(Form $form) {
            $form->text('gcode')->rules('required|alpha_dash|min:3|max:50');
            $form->text('name')->rules('required|min:3|max:50');
            $form->switch('public')->default(true);
            $form->textarea('description')->rules('nullable|max:60000');
            $form->select('join_policy', 'Join Policy')->options([
                0 => "Cannot Join",
                1 => "Invite Only",
                2 => "Apply Only",
                3 => "Invite & Apply"
            ])->default(1);
            $form->image('img', 'Custom Group Focus Image')->uniqueName()->move("static/img/group");
            if ($form->isCreating()) {
                $form->select('leader_uid', 'Group Leader')->options(function($id) {
                    $user=User::find($id);
                    if ($user) {
                        return [$user->id => $user->readable_name];
                    }
                })->config('minimumInputLength', 4)->ajax(route('admin.api.users'))->required();
            }
            $form->ignore(['leader_uid']);
            $form->saving(function(Form $form) {
                $err=function($msg, $title='Error occur.') {
                    $error=new MessageBag([
                        'title'   => $title,
                        'message' => $msg,
                    ]);
                    return back()->with(compact('error'));
                };
                $gcode=$form->gcode;
                $g=Group::where('gcode', $gcode)->first();
                //check gcode has been token.
                $gid=$form->model()->gid ?? null;
                if (!empty($gcode) && !blank($g) && $g->gid!=$gid) {
                    return $err('Gcode has been token', 'Error occur.');
                }
            });
            $form->saved(function(Form $form) {
                if ($form->isCreating()) {
                    $form->model()->members()->saveMany([new GroupMember([
                        'gid' => $form->model()->gid,
                        'uid' => request('leader_uid'),
                        'role' => 3,
                        'ranking' => 1500,
                    ])]);
                }
            });
        });
        return $form;
    }
}
