<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Judger;
use App\Models\JudgerModel;
use App\Models\Eloquent\OJ;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Model;

class JudgerController extends AdminController
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
            ->header(__('admin.judgers.index.header'))
            ->description(__('admin.judgers.index.description'))
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
            ->header(__('admin.judgers.show.header'))
            ->description(__('admin.judgers.show.description'))
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
            ->header(__('admin.judgers.edit.header'))
            ->description(__('admin.judgers.edit.description'))
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
            ->header(__('admin.judgers.create.header'))
            ->description(__('admin.judgers.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new Judger());

        $grid->column('jid', 'JID');
        $grid->column('handle', __('admin.judgers.handle'))->editable();
        $grid->column('password', __('admin.judgers.password'))->editable();
        $grid->column('available', __('admin.judgers.availability'))->display(function($available) {
            return $available ? '<i class="MDI check-circle wemd-teal-text"></i> '.__('admin.judgers.available') : '<i class="MDI close-circle wemd-pink-text"></i> '.__('admin.judgers.unavailable');
        });
        $grid->column('oid', __('admin.judgers.oj'))->display(function() {
            return $this->oj->name;
        });
        $grid->column('user_id', __('admin.judgers.user_id'))->editable();
        $grid->column('created_at', __('admin.created_at'));
        $grid->column('updated_at', __('admin.updated_at'));

        $grid->filter(function(Grid\Filter $filter) {
            $filter->like('handle', __('admin.judgers.handle'));
            $filter->like('password', __('admin.judgers.password'));
            $filter->like('user_id', __('admin.judgers.user_id'));
            $filter->equal('oid', __('admin.judgers.oj'))->select(OJ::all()->pluck('name', 'oid'));
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
        $show=new Show(Judger::findOrFail($id));

        $show->field('jid', 'JID');
        $show->field('handle', __('admin.judgers.handle'));
        $show->field('password', __('admin.judgers.password'));
        $show->field('available', __('admin.judgers.availability'))->as(function($available) {
            return $available ?__('admin.judgers.available') : __('admin.judgers.unavailable');
        });
        $show->field('oj.name', __('admin.judgers.oj'));
        $show->field('user_id', __('admin.judgers.user_id'));
        $show->field('created_at', __('admin.created_at'));
        $show->field('updated_at', __('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new Judger());
        $form->text('handle', __('admin.judgers.handle'))->help(__('admin.judgers.help.handle'))->required();
        $form->text('password', __('admin.judgers.password'))->help(__('admin.judgers.help.password'))->required();
        $form->switch('available', __('admin.judgers.availability'))->default(true);
        $form->select('oid', __('admin.judgers.oj'))->options(OJ::all()->pluck('name', 'oid'))->required();
        $form->text('user_id', __('admin.judgers.user_id'))->help(__('admin.judgers.help.user_id'));
        $form->hidden('using')->default(0);
        return $form;
    }
}
