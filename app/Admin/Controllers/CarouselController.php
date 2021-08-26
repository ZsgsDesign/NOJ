<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Carousel;
use App\Models\JudgerModel;
use App\Models\Eloquent\OJ;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CarouselController extends AdminController
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
            ->header(__('admin.carousels.index.header'))
            ->description(__('admin.carousels.index.description'))
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
            ->header(__('admin.carousels.show.header'))
            ->description(__('admin.carousels.show.description'))
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
            ->header(__('admin.carousels.edit.header'))
            ->description(__('admin.carousels.edit.description'))
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
            ->header(__('admin.carousels.create.header'))
            ->description(__('admin.carousels.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new Carousel());

        $grid->model()->orderBy('updated_at', 'desc');

        $grid->column('caid', 'CAID');
        $grid->column('image', __('admin.carousels.image'))->display(function($url) {
            return '<img src="'.$url.'" style="max-width:200px;max-height:200px" class="img img-thumbnail">';
        });
        $grid->column('url', __('admin.carousels.url'))->editable();
        $grid->column('title', __('admin.carousels.title'))->editable();
        $grid->column('available', __('admin.carousels.availability'))->switch();
        $grid->column('created_at', __('admin.created_at'));
        $grid->column('updated_at', __('admin.updated_at'))->sortable();

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
        $show=new Show(Carousel::findOrFail($id));

        $show->field('caid', 'CAID');
        $show->field('image', __('admin.carousels.image'))->unescape()->as(function($url) {
            return '<img src="'.$url.'" style="max-width:200px;max-height:200px" class="img img-thumbnail">';
        });
        $show->field('url', __('admin.carousels.url'))->link();
        $show->field('title', __('admin.carousels.title'));
        $show->field('available', __('admin.carousels.availability'))->unescape()->as(function($available) {
            return $available ? '<i class="MDI check-circle wemd-teal-text"></i> '.__('admin.carousels.available') : '<i class="MDI close-circle wemd-pink-text"></i> '.__('admin.carousels.unavailable');
        });
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
        $form=new Form(new Carousel());

        $form->image('image', __('admin.carousels.image'))->uniqueName()->move("static/img/carousel")->required();

        $form->text('url', __('admin.carousels.url'))->icon('MDI link-variant')->required();
        $form->text('title', __('admin.carousels.title'))->icon('MDI format-title');
        $form->switch('available', __('admin.carousels.availability'))->default(true);

        return $form;
    }
}
