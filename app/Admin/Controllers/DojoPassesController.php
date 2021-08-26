<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Dojo\DojoPass;
use App\Models\Eloquent\Dojo\Dojo;
use App\Models\Eloquent\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class DojoPassesController extends Controller
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
            ->header(__('admin.dojopasses.index.header'))
            ->description(__('admin.dojopasses.index.description'))
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
            ->header(__('admin.dojopasses.show.header'))
            ->description(__('admin.dojopasses.show.description'))
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
            ->header(__('admin.dojopasses.edit.header'))
            ->description(__('admin.dojopasses.edit.description'))
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
            ->header(__('admin.dojopasses.create.header'))
            ->description(__('admin.dojopasses.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new DojoPass);
        $grid->column('id', "ID")->sortable();
        $grid->column("dojo_name", __('admin.dojopasses.dojo'))->display(function() {
            return $this->dojo->name;
        });
        $grid->column("user_readable", __('admin.dojopasses.user'))->display(function() {
            return $this->user->readable_name;
        });
        $grid->column('updated_at', __('admin.dojopasses.updated_at'));

        $grid->filter(function(Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->column(6, function($filter) {
                $filter->equal('dojo_id', __('admin.dojopasses.dojo'))->select(Dojo::all()->pluck('name', 'id'));
            });
            $filter->column(6, function($filter) {
                $filter->equal('user_id', __('admin.dojopasses.user'))->select(function($id) {
                    $user=User::find($id);
                    if ($user) {
                        return [$user->id => $user->readable_name];
                    }
                })->config('minimumInputLength', 4)->ajax(route('admin.api.users'));
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
        $show=new Show(DojoPass::findOrFail($id));
        $show->field('id', "ID");
        $show->field("dojo_name", __('admin.dojopasses.dojo'))->as(function() {
            return $this->dojo->name;
        });
        $show->field("user_readable", __('admin.dojopasses.user'))->as(function() {
            return $this->user->readable_name;
        });
        $show->field('updated_at', __('admin.dojopasses.updated_at'));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new DojoPass);
        $form->tab('Basic', function(Form $form) {
            $form->display('id', 'ID');
            $form->select('dojo_id', __('admin.dojopasses.dojo'))->options(Dojo::all()->pluck('name', 'id'))->rules('required');
            $form->select('user_id', __('admin.dojopasses.user'))->options(function($id) {
                $user=User::find($id);
                if ($user) {
                    return [$user->id => $user->readable_name];
                }
            })->config('minimumInputLength', 4)->ajax(route('admin.api.users'))->rules('required');

            $form->saving(function(Form $form) {
                $err=function($msg, $title='Error occur.') {
                    $error=new MessageBag([
                        'title'   => $title,
                        'message' => $msg,
                    ]);
                    return back()->with(compact('error'));
                };
                $user_id=$form->user_id;
                $dojo_id=$form->dojo_id;
                $pass=DojoPass::where([
                    "dojo_id" => $dojo_id,
                    "user_id" => $user_id,
                ])->first();

                $pass_id=$form->model()->id ?? null;
                if (!blank($pass) && $pass->id!=$pass_id) {
                    return $err('User has passed this dojo', 'Error occured.');
                }
            });
        });
        return $form;
    }
}
