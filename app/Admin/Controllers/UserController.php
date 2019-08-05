<?php

namespace App\Admin\Controllers;

use App\Models\UserModel;
use App\Models\Eloquent\UserModel as EloquentUserModel;
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
            ->header('Users')
            ->description('all users')
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
            ->header('User Detail')
            ->description('the detail of users')
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
            ->header('Edit User')
            ->description('edit the detail of users')
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
            ->header('Create New User')
            ->description('create a new user')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new EloquentUserModel);
        $grid->id('ID')->sortable();
        $grid->name()->editable();
        $grid->email();
        $grid->created_at();
        $grid->updated_at();
        $grid->filter(function(Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->like('name');
            $filter->like('email')->email();
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
        $show=new Show(EloquentUserModel::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new EloquentUserModel);
        $form->model()->makeVisible('password');
        $form->tab('Basic', function(Form $form) {
            $form->display('id');
            $form->text('name')->rules('required');
            $form->email('email')->rules('required');
            $form->display('created_at');
            $form->display('updated_at');
        })->tab('Password', function(Form $form) {
            $form->password('password')->rules('confirmed');
            $form->password('password_confirmation');
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
