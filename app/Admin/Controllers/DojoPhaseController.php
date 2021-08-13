<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Dojo\Dojo;
use App\Http\Controllers\Controller;
use App\Models\Eloquent\Dojo\DojoPhase;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class DojoPhaseController extends Controller
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
            ->header(__('admin.dojophases.index.header'))
            ->description(__('admin.dojophases.index.description'))
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
            ->header(__('admin.dojophases.show.header'))
            ->description(__('admin.dojophases.show.description'))
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
            ->header(__('admin.dojophases.edit.header'))
            ->description(__('admin.dojophases.edit.description'))
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
            ->header(__('admin.dojophases.create.header'))
            ->description(__('admin.dojophases.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new DojoPhase);
        $grid->column('id', "ID")->sortable();
        $grid->column("name", __('admin.dojophases.name'))->editable();

        $grid->column("passline", __('admin.dojophases.passline'));
        $grid->column("order", __('admin.dojophases.order'))->sortable();
        $grid->created_at(__('admin.created_at'));
        $grid->updated_at(__('admin.updated_at'));

        $grid->filter(function(Grid\Filter $filter) {
            $filter->column(6, function($filter) {
                $filter->like('name', __('admin.dojophases.name'));
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
        $show=new Show(DojoPhase::findOrFail($id));
        $show->id('ID');
        $show->name(__('admin.dojophases.name'));
        $show->description(__('admin.dojophases.description'));
        $show->passline(__('admin.dojophases.passline'));
        $show->order(__('admin.dojophases.order'));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new DojoPhase);
        $form->tab('Basic', function(Form $form) {
            $form->display('id', 'ID');
            $form->text('name', __('admin.dojophases.name'))->rules('required');
            $form->textarea('description', __('admin.dojophases.description'))->rules('required');
            $form->number('passline', __('admin.dojophases.passline'))->default(0)->rules('required');
            $form->number('order', __('admin.dojophases.order'))->default(0)->rules('required');
        });
        return $form;
    }
}
