<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends Controller
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
            ->header(__('admin.users.index.header'))
            ->description(__('admin.users.index.description'))
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
            ->header(__('admin.users.show.header'))
            ->description(__('admin.users.show.description'))
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
            ->header(__('admin.users.edit.header'))
            ->description(__('admin.users.edit.description'))
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
            ->header(__('admin.users.create.header'))
            ->description(__('admin.users.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new User);
        $grid->id('ID')->sortable();
        $grid->name(__('admin.users.name'))->editable();
        $grid->email(__('admin.users.email'));
        $grid->created_at(__('admin.created_at'));
        $grid->updated_at(__('admin.updated_at'));
        $grid->filter(function(Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->like('name', __('admin.users.name'));
            $filter->like('email', __('admin.users.email'))->email();
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
        $show=new Show(User::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new User);
        $form->model()->makeVisible('password');
        $form->tab(__('admin.users.basic'), function(Form $form) {
            if ($form->isEditing()) {
                $form->display('id', 'ID');
            }
            $form->text('name', __('admin.users.name'))->rules('required');
            $form->email('email', __('admin.users.email'))->rules('required');
            if ($form->isEditing()) {
                $form->display('created_at', __('admin.created_at'));
                $form->display('updated_at', __('admin.updated_at'));
            }
        })->tab(__('admin.users.password'), function(Form $form) {
            $form->password('password', __('admin.password'))->rules('confirmed');
            $form->password('password_confirmation', __('admin.password_confirmation'));
        });
        $form->ignore(['password_confirmation']);
        $form->saving(function(Form $form) {
            if ($form->password && $form->model()->password!=$form->password) {
                $form->password=bcrypt($form->password);
            }
        });
        return $form;
    }
}
