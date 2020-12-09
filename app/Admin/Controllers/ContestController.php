<?php

namespace App\Admin\Controllers;

use App\Models\ContestModel;
use App\Models\Eloquent\Contest as EloquentContestModel;
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
        $grid=new Grid(new EloquentContestModel);
        $grid->column('cid', "ID")->sortable();
        $grid->column("gid", "Group");
        $grid->name("Name")->editable();
        $grid->verified("Verified")->display(function($verified) {
            return $verified ? "Yes" : "No";
        });
        $grid->rated("Rated")->display(function($rated) {
            return $rated ? "Yes" : "No";
        });
        $grid->anticheated("AntiCheated")->display(function($anticheated) {
            return $anticheated ? "Yes" : "No";
        });
        $grid->featured("Featured")->display(function($featured) {
            return $featured ? "Yes" : "No";
        });
        $grid->column("rule", "Rule");
        $grid->begin_time("Begins");
        $grid->end_time("Ends");
        $grid->public("Public")->display(function($public) {
            return $public ? "Yes" : "No";
        });
        $grid->column("registration", "Registration")->display(function($registration) {
            return $registration ? "Required" : "Free";
        });
        $grid->registration_due("Registration Due");
        $grid->filter(function(Grid\Filter $filter) {
            $filter->equal('gid');
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
        $show=new Show(EloquentContestModel::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new EloquentContestModel);
        $form->model()->makeVisible('password');
        $form->tab('Basic', function(Form $form) {
            $form->display('cid');
            // $form->number('gid')->rules('required');
            $form->text('name')->rules('required');
        });
        return $form;
    }
}
