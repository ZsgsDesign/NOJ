<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\JudgeServer;
use App\Models\JudgerModel;
use App\Models\Eloquent\OJ;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Model;

class JudgeServerController extends AdminController
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
            ->header(__('admin.judgeservers.index.header'))
            ->description(__('admin.judgeservers.index.description'))
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
            ->header(__('admin.judgeservers.show.header'))
            ->description(__('admin.judgeservers.show.description'))
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
            ->header(__('admin.judgeservers.edit.header'))
            ->description(__('admin.judgeservers.edit.description'))
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
            ->header(__('admin.judgeservers.create.header'))
            ->description(__('admin.judgeservers.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new JudgeServer());

        $grid->column('jsid', 'JSID');
        $grid->column('scode', __('admin.judgeservers.scode'));
        $grid->column('name', __('admin.judgeservers.name'));
        $grid->column('host', __('admin.judgeservers.host'));
        $grid->column('port', __('admin.judgeservers.port'));
        $grid->column('token', __('admin.judgeservers.token'));
        $grid->column('available', __('admin.judgeservers.availability'))->display(function($available) {
            return $available ?__('admin.judgeservers.available') : __('admin.judgeservers.unavailable');
        });
        $grid->column('OJ', __('admin.judgeservers.oj'))->display(function() {
            return $this->oj->name;
        });
        $grid->column('usage', __('admin.judgeservers.usage'))->display(function($usage) {
            return blank($usage) ? "-" : "$usage%";
        });
        $grid->column('status', __('admin.judgeservers.status'))->display(function($status) {
            $status=JudgerModel::$status[$status];
            return '<i class="MDI '.$status['icon'].' '.$status['color'].'"></i> '.$status['text'];
        });
        $grid->column('status_update_at', __('admin.judgeservers.status_update_at'));
        $grid->column('created_at', __('admin.created_at'));
        $grid->column('updated_at', __('admin.updated_at'));

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
        $show=new Show(JudgeServer::findOrFail($id));

        $show->field('jsid', 'JSID');
        $show->field('scode', __('admin.judgeservers.scode'));
        $show->field('name', __('admin.judgeservers.name'));
        $show->field('host', __('admin.judgeservers.host'));
        $show->field('port', __('admin.judgeservers.port'));
        $show->field('token', __('admin.judgeservers.token'));
        $show->field('available', __('admin.judgeservers.availability'))->as(function($available) {
            return $available ?__('admin.judgeservers.available') : __('admin.judgeservers.unavailable');
        });
        $show->field('oj.name', __('admin.judgeservers.oj'));
        $show->field('usage', __('admin.judgeservers.usage'))->as(function($usage) {
            return blank($usage) ? "-" : "$usage%";
        });
        $show->field('status', __('admin.judgeservers.status'))->unescape()->as(function($status) {
            $status=JudgerModel::$status[$status];
            return '<i class="MDI '.$status['icon'].' '.$status['color'].'"></i> '.$status['text'];
        });
        $show->field('status_update_at', __('admin.judgeservers.status_update_at'));
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
        $form=new Form(new JudgeServer());

        $form->text('scode', __('admin.judgeservers.scode'))->rules('required|alpha_dash|min:3|max:20');
        $form->text('name', __('admin.judgeservers.name'))->required();
        $form->text('host', __('admin.judgeservers.host'))->required();
        $form->text('port', __('admin.judgeservers.port'))->required();
        $form->text('token', __('admin.judgeservers.token'))->required();
        $form->switch('available', __('admin.judgeservers.availability'));
        $form->select('oid', __('admin.judgeservers.oj'))->options(OJ::all()->pluck('name', 'oid'))->help(__('admin.judgeservers.help.onlinejudge'))->required();
        $form->hidden('status', __('admin.judgeservers.status'))->default(0);
        return $form;
    }
}
