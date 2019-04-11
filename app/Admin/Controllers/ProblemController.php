<?php

namespace App\Admin\Controllers;

use App\Models\ProblemModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProblemController extends Controller
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
            ->header('Problems')
            ->description('all problems')
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
            ->header('Problem Detail')
            ->description('the detail of problems')
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
            ->header('Edit Problem')
            ->description('edit the detail of problems')
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
            ->header('Create New Problem')
            ->description('create a new problem')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProblemModel);
        $grid->column('pid',"ID")->sortable();
        $grid->column('pcode',"PCode")->editable();
        $grid->title("Title")->editable();
        $grid->time_limit("Time/ms")->editable();
        $grid->memory_limit("Memory/kb")->editable();
        $grid->OJ();
        $grid->update_date();
        $grid->tot_score("Score");
        $grid->partial("Partial")->display(function ($partial) {
            return $partial ? 'Yes' : 'No';
        });
        $grid->markdown("Markdown")->display(function ($markdown) {
            return $markdown ? 'Yes' : 'No';
        });
        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->like('pcode');
            $filter->like('title');
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
        $show = new Show(ProblemModel::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ProblemModel);
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
