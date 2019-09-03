<?php

namespace App\Admin\Controllers;

use App\Models\ProblemModel;
use App\Models\Eloquent\SolutionModel as EloquentSolutionModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SolutionController extends Controller
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
            ->header('Solutions')
            ->description('all solutions')
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
            ->header('Solution Detail')
            ->description('the detail of solutions')
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
            ->header('Edit Solutions')
            ->description('edit the detail of solutions')
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
            ->header('Create New Solution')
            ->description('create a new solution')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new EloquentSolutionModel);
        $grid->psoid("ID")->sortable();
        $grid->uid("Uid")->editable();
        $grid->pid("Pid")->editable();
        $grid->content("Content")->display(function($content) {
            $contentParsed=clean(convertMarkdownToHtml($content));
            return "$contentParsed";
        });
        $grid->audit("Audit")->editable('select', [0 => 'Waiting', 1 => 'Accepted', 2 => 'Declined']);
        $grid->votes("Votes");
        $grid->filter(function(Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->equal('uid');
            $filter->equal('pid');
            $filter->equal('audit')->radio([
                ''    => 'All',
                '0'   => 'Waiting',
                '1'   => 'Accepted',
                '2'   => 'Declined',
            ]);
        });
        $grid->expandFilter();
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
        $show=new Show(EloquentSolutionModel::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new EloquentSolutionModel);
        $form->model()->makeVisible('password');
        $form->tab('Basic', function(Form $form) {
            $form->display("psoid");
            $form->text("uid")->rules('required');
            $form->text("pid")->rules('required');
            $form->text("content")->rules('required');
            $form->text("audit")->rules('required');
            $form->text("votes")->rules('required');
            $form->display("created_at");
            $form->display("updated_at");
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
