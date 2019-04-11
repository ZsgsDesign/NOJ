<?php

namespace App\Admin\Controllers;

use App\Models\SubmissionModel;
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
            ->header('Submissions')
            ->description('all submissions')
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
            ->header('Submission Detail')
            ->description('the detail of submissions')
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
            ->header('Edit Submission')
            ->description('edit the detail of submissions')
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
            ->header('Create New Submission')
            ->description('create a new submission')
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
        $grid->column('sid',"ID")->sortable();
        $grid->time("Time");
        $grid->memory("Memory");
        $grid->verdict("Verdict");
        $grid->language("Language");
        $grid->submission_date("Submission Date");
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
            // $form->display('pid');
            // $form->text('pcode')->rules('required');
            $form->text('title')->rules('required');
            $form->text('time_limit')->rules('required');
            $form->text('memory_limit')->rules('required');
            $form->display('OJ');
            $form->display('update_date');
            $form->text('tot_score')->rules('required');
            $form->select('partial', 'Partial Score')->options([
                0  => "No",
                1 => "Yes"
            ])->rules('required');
            $form->select('markdown', 'Markdown Support')->options([
                0  => "No",
                1 => "Yes"
            ])->rules('required');
        });
        return $form;
    }
}
