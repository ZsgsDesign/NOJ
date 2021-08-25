<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Dojo\Dojo;
use App\Http\Controllers\Controller;
use App\Models\Eloquent\Dojo\DojoPhase;
use App\Models\Eloquent\Problem;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class DojoController extends Controller
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
            ->header(__('admin.dojos.index.header'))
            ->description(__('admin.dojos.index.description'))
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
            ->header(__('admin.dojos.show.header'))
            ->description(__('admin.dojos.show.description'))
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
            ->header(__('admin.dojos.edit.header'))
            ->description(__('admin.dojos.edit.description'))
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
            ->header(__('admin.dojos.create.header'))
            ->description(__('admin.dojos.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new Dojo);
        $grid->column('id', "ID")->sortable();
        $grid->column("name", __('admin.dojos.name'))->editable();
        $grid->column("dojo_phase", __('admin.dojos.phase'))->display(function() {
            return $this->phase->name;
        });
        $grid->column("totproblem", __('admin.dojos.totproblem'))->display(function() {
            return $this->tot_problem;
        });
        $grid->column("passline", __('admin.dojos.passline'));
        $grid->column("precondition", __('admin.dojos.precondition'))->display(function($precondition) {
            $output='';
            foreach ($precondition as $p) {
                $output.='<span class="label label-primary">'.Dojo::find($p)->name.'</span> ';
            }
            return $output;
        });
        $grid->column("order", __('admin.dojos.order'))->sortable();
        $grid->created_at(__('admin.created_at'));
        $grid->updated_at(__('admin.updated_at'));

        $grid->filter(function(Grid\Filter $filter) {
            $filter->column(6, function($filter) {
                $filter->like('name', __('admin.dojos.name'));
            });
            $filter->column(6, function($filter) {
                $filter->equal('dojo_phase_id', __('admin.dojos.phase'))->select(DojoPhase::all()->pluck('name', 'id'));
            });
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
        $show=new Show(Dojo::findOrFail($id));
        $show->id('ID');
        $show->name(__('admin.dojos.name'));
        $show->description(__('admin.dojos.description'));
        $show->dojo_phase_id(__('admin.dojos.phase'));
        $show->passline(__('admin.dojos.passline'));
        $show->order(__('admin.dojos.order'));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new Dojo);
        $form->tab('Basic', function(Form $form) {
            $form->display('id', 'ID');
            $form->text('name', __('admin.dojos.name'))->rules('required');
            $form->textarea('description', __('admin.dojos.description'))->rules('required');
            $form->select('dojo_phase_id', __('admin.dojos.phase'))->options(DojoPhase::all()->pluck('name', 'id'))->rules('required');
            $form->number('passline', __('admin.dojos.passline'))->default(0)->rules('required');
            $form->number('order', __('admin.dojos.order'))->default(0)->rules('required');
            $form->multipleSelect('precondition', __('admin.dojos.precondition'))->options(Dojo::all()->pluck('name', 'id'));
            $form->hasMany('problems', __('admin.dojos.problems'), function(Form\NestedForm $form) {
                $form->select('problem_id', __('admin.dojos.problem'))->options(function($pid) {
                    $problem=Problem::find($pid);
                    if ($problem) {
                        return [$problem->pid => $problem->readable_name];
                    }
                })->config('minimumInputLength', 4)->ajax(route('admin.api.problems'))->required();
                $form->number('order', __('admin.dojos.problemorder'))->default(0)->required();
            });
        });
        return $form;
    }
}
