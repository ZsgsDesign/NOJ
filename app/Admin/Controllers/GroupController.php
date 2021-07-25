<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Group;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

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
            $form->text('gcode')->rules('required');
            $form->text('name')->rules('required');
            $form->switch('public')->default(true);
            $form->text('description');
            $form->select('join_policy', '加入政策')->options([
                1 => "仅邀请",
                2 => "仅申请",
                3 => "邀请或申请"
            ])->default(1);
            $form->image('img', '自定义小组图标')->uniqueName()->move("static/img/group");
        });
        return $form;
    }
}
