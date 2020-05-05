<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Submission as EloquentSubmissionModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SubmissionController extends Controller
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
            ->header('Submissions')
            ->description('all submissions')
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
            ->header('Submission Detail')
            ->description('the detail of submissions')
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
            ->header('Edit Submission')
            ->description('edit the detail of submissions')
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
            ->header('Create New Submission')
            ->description('create a new submission')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new EloquentSubmissionModel);
        $grid->column('sid', "ID")->sortable();
        $grid->time("Time");
        $grid->memory("Memory");
        $grid->verdict("Verdict")->display(function ($verdict) {
            return '<i class="fa fa-circle '.$this->color.'"></i> '.$verdict;
        });
        $grid->language("Language");
        $grid->submission_date("Submission Date")->display(function ($submission_date) {
            return date("Y-m-d H:i:s", $submission_date);
        });
        ;
        $grid->uid("UID");
        $grid->cid("CID");
        $grid->pid("PID");
        $grid->jid("JID");
        $grid->coid("COID");
        $grid->score("Raw Score");
        $grid->filter(function (Grid\Filter $filter) {
            $filter->column(6, function ($filter) {
                $filter->like('verdict');
            });
            $filter->column(6, function ($filter) {
                $filter->equal('cid', 'Contest ID');
                $filter->equal('uid', 'User ID');
                $filter->equal('pid', 'Problem ID');
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
        $show=new Show(EloquentSubmissionModel::findOrFail($id));
        $show->sid('SID');
        $show->time();
        $show->memory();
        $show->verdict();
        $show->color();
        $show->language();
        $show->submission_date();
        $show->remote_id();
        $this->codify($show->solution(), $show->getModel()->compiler->lang);
        if (!blank($show->getModel()->compile_info)) {
            $this->codify($show->compile_info());
        }
        $show->uid('UID');
        $show->pid('PID');
        $show->cid('CID');
        $show->jid('JID');
        $show->coid('COID');
        $show->vcid('VCID');
        $show->score();
        $show->share()->using(['No','Yes']);
        return $show;
    }

    private function codify($field, $lang=null)
    {
        $field->unescape()->as(function ($value) use ($field,$lang) {
            $field->border = false;
            $hash=md5($value);
            if (blank($value)) {
                $value=" ";
            }
            return "
                <style>
                #x$hash {
                    background: #ffffff;
                    border-top-left-radius: 0;
                    border-top-right-radius: 0;
                    border-bottom-right-radius: 3px;
                    border-bottom-left-radius: 3px;
                    padding: 10px;
                    border: 1px solid #d2d6de;
                }
                #x$hash code {
                    background: #ffffff;
                }
                </style>
                <pre id='x$hash'><code class='$lang'>".htmlspecialchars($value)."</code></pre>
                <script>
                    try{
                        hljs.highlightBlock(document.querySelector('#x$hash code'));
                    }catch(err){
                        window.addEventListener('load', function(){hljs.highlightBlock(document.querySelector('#x$hash code'));});
                    }
                </script>
            ";
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new EloquentSubmissionModel);
        $form->model()->makeVisible('password');
        $form->tab('Basic', function (Form $form) {
            $form->display('sid');
            $form->text('time')->rules('required');
            $form->text('memory')->rules('required');
            $form->text('verdict')->rules('required');
            $form->text('color')->rules('required');
            $form->text('language')->rules('required');
            $form->display('submission_date');
            $form->number('uid')->rules('required');
            $form->number('cid');
            $form->number('pid')->rules('required');
            $form->number('jid')->rules('required');
            $form->number('coid')->rules('required');
            $form->number('score')->rules('required');
        });
        return $form;
    }
}
