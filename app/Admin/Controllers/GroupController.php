<?php

namespace App\Admin\Controllers;

use App\Models\GroupModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SubmissionController extends Controller
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
            ->body($this->grid());
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
        $grid = new Grid(new GroupModel);
        $grid->column('sid',"ID")->sortable();
        $grid->time("Time");
        $grid->memory("Memory");
        $grid->verdict("Verdict")->display(function ($verdict) {
            return '<i class="fa fa-circle '.$this->color.'"></i> '.$verdict;
        });
        $grid->language("Language");
        $grid->submission_date("Submission Date")->display(function ($submission_date) {
            return date("Y-m-d H:i:s",$submission_date);
        });;
        $grid->uid("UID");
        $grid->cid("CID");
        $grid->pid("PID");
        $grid->jid("JID");
        $grid->coid("COID");
        $grid->score("Raw Score");
        $grid->filter(function (Grid\Filter $filter) {
            $filter->like('verdict');
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
        $show = new Show(GroupModel::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new GroupModel);
        $form->model()->makeVisible('password');
        $form->tab('Basic', function (Form $form) {
            $form->display('sid');
            $form->text('time')->rules('required');
            $form->text('memory')->rules('required');
            $form->text('verdict')->rules('required');
            $form->text('color')->rules('required');
            $form->text('language')->rules('required');
            $form->display('submission_date');
            $form->number('uid')->rules('required');
            $form->number('cid');
            $form->number('pid')->rules('required');
            $form->number('jid')->rules('required');
            $form->number('coid')->rules('required');
            $form->number('score')->rules('required');
        });
        return $form;
    }
}
