<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Announcement;
use App\Models\Eloquent\User;
use App\Models\Eloquent\OJ;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AnnouncementController extends AdminController
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
            ->header(__('admin.announcements.index.header'))
            ->description(__('admin.announcements.index.description'))
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
            ->header(__('admin.announcements.show.header'))
            ->description(__('admin.announcements.show.description'))
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
            ->header(__('admin.announcements.edit.header'))
            ->description(__('admin.announcements.edit.description'))
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
            ->header(__('admin.announcements.create.header'))
            ->description(__('admin.announcements.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new Announcement());

        $grid->column('anid', 'ANID');
        $grid->column('user', __('admin.announcements.user'))->display(function() {
            return $this->user->readable_name;
        }); ;
        $grid->column('title', __('admin.announcements.title'))->editable();
        $grid->column('created_at', __('admin.created_at'));
        $grid->column('updated_at', __('admin.updated_at'));

        $grid->filter(function(Grid\Filter $filter) {
            $filter->like('title', __('admin.announcements.title'));
            $filter->equal('uid', __('admin.announcements.user'))->select(function($id) {
                $user=User::find($id);
                if ($user) {
                    return [$user->id => $user->readable_name];
                }
            })->config('minimumInputLength', 4)->ajax(route('admin.api.users'));
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
        $show=new Show(Announcement::findOrFail($id));

        $show->field('anid', 'ANID');
        $show->field('user.name', __('admin.announcements.user'));
        $show->field('title', __('admin.announcements.title'));
        $show->field('content', __('admin.announcements.content'));
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
        $form=new Form(new Announcement());
        $form->text('title', __('admin.announcements.title'))->required();
        $form->simplemde('content', __('admin.announcements.content'))->help(__('admin.announcements.help.markdown'))->required();
        $form->select('uid', __('admin.announcements.user'))->options(function($id) {
            $user=User::find($id);
            if ($user) {
                return [$user->id => $user->readable_name];
            }
        })->config('minimumInputLength', 4)->ajax(route('admin.api.users'))->required();
        return $form;
    }
}
