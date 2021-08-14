<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use App\Models\Eloquent\OJ;
use App\Models\Eloquent\Problem;
use App\Models\Eloquent\Compiler;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use App\Babel\Babel;
use Arr;

class SettingsController extends Controller
{
    /**
     * Show the Testing Page.
     *
     * @return Response
     */
    public function index(Content $content)
    {
        $content=$content->header(__('admin.settings.index.header'));
        $content=$content->description(__('admin.settings.index.description'));
        if (request()->isMethod('post')) {
            $this->writeSettings();
            admin_toastr(__('admin.settings.tooltip.success.message'), 'success');
        }
        return $content->body($this->form());
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $box=new Box(__('admin.settings.form.header'));
        $box->style('success');
        $form=new Form();
        $form->simplemde('terms', __('admin.settings.form.terms'))->default(setting('terms'))->help(__('admin.settings.help.terms'));
        $form->method('POST');
        $form->action(route('admin.settings.index'));
        $form->disableReset();
        $box->content($form);
        return $box;
    }

    protected function writeSettings()
    {
        setting(['terms' => request()->terms]);
    }
}
