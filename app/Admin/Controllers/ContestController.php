<?php

namespace App\Admin\Controllers;

use App\Models\ContestModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ContestController extends Controller
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
            ->header('Contests')
            ->description('all contests')
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
            ->header('Contest Detail')
            ->description('the detail of contests')
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
            ->header('Edit Contest')
            ->description('edit the detail of contests')
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
            ->header('Create New Contest')
            ->description('create a new contest')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SubmissionModel);
        $grid->column('cid',"ID")->sortable();
        $grid->gid("Group");
        $grid->name("Name");
        $grid->verified("Verified")->display(function ($verified) {
            return $verified?"Yes":"No";
        });
        $grid->rated("Rated")->display(function ($rated) {
            return $rated?"Yes":"No";
        });
        $grid->anticheated("AntiCheated")->display(function ($anticheated) {
            return $anticheated?"Yes":"No";
        });
        $grid->featured("Featured")->display(function ($featured) {
            return $featured?"Yes":"No";
        });
        $grid->rule("Rule");
        $grid->begin_time("Begins");
        $grid->end_time("Ends");
        $grid->public("Public")->display(function ($public) {
            return $public?"Yes":"No";
        });
        $grid->registration("Registration")->display(function ($registration) {
            return $registration?"Required":"Free";
        });
        $grid->registration_due("Registration Due");
        $grid->filter(function (Grid\Filter $filter) {
            $filter->match('gid');
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
        $show = new Show(SubmissionModel::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SubmissionModel);
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
